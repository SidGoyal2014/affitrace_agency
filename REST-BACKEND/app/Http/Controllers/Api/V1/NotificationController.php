<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notifications\ListNotificationsRequest;
use App\Http\Requests\Notifications\SendNotificationRequest;
use App\Http\Requests\Notifications\UpdateNotificationRequest;
use App\Http\Resources\NotificationRecipientResource;
use App\Http\Resources\NotificationResource;
use App\Models\AgencyNotification;
use App\Models\AgencyNotificationRecipient;
use App\Services\NotificationDispatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @group Notifications
 *
 * Endpoints for creating, dispatching, and consuming agency notifications.
 * All routes require auth:sanctum + api.tenant (applied at the route group level).
 */
class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationDispatchService $dispatchService
    ) {}

    // ─── Sender / Admin ────────────────────────────────────────────────────────

    /**
     * Create and dispatch a new notification.
     *
     * POST /api/v1/notifications
     */
    public function send(SendNotificationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Resolve agency identifier from the header set by ApiSetTenantConnection middleware.
        // This is the tenant DB name / agency username used across all agency-scoped records.
        $agencyId = (string) $request->header('X-Agency-ID', '');

        $notification = AgencyNotification::create([
            'agency_id'    => $agencyId,
            'sender_id'    => Auth::id(),
            'title'        => $validated['title'],
            // TODO: The migration column is named 'body' but the model fillable
            //       declares 'message'. Align the migration and model when possible.
            //       Using 'message' here to match the model's fillable definition.
            'body'      => $validated['message'],
            'type'         => $validated['type'],
            'category'     => $validated['category'],
            'priority'     => $validated['priority']     ?? 'normal',
            'target_role'  => $validated['target_role']  ?? 'all',
            'channel'      => $validated['channel']      ?? 'in-app',
            'is_active'    => $validated['is_active']    ?? true,
            'is_dispatched' => false,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'expires_at'   => $validated['expires_at']   ?? null,
            // TODO: 'data' column is in the model fillable but absent from the migration.
            //       Persist only when the column exists in the DB.
            // 'data'      => $validated['data'] ?? null,
        ]);

        /** @var array<int> $recipientIds */
        $recipientIds = $validated['recipient_ids'] ?? [];

        if (!empty($recipientIds)) {
            // Explicit recipient list takes highest priority.
            $this->dispatchService->send($notification, $recipientIds);
        } elseif (($notification->target_role ?? 'all') === 'all') {
            $this->dispatchService->broadcastToAll($notification);
        } else {
            $this->dispatchService->broadcastToRole($notification, $notification->target_role);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification created and dispatched successfully.',
            'data'    => new NotificationResource($notification->refresh()),
        ], 201);
    }

    /**
     * Paginated list of notifications sent within this agency.
     * Optionally filter by category and/or type.
     *
     * GET /api/v1/notifications/sent
     */
    public function sentIndex(ListNotificationsRequest $request): JsonResponse
    {
        $agencyId = (string) $request->header('X-Agency-ID', '');

        $query = AgencyNotification::where('agency_id', $agencyId)
            ->with('sender')
            ->withCount('recipients')
            ->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $items = $query->get();

        return response()->json([
            'success' => true,
            'data'    => NotificationResource::collection($items),
        ]);
    }

    /**
     * Show a single sent notification.
     *
     * GET /api/v1/notifications/sent/{id}
     */
    public function sentShow(int $id): JsonResponse
    {
        $agencyId = (string) request()->header('X-Agency-ID', '');

        $notification = AgencyNotification::where('agency_id', $agencyId)
            ->withCount('recipients')
            ->find($id);

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new NotificationResource($notification),
        ]);
    }

    /**
     * Update a notification — only permitted while it has not yet been dispatched.
     *
     * PUT /api/v1/notifications/sent/{id}
     */
    public function sentUpdate(UpdateNotificationRequest $request, int $id): JsonResponse
    {
        $agencyId = (string) $request->header('X-Agency-ID', '');

        $notification = AgencyNotification::where('agency_id', $agencyId)->find($id);

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
        }

        if ($notification->is_dispatched) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update a notification that has already been dispatched.',
            ], 422);
        }

        $notification->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Notification updated successfully.',
            'data'    => new NotificationResource($notification->refresh()),
        ]);
    }

    /**
     * Delete a sent notification record.
     *
     * DELETE /api/v1/notifications/sent/{id}
     */
    public function sentDestroy(int $id): JsonResponse
    {
        $agencyId = (string) request()->header('X-Agency-ID', '');

        $notification = AgencyNotification::where('agency_id', $agencyId)->find($id);

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully.',
        ]);
    }

    // ─── Recipient / Inbox ─────────────────────────────────────────────────────

    /**
     * Authenticated user's notification inbox — paginated.
     * Supports filtering by category, type, priority, and is_read.
     *
     * GET /api/v1/notifications/inbox
     */
    public function inbox(ListNotificationsRequest $request): JsonResponse
    {
        $userId  = Auth::id();
        $perPage = (int) $request->input('per_page', 15);
        $perPage = min($perPage, 100);

        $query = AgencyNotificationRecipient::where('recipient_id', $userId)
            ->with('notification')
            ->whereHas('notification', function ($q) use ($request) {
                if ($request->filled('category')) {
                    $q->where('category', $request->input('category'));
                }

                if ($request->filled('type')) {
                    $q->where('type', $request->input('type'));
                }

                if ($request->filled('priority')) {
                    $q->where('priority', $request->input('priority'));
                }

                if ($request->filled('from')) {
                    $q->whereDate('created_at', '>=', $request->input('from'));
                }

                if ($request->filled('to')) {
                    $q->whereDate('created_at', '<=', $request->input('to'));
                }



            })
            ->latest();

        if ($request->filled('is_read')) {
            $query->where('is_read', filter_var($request->input('is_read'), FILTER_VALIDATE_BOOLEAN));
        }

        $paginator = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => NotificationRecipientResource::collection($paginator->items()),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    /**
     * Show a single inbox item and mark it as read.
     *
     * GET /api/v1/notifications/inbox/{id}
     */
    public function inboxShow(int $id): JsonResponse
    {
        $userId = Auth::id();

        $recipient = AgencyNotificationRecipient::where('recipient_id', $userId)
            ->with('notification')
            ->find($id);

        if (!$recipient) {
            return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
        }

        // Auto-mark as read on open — only if not already read
        if (!$recipient->is_read && $recipient->notification) {
            $this->dispatchService->markAsRead($recipient->notification->id, $userId);
            $recipient->refresh();
        }

        return response()->json([
            'success' => true,
            'data'    => new NotificationRecipientResource($recipient),
        ]);
    }

    /**
     * Mark a specific inbox item as read.
     *
     * PATCH /api/v1/notifications/inbox/{id}/read
     */
    public function markRead(int $id): JsonResponse
    {
        $userId = Auth::id();

        $recipient = AgencyNotificationRecipient::where('recipient_id', $userId)->find($id);

        if (!$recipient) {
            return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
        }

        if ($recipient->notification_id ?? $recipient->agency_notification_id) {
            $notifId = $recipient->notification_id ?? $recipient->agency_notification_id;
            $this->dispatchService->markAsRead((int) $notifId, $userId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Marked as read.',
        ]);
    }

    /**
     * Mark all inbox notifications as read for the authenticated user.
     *
     * PATCH /api/v1/notifications/inbox/read-all
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $userId = Auth::id();

        // Bulk-update: set is_read = true and read_at = now() for all unread rows
        AgencyNotificationRecipient::where('recipient_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

    /**
     * Return total unread notification count for the authenticated user.
     *
     * GET /api/v1/notifications/unread-count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = $this->dispatchService->getUnreadCount((int) Auth::id());

        return response()->json([
            'success' => true,
            'data'    => ['unread_count' => $count],
        ]);
    }

    // ─── System Triggers ──────────────────────────────────────────────────────

    /**
     * Trigger a subscription-expiry notification.
     * Protected by X-Notification-Secret header (or use 'signed' middleware on the route).
     *
     * POST /api/v1/notifications/triggers/subscription-expiry
     */
    public function triggerSubscriptionExpiry(Request $request): JsonResponse
    {
        if (!$this->authoriseTrigger($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $agencyId = (string) $request->header('X-Agency-ID', '');

        // TODO: Accept target_user_id or subscription_id to scope the notification;
        //       for now, broadcasts to all users in the tenant.
        $notification = AgencyNotification::create([
            'agency_id'    => $agencyId,
            'sender_id'    => null, // System-generated
            'title'        => 'Subscription Expiring Soon',
            'message'      => $request->input('message', 'Your subscription is expiring soon. Please renew to avoid service interruption.'),
            'type'         => 'alert',
            'category'     => 'subscription',
            'priority'     => 'high',
            'target_role'  => 'all',
            'channel'      => 'in-app',
            'is_active'    => true,
            'is_dispatched' => false,
        ]);

        $this->dispatchService->broadcastToAll($notification);

        Log::info("triggerSubscriptionExpiry — notification {$notification->id} dispatched for agency {$agencyId}.");

        return response()->json([
            'success'         => true,
            'message'         => 'Subscription expiry notification dispatched.',
            'notification_id' => $notification->id,
        ], 201);
    }

    /**
     * Trigger a payment-reminder notification.
     * Protected by X-Notification-Secret header (or use 'signed' middleware on the route).
     *
     * POST /api/v1/notifications/triggers/payment-reminder
     */
    public function triggerPaymentReminder(Request $request): JsonResponse
    {
        if (!$this->authoriseTrigger($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $agencyId = (string) $request->header('X-Agency-ID', '');

        // TODO: Accept target_user_id or invoice_id for scoped reminders.
        $notification = AgencyNotification::create([
            'agency_id'    => $agencyId,
            'sender_id'    => null,
            'title'        => 'Payment Reminder',
            'message'      => $request->input('message', 'A payment is due. Please complete your payment to avoid disruption.'),
            'type'         => 'notification',
            'category'     => 'payment',
            'priority'     => 'high',
            'target_role'  => $request->input('target_role', 'all'),
            'channel'      => 'in-app',
            'is_active'    => true,
            'is_dispatched' => false,
        ]);

        $targetRole = $notification->target_role;

        if ($targetRole === 'all') {
            $this->dispatchService->broadcastToAll($notification);
        } else {
            $this->dispatchService->broadcastToRole($notification, $targetRole);
        }

        Log::info("triggerPaymentReminder — notification {$notification->id} dispatched for agency {$agencyId}.");

        return response()->json([
            'success'         => true,
            'message'         => 'Payment reminder notification dispatched.',
            'notification_id' => $notification->id,
        ], 201);
    }

    /**
     * Trigger a fully-custom system notification.
     * Protected by X-Notification-Secret header (or use 'signed' middleware on the route).
     *
     * POST /api/v1/notifications/triggers/custom
     */
    public function triggerCustom(Request $request): JsonResponse
    {
        if (!$this->authoriseTrigger($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'message'        => ['required', 'string'],
            'type'           => ['nullable', 'in:notification,news,alert,broadcast'],
            'category'       => ['nullable', 'in:offer,payment,transaction,subscription,general'],
            'priority'       => ['nullable', 'in:low,normal,high,urgent'],
            'target_role'    => ['nullable', 'in:affiliate,advertiser,employee,all'],
            'channel'        => ['nullable', 'in:in-app,email'],
            'recipient_ids'  => ['nullable', 'array'],
            'recipient_ids.*' => ['integer', 'min:1'],
        ]);

        $agencyId = (string) $request->header('X-Agency-ID', '');

        $notification = AgencyNotification::create([
            'agency_id'    => $agencyId,
            'sender_id'    => null,
            'title'        => $validated['title'],
            'message'      => $validated['message'],
            'type'         => $validated['type']        ?? 'notification',
            'category'     => $validated['category']    ?? 'general',
            'priority'     => $validated['priority']    ?? 'normal',
            'target_role'  => $validated['target_role'] ?? 'all',
            'channel'      => $validated['channel']     ?? 'in-app',
            'is_active'    => true,
            'is_dispatched' => false,
        ]);

        $recipientIds = $validated['recipient_ids'] ?? [];

        if (!empty($recipientIds)) {
            $this->dispatchService->send($notification, $recipientIds);
        } elseif (($notification->target_role ?? 'all') === 'all') {
            $this->dispatchService->broadcastToAll($notification);
        } else {
            $this->dispatchService->broadcastToRole($notification, $notification->target_role);
        }

        Log::info("triggerCustom — notification {$notification->id} dispatched for agency {$agencyId}.");

        return response()->json([
            'success'         => true,
            'message'         => 'Custom notification dispatched.',
            'notification_id' => $notification->id,
        ], 201);
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    /**
     * Validate the incoming trigger request using a shared secret header.
     *
     * The secret is read from NOTIFICATION_TRIGGER_SECRET in .env.
     * If the env var is not set, all trigger calls are denied as a safe default.
     *
     * Alternative: protect trigger routes with the 'signed' middleware alias
     * instead of (or in addition to) this header check.
     */
    private function authoriseTrigger(Request $request): bool
    {
        $secret = config('app.notification_trigger_secret', env('NOTIFICATION_TRIGGER_SECRET'));

        if (!$secret) {
            Log::warning('NotificationController: NOTIFICATION_TRIGGER_SECRET is not configured. Trigger endpoints are disabled.');
            return false;
        }

        return hash_equals((string) $secret, (string) $request->header('X-Notification-Secret', ''));
    }
}

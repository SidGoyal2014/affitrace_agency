<?php

namespace App\Services;

use App\Jobs\SendNotificationToRecipientJob;
use App\Models\AgencyNotification;
use App\Models\AgencyNotificationRecipient;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationDispatchService
{
    // ─── Core Dispatch ────────────────────────────────────────────────────────

    /**
     * Create recipient rows for each user ID and dispatch a job per recipient.
     *
     * Uses firstOrCreate to prevent duplicate recipient entries if called
     * multiple times for the same notification + user combination.
     *
     * @param  AgencyNotification  $notification
     * @param  array<int>          $recipientIds
     */
    public function send(AgencyNotification $notification, array $recipientIds): void
    {
        if (empty($recipientIds)) {
            Log::info("NotificationDispatchService::send — no recipients for notification {$notification->id}, skipping.");
            return;
        }

        foreach ($recipientIds as $userId) {
            $recipient = AgencyNotificationRecipient::firstOrCreate(
                [
                    'notification_id' => $notification->id,
                    'recipient_id'           => (int) $userId,
                ],
                [
                    'is_read'         => false,
                ]
            );

            SendNotificationToRecipientJob::dispatch($recipient)
                ->onQueue('notifications');
        }

        Log::info("NotificationDispatchService::send — dispatched " . count($recipientIds) . " job(s) for notification {$notification->id}.");
    }

    // ─── Broadcast Helpers ────────────────────────────────────────────────────

    /**
     * Broadcast a notification to all users that have the given role/user_type
     * within the current tenant database.
     *
     * @param  AgencyNotification  $notification
     * @param  string              $role  e.g. 'admin', 'affiliate', 'advertiser', 'employee'
     */
    public function broadcastToRole(AgencyNotification $notification, string $role): void
    {
        $recipientIds = $this->resolveByRole($role);
        $this->send($notification, $recipientIds);
    }

    /**
     * Broadcast a notification to every user in the current tenant database.
     *
     * In the multi-tenant architecture each agency has its own isolated database,
     * so querying User::all() is already scoped to the correct agency.
     *
     * @param  AgencyNotification  $notification
     */
    public function broadcastToAll(AgencyNotification $notification): void
    {
        // TODO: if users table ever gains soft-deletes, add ->whereNull('deleted_at') here
        $recipientIds = User::pluck('id')->toArray();
        $this->send($notification, $recipientIds);
    }

    // ─── Read / Unread ────────────────────────────────────────────────────────

    /**
     * Mark a specific notification as read for a user.
     *
     * Only updates rows where read_at is still null to avoid overwriting
     * the original read timestamp on repeated calls.
     *
     * @param  int  $notificationId
     * @param  int  $userId
     */
    public function markAsRead(int $notificationId, int $userId): void
    {
        AgencyNotificationRecipient::where('notification_id', $notificationId)
            ->where('recipient_id', $userId)
            ->whereNull('read_at')
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Return the total number of unread notifications for a user.
     *
     * @param  int  $userId
     * @return int
     */
    public function getUnreadCount(int $userId): int
    {
        return AgencyNotificationRecipient::where('recipient_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    // ─── Recipient Resolution ─────────────────────────────────────────────────

    /**
     * Derive the list of recipient user IDs from the notification's target_role field.
     *
     * - null / 'all'           → every user in the tenant
     * - any other string value → users whose user_type matches the value
     *
     * @param  AgencyNotification  $notification
     * @return array<int>
     */
    public function resolveRecipients(AgencyNotification $notification): array
    {
        $role = $notification->target_role;

        if (empty($role) || strtolower($role) === 'all') {
            // TODO: if users table ever gains soft-deletes, add ->whereNull('deleted_at') here
            return User::pluck('id')->toArray();
        }

        return $this->resolveByRole($role);
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    /**
     * Return IDs of all active users matching the given user_type/role.
     *
     * @param  string  $role
     * @return array<int>
     */
    private function resolveByRole(string $role): array
    {
        // TODO: filter by status 'active' once UserStatus enum values are confirmed
        return User::where('user_type', $role)
            ->pluck('id')
            ->toArray();
    }
}

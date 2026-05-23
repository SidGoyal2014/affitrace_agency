<?php

namespace App\Jobs;

use App\Mail\AgencyNotificationMail;
use App\Models\AgencyNotificationRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendNotificationToRecipientJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // ─── Queue Configuration ──────────────────────────────────────────────────

    /**
     * Maximum number of attempts before the job is marked as failed.
     */
    public int $tries = 3;

    /**
     * Seconds the job may run before timing out.
     */
    public int $timeout = 60;

    // ─── Payload ──────────────────────────────────────────────────────────────

    /**
     * Primary key of the AgencyNotificationRecipient row.
     *
     * We store the ID rather than serialising the full Eloquent model so that
     * the job always re-fetches the freshest state when it is processed.
     */
    protected int $recipientRowId;

    /**
     * Tenant database name captured at dispatch time.
     *
     * Mirrors the pattern used in GenerateReportExport: the worker process
     * may not have a tenant connection configured, so we re-establish it
     * inside handle() using this stored value.
     */
    protected ?string $tenantDatabase = null;

    // ─── Constructor ──────────────────────────────────────────────────────────

    /**
     * Create a new job instance.
     *
     * @param  AgencyNotificationRecipient  $recipient
     */
    public function __construct(AgencyNotificationRecipient $recipient)
    {
        $this->recipientRowId = $recipient->id;

        // Capture the tenant database name so we can restore it in the worker
        $tenantConfig = config('database.connections.tenant_db_con_runtime');
        if ($tenantConfig && !empty($tenantConfig['database'])) {
            $this->tenantDatabase = $tenantConfig['database'];
        }

        // Only pin the queue when not in sync/testing mode
        if (config('queue.default') !== 'sync') {
            $this->onQueue('notifications');
        }
    }

    // ─── Job Handler ──────────────────────────────────────────────────────────

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->restoreTenantConnection();

        /** @var AgencyNotificationRecipient|null $recipient */
        $recipient = AgencyNotificationRecipient::with([
            'notification',
            'recipient',
        ])->find($this->recipientRowId);

        if (!$recipient) {
            Log::warning("SendNotificationToRecipientJob: recipient row [{$this->recipientRowId}] not found. Aborting.");
            return;
        }

        $notification = $recipient->notification;

        // Skip if parent notification has been deactivated after the job was queued
        if (!$notification || !$notification->is_active) {
            Log::info("SendNotificationToRecipientJob: notification is inactive for recipient row [{$this->recipientRowId}]. Skipping.");
            return;
        }

        $channel = $notification->channel ?? 'in-app';

        try {
            match ($channel) {
                'email'  => $this->handleEmail($recipient),
                'in-app' => $this->handleInApp($recipient),
                default  => $this->handleInApp($recipient),
            };

            /*$recipient->update([
                'delivery_status' => 'delivered',
                'delivered_at'    => now(),
            ]);*/

        } catch (Throwable $e) {
            //$recipient->update(['delivery_status' => 'failed']);

            Log::error("SendNotificationToRecipientJob: delivery failed for recipient row [{$this->recipientRowId}].", [
                'channel' => $channel,
                'error'   => $e->getMessage(),
            ]);

            // Re-throw so the queue can apply retry / backoff logic
            throw $e;
        }
    }

    // ─── Channel Handlers ─────────────────────────────────────────────────────

    /**
     * Send the notification via email using AgencyNotificationMail.
     *
     * @param  AgencyNotificationRecipient  $recipient
     */
    private function handleEmail(AgencyNotificationRecipient $recipient): void
    {
        $user = $recipient->recipient;

        if (!$user || empty($user->email)) {
            Log::warning("SendNotificationToRecipientJob: no email address for user [{$recipient->recipient_id}]. Skipping email send.");
            return;
        }

        Mail::to($user->email)->send(new AgencyNotificationMail($recipient->notification));
    }

    /**
     * Handle in-app channel delivery.
     *
     * The AgencyNotificationRecipient row already exists at this point
     * (created by NotificationDispatchService::send), so delivery is
     * considered complete as soon as the row is persisted.
     *
     * TODO: trigger a WebSocket / Server-Sent Events push here once
     * real-time infrastructure is in place.
     *
     * @param  AgencyNotificationRecipient  $recipient
     */
    private function handleInApp(AgencyNotificationRecipient $recipient): void
    {
        // Intentionally a no-op: DB row creation == delivery for in-app channel.
    }

    // ─── Tenant Connection Restoration ────────────────────────────────────────

    /**
     * Re-establish the tenant database connection in the worker process.
     *
     * Mirrors the identical pattern used in GenerateReportExport so that
     * all queued jobs behave consistently across the codebase.
     */
    protected function restoreTenantConnection(): void
    {
        if (!$this->tenantDatabase) {
            return;
        }

        $pgsqlConfig = config('database.connections.pgsql');

        Config::set('database.connections.tenant_db_con_runtime', [
            'driver'         => 'pgsql',
            'host'           => $pgsqlConfig['host'] ?? '127.0.0.1',
            'port'           => $pgsqlConfig['port'] ?? 5432,
            'database'       => $this->tenantDatabase,
            'username'       => $pgsqlConfig['username'] ?? 'root',
            'password'       => $pgsqlConfig['password'] ?? '',
            'charset'        => 'utf8',
            'prefix'         => '',
            'prefix_indexes' => true,
            'search_path'    => 'public',
            'sslmode'        => 'disable',
        ]);

        DB::purge('tenant_db_con_runtime');
        DB::reconnect('tenant_db_con_runtime');
        DB::setDefaultConnection('tenant_db_con_runtime');

        Log::info("SendNotificationToRecipientJob: restored tenant connection for database [{$this->tenantDatabase}].");
    }

    // ─── Failure Hook ─────────────────────────────────────────────────────────

    /**
     * Called by the queue worker after all retry attempts are exhausted.
     *
     * @param  Throwable  $exception
     */
    public function failed(Throwable $exception): void
    {
        Log::error("SendNotificationToRecipientJob: permanently failed for recipient row [{$this->recipientRowId}].", [
            'error' => $exception->getMessage(),
        ]);

        // Best-effort status update — tenant connection may already be torn down
        try {
            $this->restoreTenantConnection();

            /*
            AgencyNotificationRecipient::where('id', $this->recipientRowId)
                ->update(['delivery_status' => 'failed']);
            */
        } catch (Throwable $inner) {
            //Log::error("SendNotificationToRecipientJob::failed — could not update delivery_status: " . $inner->getMessage());
            Log::error("SendNotificationToRecipientJob::failed : " . $inner->getMessage());
        }
    }
}

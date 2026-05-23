<?php

namespace App\Console\Commands;

use App\Models\AgencyNotification;
use App\Services\NotificationDispatchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionExpiryNotifier extends Command
{
    /**
     * Days-ahead thresholds for which reminders are sent.
     */
    private const DAYS_AHEAD = [7, 3, 1];

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:subscription-expiry';

    /**
     * The console command description.
     */
    protected $description = 'Auto-create and send subscription expiry reminders for subscriptions expiring in 1, 3, or 7 days';

    /**
     * Execute the console command.
     */
    public function handle(NotificationDispatchService $dispatchService): int
    {
        $this->info('Scanning subscriptions expiring soon...');

        $tenants = DB::connection('pgsql_main')->table('tenants')->get();

        if ($tenants->isEmpty()) {
            $this->warn('No tenants found in pgsql_main.tenants.');
            return Command::SUCCESS;
        }

        $totalSent = 0;

        foreach ($tenants as $tenant) {
            $this->info("Checking tenant: {$tenant->database_name}");

            $this->switchToTenantConnection($tenant->database_name);

            try {
                $totalSent += $this->processExpiryReminders($tenant->database_name, $dispatchService);
            } catch (\Throwable $e) {
                Log::error(
                    "SubscriptionExpiryNotifier: failed for tenant {$tenant->database_name}",
                    ['exception' => $e->getMessage()]
                );
                $this->error("  Failed for {$tenant->database_name}: " . $e->getMessage());
            }
        }

        $this->info("Done. Sent {$totalSent} expiry reminder notification(s) across all tenants.");

        return Command::SUCCESS;
    }

    /**
     * Check each day-ahead threshold and create one reminder notification per
     * threshold when matching active subscriptions exist in the tenant DB.
     *
     * @return int  Number of notifications created and dispatched for this tenant.
     */
    private function processExpiryReminders(
        string $agencyId,
        NotificationDispatchService $dispatchService
    ): int {
        $sent = 0;

        foreach (self::DAYS_AHEAD as $daysAhead) {
            $targetDate = now()->addDays($daysAhead)->toDateString();

            // Raw query on tenant_db_con_runtime to stay consistent with how
            // AffiliatePushData accesses per-tenant tables.
            // The subscriptions table has no agency_id column — per-tenant DB
            // isolation already scopes this to the correct agency.
            // TODO: If a future migration adds agency_id to subscriptions, add
            //       ->where('agency_id', ...) here for extra safety.
            $subscriptionCount = DB::table('subscriptions')
                ->whereDate('subscription_end_date', $targetDate)
                ->whereNotIn('status', ['Cancelled', 'Deleted'])
                ->count();

            if ($subscriptionCount === 0) {
                continue;
            }

            $pluralDays = $daysAhead > 1 ? 'days' : 'day';

            // TODO: sender_id is null for system-generated notifications. If the
            //       FK on agency_notifications.sender_id is NOT NULL, this will
            //       fail. Confirm the column is nullable in the migration before
            //       deploying to production.
            $notification = AgencyNotification::create([
                // agency_id == database_name per ApiSetTenantConnection middleware.
                'agency_id'      => $agencyId,
                'sender_id'      => null,
                'type'           => 'system',
                'category'       => 'subscription',
                'priority'       => $daysAhead === 1 ? 'high' : 'normal',
                'target_role'    => 'admin',
                'channel'        => 'in-app',
                'title'          => "Subscription Expiring In {$daysAhead} {$pluralDays}",
                'message'        => "Your agency subscription is expiring in {$daysAhead} {$pluralDays} "
                                  . "({$subscriptionCount} active subscription(s)). "
                                  . 'Please renew to avoid service interruption.',
                'is_active'      => true,
                'is_dispatched'  => false,
            ]);

            // Broadcast to all admins in this tenant DB.
            $dispatchService->broadcastToRole($notification, 'admin');
            $notification->update(['is_dispatched' => true]);

            $sent++;
            $this->info(
                "  Sent {$daysAhead}-{$pluralDays} expiry reminder for {$agencyId} "
                . "({$subscriptionCount} subscription(s) affected)."
            );
        }

        return $sent;
    }

    /**
     * Switch the default Eloquent/DB connection to the given tenant database.
     *
     * Mirrors ApiSetTenantConnection middleware exactly so that all Eloquent
     * model calls inside this iteration (AgencyNotification, User) resolve
     * against the correct tenant DB.
     */
    private function switchToTenantConnection(string $databaseName): void
    {
        Config::set('database.connections.tenant_db_con_runtime', [
            'driver'         => 'pgsql',
            'host'           => config('database.connections.pgsql.host'),
            'port'           => config('database.connections.pgsql.port'),
            'database'       => $databaseName,
            'username'       => config('database.connections.pgsql.username'),
            'password'       => config('database.connections.pgsql.password'),
            'charset'        => 'utf8',
            'prefix'         => '',
            'prefix_indexes' => true,
            'search_path'    => 'public',
            'sslmode'        => 'disable',
        ]);

        Config::set('database.default', 'tenant_db_con_runtime');
        Config::set('auth.providers.users.connection', 'tenant_db_con_runtime');

        DB::purge('tenant_db_con_runtime');
        DB::reconnect('tenant_db_con_runtime');
        DB::setDefaultConnection('tenant_db_con_runtime');
    }
}

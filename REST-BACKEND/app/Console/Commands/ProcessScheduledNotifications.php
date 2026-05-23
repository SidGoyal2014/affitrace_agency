<?php

namespace App\Console\Commands;

use App\Models\AgencyNotification;
use App\Services\NotificationDispatchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:process-scheduled';

    /**
     * The console command description.
     */
    protected $description = 'Dispatch all pending scheduled notifications whose scheduled_at time has passed';

    /**
     * Execute the console command.
     */
    public function handle(NotificationDispatchService $dispatchService): int
    {
        $this->info('Processing scheduled notifications...');

        $tenants = DB::connection('pgsql_main')->table('tenants')->get();

        if ($tenants->isEmpty()) {
            $this->warn('No tenants found in pgsql_main.tenants.');
            return Command::SUCCESS;
        }

        $totalDispatched = 0;

        foreach ($tenants as $tenant) {
            $this->info("Processing tenant: {$tenant->database_name}");

            $this->switchToTenantConnection($tenant->database_name);

            try {
                $pending = AgencyNotification::pendingDispatch()->get();

                if ($pending->isEmpty()) {
                    $this->line("  No pending notifications for {$tenant->database_name}.");
                    continue;
                }

                foreach ($pending as $notification) {
                    $this->dispatchSingle($notification, $dispatchService);
                    $notification->update(['is_dispatched' => true]);
                    $totalDispatched++;
                }

                $this->info("  Dispatched {$pending->count()} notification(s) for {$tenant->database_name}.");

            } catch (\Throwable $e) {
                Log::error(
                    "ProcessScheduledNotifications: failed for tenant {$tenant->database_name}",
                    ['exception' => $e->getMessage()]
                );
                $this->error("  Failed for {$tenant->database_name}: " . $e->getMessage());
            }
        }

        $this->info("Done. Total dispatched: {$totalDispatched}.");

        return Command::SUCCESS;
    }

    /**
     * Switch the default Eloquent/DB connection to the given tenant database.
     *
     * Mirrors ApiSetTenantConnection middleware exactly so that all Eloquent
     * model calls inside this iteration (AgencyNotification, User,
     * AgencyNotificationRecipient) resolve against the correct tenant DB.
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

    /**
     * Dispatch a single notification using the same target_role routing logic
     * as the NotificationController (send action).
     */
    private function dispatchSingle(
        AgencyNotification $notification,
        NotificationDispatchService $dispatchService
    ): void {
        $targetRole = $notification->target_role ?? 'all';

        if (strtolower($targetRole) === 'all') {
            $dispatchService->broadcastToAll($notification);
        } else {
            $dispatchService->broadcastToRole($notification, $targetRole);
        }
    }
}

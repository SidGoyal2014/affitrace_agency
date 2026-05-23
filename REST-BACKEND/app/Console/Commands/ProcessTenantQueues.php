<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Throwable;

class ProcessTenantQueues extends Command
{
    protected $signature = 'tenants:queue:work
                            {--queue=reports : Queue name to process}
                            {--sleep=3 : Seconds to sleep when no jobs are found}';

    protected $description = 'Process queued jobs from all tenant databases';

    public function handle(): void
    {
        $queueName = $this->option('queue');
        $sleep     = (int) $this->option('sleep');

        $this->info("Multi-tenant queue worker started. Queue: [{$queueName}]");

        while (true) {
            $processed = 0;

            try {
                $tenantDbs = DB::connection('pgsql_main')
                    ->table('users')
                    ->whereNotNull('username')
                    ->where('username', '!=', '')
                    ->distinct()
                    ->pluck('username');
            } catch (Throwable $e) {
                $this->error('Failed to fetch tenant list: ' . $e->getMessage());
                sleep($sleep);
                continue;
            }

            foreach ($tenantDbs as $dbName) {
                $connKey = 'queue_tenant_' . $dbName;

                // Register a DB connection for this tenant
                Config::set("database.connections.{$connKey}", array_merge(
                    config('database.connections.pgsql'),
                    ['database' => $dbName]
                ));

                // Register a queue connection using that DB connection
                Config::set("queue.connections.{$connKey}", [
                    'driver'       => 'database',
                    'connection'   => $connKey,
                    'table'        => 'jobs',
                    'queue'        => $queueName,
                    'retry_after'  => 90,
                    'after_commit' => false,
                ]);

                try {
                    $job = Queue::connection($connKey)->pop($queueName);

                    if ($job) {
                        $processed++;
                        $this->info("[{$dbName}] Processing job ID: {$job->getJobId()}");

                        $savedDefault = DB::getDefaultConnection();

                        try {
                            $job->fire();

                            // Delete the job if it didn't delete/release itself
                            if (!$job->isDeletedOrReleased()) {
                                $job->delete();
                            }

                            $this->info("[{$dbName}] Job completed: {$job->getJobId()}");

                        } catch (Throwable $e) {
                            $this->error("[{$dbName}] Job failed: {$e->getMessage()}");

                            if ($job->attempts() >= ($job->maxTries() ?? 3)) {
                                $job->fail($e);
                            } else {
                                $job->release(60);
                            }
                        } finally {
                            // Restore the default connection the job may have changed
                            DB::setDefaultConnection($savedDefault);
                            // Clean up the tenant connection the job created
                            try { DB::purge('tenant_db_con_runtime'); } catch (Throwable $e) {}
                        }
                    }

                } catch (Throwable $e) {
                    $this->error("[{$dbName}] Queue error: {$e->getMessage()}");
                } 
            }

            if ($processed === 0) {
                sleep($sleep);
            }
        }
    }
}
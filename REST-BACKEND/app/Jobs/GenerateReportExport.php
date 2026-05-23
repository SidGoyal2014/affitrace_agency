<?php

namespace App\Jobs;

use App\Models\ReportExport;
use App\Services\ReportExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateReportExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 900;

    /**
     * The export record ID.
     */
    protected int $exportId;

    /**
     * The tenant database name (for multi-tenant support).
     * Captured when job is dispatched, restored when job runs.
     */
    protected ?string $tenantDatabase = null;

    /**
     * Create a new job instance.
     */
    public function __construct(int $exportId)
    {
        $this->exportId = $exportId;

        // Capture tenant database context for multi-tenant support
        $tenantConfig = config('database.connections.tenant_db_con_runtime');
        if ($tenantConfig && !empty($tenantConfig['database'])) {
            $this->tenantDatabase = $tenantConfig['database'];
        }

        // Only set queue when not in sync mode
        if (config('queue.default') !== 'sync') {
            $this->onQueue(config('reports.queue', 'reports'));
        }
    }

    /**
     * Restore tenant database connection for multi-tenant support.
     */
    protected function restoreTenantConnection(): void
    {
        if (!$this->tenantDatabase) {
            return;
        }

        // Get connection settings from pgsql connection (works with cached config)
        $pgsqlConfig = config('database.connections.pgsql');

        // Reconfigure tenant connection with the stored database name
        Config::set('database.connections.tenant_db_con_runtime', [
            'driver' => 'pgsql',
            'host' => $pgsqlConfig['host'] ?? '127.0.0.1',
            'port' => $pgsqlConfig['port'] ?? 5432,
            'database' => $this->tenantDatabase,
            'username' => $pgsqlConfig['username'] ?? 'root',
            'password' => $pgsqlConfig['password'] ?? '',
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'disable',
        ]);

        DB::purge('tenant_db_con_runtime');
        DB::reconnect('tenant_db_con_runtime');
        DB::setDefaultConnection('tenant_db_con_runtime');

        Log::info("Restored tenant connection for database: {$this->tenantDatabase}");
    }

    /**
     * Execute the job.
     */
    public function handle(ReportExportService $service): void
    {
        // Restore tenant connection before processing (multi-tenant support)
        $this->restoreTenantConnection();

        $export = ReportExport::find($this->exportId);

        if (!$export) {
            Log::warning("ReportExport not found: {$this->exportId}");
            return;
        }

        // Check if already expired before processing
        if ($export->isExpired()) {
            $export->update(['status' => ReportExport::STATUS_EXPIRED]);
            return;
        }

        try {
            // Mark as processing
            $export->update(['status' => ReportExport::STATUS_PROCESSING]);

            /*
            // Generate the report based on format
            if ($export->format === 'csv') {
                $service->exportCSV($export);
            } else {
                $service->exportXLSX($export);
            }

            // Mark as ready
            $export->update(['status' => ReportExport::STATUS_READY]);
            */

            // Generate the report based on format — capture the returned file path
            if ($export->format === 'csv') {
                $filePath = $service->exportCSV($export);
            } else {
                $filePath = $service->exportXLSX($export);
            }

            Log::info("filePath in GenerateReportExport.php: " . $filePath);

            // Mark as ready and store file path for download
            $export->update([
                'status' => ReportExport::STATUS_READY,
                'file_path' => $filePath,
            ]);



            Log::info("Report export completed: {$this->exportId}");

        } catch (Throwable $e) {
            Log::error("Report export failed: {$this->exportId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $export->update([
                'status' => ReportExport::STATUS_FAILED,
                'error' => $e->getMessage(),
            ]);

            throw $e; // Re-throw for retry mechanism
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(Throwable $exception): void
    {
        // Restore tenant connection for failure handling (multi-tenant support)
        $this->restoreTenantConnection();

        $export = ReportExport::find($this->exportId);

        if ($export) {
            $export->update([
                'status' => ReportExport::STATUS_FAILED,
                'error' => $exception->getMessage(),
            ]);
        }

        Log::error("Report export job failed permanently: {$this->exportId}", [
            'error' => $exception->getMessage(),
        ]);
    }
}

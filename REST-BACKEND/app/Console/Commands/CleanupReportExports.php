<?php

namespace App\Console\Commands;

use App\Models\ReportExport;
use App\Services\ReportExportService;
use Illuminate\Console\Command;

class CleanupReportExports extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reports:cleanup';

    /**
     * The console command description.
     */
    protected $description = 'Delete expired report exports and their temp files';

    /**
     * Execute the console command.
     */
    public function handle(ReportExportService $service): int
    {
        $this->info('Starting report exports cleanup...');

        // Mark expired exports
        $markedExpired = ReportExport::where('expires_at', '<', now())
            ->where('status', '!=', ReportExport::STATUS_EXPIRED)
            ->update(['status' => ReportExport::STATUS_EXPIRED]);

        if ($markedExpired > 0) {
            $this->info("Marked {$markedExpired} exports as expired.");
        }

        // Cleanup expired records and files
        $deleted = $service->cleanupExpired();

        if ($deleted > 0) {
            $this->info("Deleted {$deleted} expired exports and their files.");
        } else {
            $this->info('No expired exports to clean up.');
        }

        return Command::SUCCESS;
    }
}

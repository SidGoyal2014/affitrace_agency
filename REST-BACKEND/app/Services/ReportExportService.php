<?php

namespace App\Services;

use App\Models\ReportExport;
use App\Models\OfferTracking;
use App\Models\Conversion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReportExportService
{
    /**
     * Build query based on report type and filters.
     */
    public function buildQuery(string $reportType, array $filters): Builder
    {
        // Clicks from offer_trackings, conversions from dedicated conversions table
        if ($reportType === 'conversions') {
            $query = Conversion::query();

            // Apply filters with conversions table column names
            if (!empty($filters['offer_id'])) {
                $query->where('offer_id', $filters['offer_id']);
            }

            if (!empty($filters['affiliate_id'])) {
                $query->where('affiliate_id', $filters['affiliate_id']);
            }

            if (!empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            return $query;
        }

        // Clicks and other reports from offer_trackings
        $query = match ($reportType) {
            'clicks' => OfferTracking::query(),
            'all_trackings' => OfferTracking::query(),
            default => OfferTracking::query(),
        };

        // Apply common filters for offer_trackings
        if (!empty($filters['offer_id'])) {
            $query->where('offer_id', $filters['offer_id']);
        }

        if (!empty($filters['affiliate_id'])) {
            $query->where('aff_id', $filters['affiliate_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    /**
     * Get the temporary file path for an export.
     */
    public function getTempFilePath(ReportExport $export): string
    {
        $tempPath = config('reports.temp_path');

        Log::info($tempPath);

        // Ensure directory exists
        if (!File::isDirectory($tempPath)) {
            File::makeDirectory($tempPath, 0755, true);
        }

        $extension = $export->format;
        $filename = $export->id . '_' . Str::uuid() . '.' . $extension;

        return $tempPath . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Export data to CSV using chunked streaming.
     */
    public function exportCSV(ReportExport $export): string
    {
        $query = $this->buildQuery($export->report_type, $export->filters ?? []);
        $filePath = $this->getTempFilePath($export);
        
        Log::info("filePath in ReportExportService.php: " . $filePath);

        $columns = $export->columns ?? $this->getDefaultColumns($export->report_type);

        $selectColumns = $columns;
        if ($export->report_type === 'conversions') {
            $selectColumns = array_unique(array_merge($columns, ['id', 'created_at', 'conversion_time', 'click_time', 'click_ip', 'conversion_ip', 'ip', 'tid']));
            $query->with('click');
        }

        $user = \App\Models\User::find($export->user_id);

        // Open file for writing
        $handle = fopen($filePath, 'w');

        if ($handle === false) {
            throw new \RuntimeException("Cannot create temp file: {$filePath}");
        }

        try {
            // Write header row
            fputcsv($handle, $this->formatHeaders($columns));

            // Stream data using chunked queries - NEVER load all at once
            $query->select($selectColumns)->chunkById(1000, function ($rows) use ($handle, $columns, $user, $export) {
                foreach ($rows as $row) {
                    fputcsv($handle, $this->formatRow($row, $columns, $user, $export->report_type));
                }
            });

        } finally {
            fclose($handle);
        }

        return $filePath;
    }

    /**
     * Export data to XLSX using streaming.
     * Uses OpenSpout for memory-efficient streaming.
     */
    public function exportXLSX(ReportExport $export): string
    {
        $query = $this->buildQuery($export->report_type, $export->filters ?? []);
        $filePath = $this->getTempFilePath($export);
        $columns = $export->columns ?? $this->getDefaultColumns($export->report_type);

        // Check if OpenSpout is available
        if (!class_exists(\OpenSpout\Writer\XLSX\Writer::class)) {
            // Fallback to CSV if XLSX library not installed
            return $this->exportCSV($export);
        }

        $selectColumns = $columns;
        if ($export->report_type === 'conversions') {
            $selectColumns = array_unique(array_merge($columns, ['id', 'created_at', 'conversion_time', 'click_time', 'click_ip', 'conversion_ip', 'ip', 'tid']));
            $query->with('click');
        }

        $user = \App\Models\User::find($export->user_id);

        $writer = new \OpenSpout\Writer\XLSX\Writer();
        $writer->openToFile($filePath);

        try {
            // Write header row
            $headerRow = \OpenSpout\Common\Entity\Row::fromValues($this->formatHeaders($columns));
            $writer->addRow($headerRow);

            // Stream data using chunked queries
            $query->select($selectColumns)->chunkById(1000, function ($rows) use ($writer, $columns, $user, $export) {
                foreach ($rows as $row) {
                    $dataRow = \OpenSpout\Common\Entity\Row::fromValues($this->formatRow($row, $columns, $user, $export->report_type));
                    $writer->addRow($dataRow);
                }
            });

        } finally {
            $writer->close();
        }

        return $filePath;
    }

    /**
     * Get default columns for a report type.
     */
    protected function getDefaultColumns(string $reportType): array
    {
        return match ($reportType) {
            'clicks' => ['id', 'offer_id', 'aff_id', 'offer_name', 'ip', 'country', 'os', 'created_at'],
            'conversions' => ['id', 'offer_id', 'affiliate_id', 'offer_name', 'country', 'revenue', 'payout', 'created_at'],
            default => ['id', 'offer_id', 'aff_id', 'offer_name', 'tid', 'country', 'created_at'],
        };
    }

    /**
     * Format column headers for display.
     */
    protected function formatHeaders(array $columns): array
    {
        return array_map(function ($col) {
            return ucwords(str_replace('_', ' ', $col));
        }, $columns);
    }

    /**
     * Format a row for export.
     */
    protected function formatRow($row, array $columns, $user = null, $reportType = ''): array
    {
        // Apply masking to the Eloquent model / row
        if ($reportType === 'conversions') {
            $userType = $user ? $user->user_type : 'guest';
            $isObserver = $user ? $user->isObserver() : false;

            if ($userType === 'affiliate') {
                $row->advertiser_id = null;
                $row->advertiser_name = 'Advertiser';
                $row->advertiser_price = 0;
                $row->revenue = 0;
                $row->revenue_usd = 0;
                $row->profit = 0;
                $row->margin_pct = 0;
                $row->adv_sub1 = null; $row->adv_sub2 = null; $row->adv_sub3 = null; $row->adv_sub4 = null; $row->adv_sub5 = null;
                $row->adv_sub6 = null; $row->adv_sub7 = null; $row->adv_sub8 = null; $row->adv_sub9 = null; $row->adv_sub10 = null;
                $row->landing_page_id = null;
                $row->landing_page_url = null;
                $row->tracking_url = null;
                $row->redirect_url = null;
                $row->rejection_reason = null;
            } elseif ($userType === 'advertiser') {
                $row->affiliate_id = null;
                $row->affiliate_name = 'Affiliate';
                $row->affiliate_price = 0;
                $row->payout = 0;
                $row->payout_usd = 0;
                $row->profit = 0;
                $row->margin_pct = 0;
                $row->aff_sub1 = null; $row->aff_sub2 = null; $row->aff_sub3 = null; $row->aff_sub4 = null; $row->aff_sub5 = null;
                $row->aff_sub6 = null; $row->aff_sub7 = null; $row->aff_sub8 = null; $row->aff_sub9 = null; $row->aff_sub10 = null;
                $row->postback_status = null;
                $row->postback_fired_at = null;
                $row->postback_received_at = null;
                $row->postback_url_fired = null;
                $row->postback_http_code = null;
                $row->postback_attempts = 0;
            }

            if ($isObserver) {
                $row->revenue = 0;
                $row->payout = 0;
                $row->revenue_usd = 0;
                $row->payout_usd = 0;
                $row->profit = 0;
                $row->sale = 0;
                $row->affiliate_price = 0;
                $row->advertiser_price = 0;
            }
        } elseif ($reportType === 'clicks') {
            $userType = $user ? $user->user_type : 'guest';
            $isObserver = $user ? $user->isObserver() : false;

            if ($userType === 'affiliate') {
                $row->adv_id = null;
                $row->advert_name = 'Advertiser';
                $row->revenue = 0;
                $row->revenue_usd = 0;
            } elseif ($userType === 'advertiser') {
                $row->aff_id = null;
                $row->aff_name = 'Affiliate';
                $row->payout = 0;
                $row->payout_usd = 0;
            }

            if ($isObserver) {
                $row->revenue = 0;
                $row->payout = 0;
                $row->revenue_usd = 0;
                $row->payout_usd = 0;
            }
        }

        $data = [];
        foreach ($columns as $column) {
            $value = $row->{$column} ?? '';

            // Format datetime values
            if ($value instanceof \DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            }

            $data[] = $value;
        }
        return $data;
    }

    /**
     * Check if temp file exists for an export.
     */
    public function tempFileExists(ReportExport $export): bool
    {
        $pattern = config('reports.temp_path') . DIRECTORY_SEPARATOR . $export->id . '_*.' . $export->format;
        $files = glob($pattern);
        return !empty($files);
    }

    /**
     * Get the temp file path if it exists.
     */
    public function getExistingTempFile(ReportExport $export): ?string
    {
        $pattern = config('reports.temp_path') . DIRECTORY_SEPARATOR . $export->id . '_*.' . $export->format;
        $files = glob($pattern);
        return $files[0] ?? null;
    }

    /**
     * Delete temp file for an export.
     */
    public function deleteTempFile(ReportExport $export): void
    {
        $file = $this->getExistingTempFile($export);
        if ($file && File::exists($file)) {
            File::delete($file);
        }
    }

    /**
     * Cleanup all expired exports and their temp files.
     */
    public function cleanupExpired(): int
    {
        $deleted = 0;

        // Get all expired exports
        $expiredExports = ReportExport::where('expires_at', '<', now())
            ->orWhere('status', \App\Enums\ReportExportStatus::Expired->value)
            ->get();

        foreach ($expiredExports as $export) {
            // Delete temp file first
            $this->deleteTempFile($export);

            // Delete the record
            $export->delete();
            $deleted++;
        }

        // Also cleanup any orphaned temp files older than TTL
        $this->cleanupOrphanedFiles();

        return $deleted;
    }

    /**
     * Delete orphaned temp files that don't have matching DB records.
     */
    protected function cleanupOrphanedFiles(): void
    {
        $tempPath = config('reports.temp_path');

        if (!File::isDirectory($tempPath)) {
            return;
        }

        $ttlMinutes = config('reports.export_ttl_minutes', 10);
        $cutoffTime = now()->subMinutes($ttlMinutes)->getTimestamp();

        $files = File::files($tempPath);

        foreach ($files as $file) {
            if ($file->getMTime() < $cutoffTime) {
                File::delete($file->getPathname());
            }
        }
    }
}

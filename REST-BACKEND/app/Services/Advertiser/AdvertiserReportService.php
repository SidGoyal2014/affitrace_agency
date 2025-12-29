<?php

namespace App\Services\Advertiser;

use Illuminate\Support\Facades\DB;
use App\Services\Reports\BaseReportService;
use App\DTOs\ReportFilterDTO;

class AdvertiserReportService extends BaseReportService
{
    /**
     * Get summary report for advertiser (wrapper method for backward compatibility)
     * 
     * @param int $advertiser_id
     * @param ReportFilterDTO|null $filters
     * @return array
     */
    public function getSummaryReportForAdvertiser($advertiser_id, ?ReportFilterDTO $filters = null): array
    {
        // If no filters provided, use empty filters for backward compatibility
        if (!$filters) {
            $filters = new ReportFilterDTO();
        }

        // Use base class implementation with advertiser scoping
        return parent::getSummaryReport($filters, $advertiser_id, 'advertiser');
    }

    /**
     * Get detailed click and conversion report for advertiser (wrapper method)
     * 
     * @param int $advertiser_id
     * @param ReportFilterDTO $filters
     * @return array
     */
    public function getClickConversionReportForAdvertiser($advertiser_id, ReportFilterDTO $filters): array
    {
        // Use base class implementation with advertiser scoping
        return parent::getClickConversionReport($filters, $advertiser_id, 'advertiser');
    }

    /**
     * Get invoices for advertiser
     * 
     * @param int $advertiser_id
     * @return array
     */
    public function getInvoices($advertiser_id)
    {
        $invoices = DB::table('invoices')
            ->where('advertiser_id', $advertiser_id)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'module_name' => 'Invoices',
            'invoices' => $invoices
        ];
    }
}


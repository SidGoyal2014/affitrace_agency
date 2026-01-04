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

    /**
     * Get clicks report for advertiser (from offer_trackings)
     */
    public function getClicksReport($request, $advertiser_id): array
    {
        $query = DB::table('offer_trackings')
            ->select(
                'offer_trackings.*',
                'offer_trackings.id as otid',
                'offer_trackings.created_at as link_clicked_at',
                'offer_trackings.country as ot_country',
                'offer_trackings.os as ot_os',
                'offer_trackings.browser as ot_browser',
                'affis.id as aff_id',
                'affis.name as aff_name',
                'offers.id as offer_id',
                'offers.offer_name',
                'devices.device_name'
            )
            ->leftJoin('offers', 'offer_trackings.offer_id', '=', 'offers.id')
            ->leftJoin('devices', 'offer_trackings.device_id', '=', 'devices.id')
            ->leftJoin('users as affis', 'offer_trackings.aff_id', '=', 'affis.id')
            ->where('offers.advertiser_id', $advertiser_id);

        if ($request->from && $request->to) {
            $query->whereBetween('offer_trackings.created_at', [$request->from, $request->to]);
        }
        if ($request->offer_id) {
            $query->where('offers.id', $request->offer_id);
        }

        $limit = $request->limit ?? 50;
        $results = $query->orderBy('offer_trackings.id', 'desc')->paginate($limit);

        return [
            'module_name' => 'Clicks Report',
            'data' => $results,
            'filters' => $request->all()
        ];
    }

    /**
     * Get conversions report for advertiser (from conversions table)
     */
    public function getConversionsReport($request, $advertiser_id): array
    {
        $query = DB::table('conversions')
            ->select(
                'conversions.*',
                'conversions.id as conv_id',
                'conversions.created_at as conversion_time',
                'conversions.country',
                'conversions.device',
                'conversions.event',
                'conversions.revenue',
                'affis.id as affiliate_id',
                'affis.name as affiliate_name',
                'offers.id as offer_id',
                'offers.offer_name'
            )
            ->leftJoin('offers', 'conversions.offer_id', '=', 'offers.id')
            ->leftJoin('users as affis', 'conversions.affiliate_id', '=', 'affis.id')
            ->where('conversions.advertiser_id', $advertiser_id);

        if ($request->from && $request->to) {
            $query->whereBetween('conversions.created_at', [$request->from, $request->to]);
        }
        if ($request->offer_id) {
            $query->where('conversions.offer_id', $request->offer_id);
        }

        $limit = $request->limit ?? 50;
        $results = $query->orderBy('conversions.id', 'desc')->paginate($limit);

        return [
            'module_name' => 'Conversions Report',
            'data' => $results,
            'filters' => $request->all()
        ];
    }
}


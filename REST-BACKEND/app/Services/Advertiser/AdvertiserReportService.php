<?php

namespace App\Services\Advertiser;

use Illuminate\Support\Facades\DB;

class AdvertiserReportService
{
    public function getSummaryReport($advertiser_id)
    {
        // Get summary stats
        $sql = "SELECT 
                    DATE(ot.created_at) as date,
                    COUNT(CASE WHEN ot.event = 'click' THEN 1 END) as clicks,
                    COUNT(CASE WHEN ot.event != 'click' THEN 1 END) as conversions,
                    SUM(COALESCE(ot.revenue_usd, 0)) as cost
                FROM offer_trackings ot
                INNER JOIN offers o ON ot.offer_id = o.id
                WHERE o.advertiser_id = " . $advertiser_id . "
                GROUP BY DATE(ot.created_at)
                ORDER BY DATE(ot.created_at) DESC
                LIMIT 30";

        $stats = DB::select($sql);

        return [
            'module_name' => 'Summary Report',
            'stats' => $stats
        ];
    }

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

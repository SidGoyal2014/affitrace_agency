<?php

namespace App\Http\Controllers\Advertiser;

use DB, Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdvertiserReportController extends Controller
{

    public function summary(Request $request)
    {

        try {

            $advertiser_id = Auth::id();

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

            return response()->json([
                'module_name' => 'Summary Report',
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }


    public function invoices(Request $request)
    {

        try {

            $advertiser_id = Auth::id();

            // Placeholder - invoices table may not exist yet
            // Return empty array for now
            $invoices = [];

            return response()->json([
                'module_name' => 'Invoices',
                'invoices' => $invoices
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

}

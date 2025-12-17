<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\OfferTracking;
use App\Models\OfferAffiliate;

class AffiliateDashboardService
{
    /**
     * Get category wise revenue for the logged-in affiliate.
     */
    /**
     * Get category wise revenue for the logged-in affiliate.
     */
    public function get_category_wise_revenue()
    {
        $sql = "SELECT 
                    MONTH(o.created_at) AS month,
                    c.category_name,
                    SUM(ot.payout_usd) AS revenue
                FROM 
                    offer_trackings ot
                JOIN 
                    offers o ON ot.offer_id = o.id
                JOIN 
                    categories c ON o.category_id = c.id
                WHERE
                    ot.aff_id = " . Auth::id() . "
                    AND ot.aff_click_id IS NOT NULL
                    AND ot.tid IS NOT NULL
                    AND ot.aff_click_id = ot.tid
                GROUP BY 
                    MONTH(o.created_at), c.category_name
                ORDER BY 
                    MONTH(o.created_at), c.category_name
                LIMIT 0,5";

        return DB::select($sql);
    }

    /**
     * Get monthly revenue for charts.
     */
    public function get_monthly_revenue()
    {
        $arrSTR = array();

        $sql = "SELECT sum(payout_usd) as revenue, month(created_at) as month FROM offer_trackings where aff_id = " . Auth::id() . " AND aff_click_id IS NOT NULL AND tid IS NOT NULL AND aff_click_id = tid group by month(created_at); ";

        $arr = DB::select($sql);

        foreach ($arr as $obj) {
            $month = (trim($obj->month) == "") ? 0 : $obj->month;
            $arrSTR[$month] = $obj->revenue;
        }

        for ($index = 1; $index <= 12; $index++) {
            if (!isset($arrSTR[$index])) {
                $arrSTR[$index] = 0;
            }
        }

        ksort($arrSTR, SORT_NUMERIC);

        return '[' . implode(',', $arrSTR) . ']';
    }

    /**
     * Get IDs of offers assigned to the affiliate.
     */
    public function get_affiliate_offers()
    {
        $sql = "SELECT 
                    GROUP_CONCAT( offer_id ) AS all_offer_ids
                FROM 
                    offer_affiliates 
                WHERE
                    affiliate_id = " . Auth::id();

        $data = DB::select($sql);
        return $data[0] ?? null;
    }

    /**
     * Get detailed list of assigned offers.
     */
    public function get_offers_assigned()
    {
        try {
            $sql = "SELECT 
                        offers.*,
                        categories.category_name,
                        offers.id as oid 
                    FROM 
                        offers 
                    INNER JOIN 
                        offer_affiliates 
                    ON 
                        offers.id = offer_affiliates.offer_id 
                    LEFT JOIN 
                        categories 
                    ON 
                        offers.category_id = categories.id
                    WHERE 
                        offer_affiliates.affiliate_id = " . Auth::id();

            return DB::select($sql);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get top top 5 affiliates (Global/System wide logic or specific logic? 
     * Based on original controller, this query seems to get top affiliates system-wide 
     * but only returns 5. This might be odd for an affiliate dash but keeping logic same).
     * 
     * WAIT: The original query groups by OT.aff_id.
     * If this is the Affiliate Dashboard, why show other top affiliates?
     * Ah, it seems it might be a leaderboard. Keeping logic EXACTLY as is.
     */
    public function top_affiliates($dod = "Y")
    {
        // Always return data
        $strLimit = " LIMIT 0,5";
        $sql = "SELECT U.id as uid, OT.aff_id, U.name, sum(OT.payout) as payout FROM  users U INNER JOIN offer_trackings OT ON U.id=OT.aff_id GROUP BY OT.aff_id, U.id, U.name ORDER BY payout DESC" . $strLimit;
        return DB::select($sql);
    }

    /**
     * Get top 5 offers by revenue.
     */
    public function top_offers($dod = "Y")
    {
        // Always return data
        $sql = "SELECT OT.offer_id, O.offer_name, sum(OT.revenue) as revenue, O.offer_status FROM  offers O INNER JOIN offer_trackings OT ON O.id=OT.offer_id GROUP BY OT.offer_id, O.offer_name, O.offer_status ORDER BY revenue DESC LIMIT 0,5";
        return DB::select($sql);
    }

    // --- Statistics Methods ---

    public function get_total_clicks()
    {
        // 'aff_click_id' = 'tid' implies the initial click record (not a postback/duplicate)
        // Correct logic: Count distinct TIDs (clicks)
        return OfferTracking::where('aff_id', Auth::id())
            ->distinct('tid')
            ->count('tid');
    }

    public function get_total_conversions()
    {
        // Conversions are where aff_click_id matches tid (click_id)
        return OfferTracking::where('aff_id', Auth::id())
            ->whereNotNull('aff_click_id')
            ->whereNotNull('tid')
            ->whereColumn('aff_click_id', 'tid')
            ->count();
    }

    public function get_total_earnings()
    {
        // Earnings should only be calculated for valid conversions
        $total = OfferTracking::where('aff_id', Auth::id())
            ->whereNotNull('aff_click_id')
            ->whereNotNull('tid')
            ->whereColumn('aff_click_id', 'tid')
            ->sum('payout_usd');
        return number_format((float) $total, 2, '.', '');
    }

    public function get_today_earnings()
    {
        $total = OfferTracking::where('aff_id', Auth::id())
            ->whereNotNull('aff_click_id')
            ->whereNotNull('tid')
            ->whereColumn('aff_click_id', 'tid')
            ->whereDate('created_at', date('Y-m-d'))
            ->sum('payout_usd');
        return number_format((float) $total, 2, '.', '');
    }

    public function get_month_earnings()
    {
        $total = OfferTracking::where('aff_id', Auth::id())
            ->whereNotNull('aff_click_id')
            ->whereNotNull('tid')
            ->whereColumn('aff_click_id', 'tid')
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->sum('payout_usd');
        return number_format((float) $total, 2, '.', '');
    }
}

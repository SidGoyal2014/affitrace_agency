<?php

namespace App\Services\Affiliate;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\OfferTracking;
use App\Models\Conversion;
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
                    EXTRACT(MONTH FROM o.created_at) AS month,
                    c.category_name,
                    SUM(conv.payout_usd) AS revenue
                FROM 
                    conversions conv
                JOIN 
                    offers o ON conv.offer_id = o.id
                JOIN 
                    categories c ON o.category_id = c.id
                WHERE
                    conv.affiliate_id = " . Auth::id() . "
                GROUP BY 
                    EXTRACT(MONTH FROM o.created_at), c.category_name
                ORDER BY 
                    EXTRACT(MONTH FROM o.created_at), c.category_name
                LIMIT 5 OFFSET 0";

        return DB::select($sql);
    }

    /**
     * Get monthly revenue for charts.
     */
    public function get_monthly_revenue()
    {
        $arrSTR = array();

        $sql = "SELECT sum(payout_usd) as revenue, EXTRACT(MONTH FROM created_at) as month FROM conversions where affiliate_id = " . Auth::id() . " group by EXTRACT(MONTH FROM created_at)";

        $arr = DB::select($sql);

        foreach ($arr as $obj) {
            $month = (trim($obj->month) == "") ? 0 : $obj->month;
            $arrSTR[$month] = \App\Utils\CurrencyUtils::convertFromUsdToDefault($obj->revenue);
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
                    STRING_AGG(CAST(offer_id AS TEXT), ',') AS all_offer_ids
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

            $offers = DB::select($sql);
            foreach ($offers as $offer) {
                $offer->payout_usd = \App\Utils\CurrencyUtils::convertFromUsdToDefault($offer->payout_usd);
                $offer->revenue_usd = \App\Utils\CurrencyUtils::convertFromUsdToDefault($offer->revenue_usd);
                $offer->offer_price_usd = $offer->payout_usd; // Compatibility mapping for client-side
            }
            return $offers;
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
        $strLimit = " LIMIT 5 OFFSET 0";
        $sql = "SELECT U.id as uid, C.affiliate_id as aff_id, U.name, sum(C.payout_usd) as payout FROM users U INNER JOIN conversions C ON U.id=C.affiliate_id GROUP BY C.affiliate_id, U.id, U.name ORDER BY payout DESC" . $strLimit;
        $data = DB::select($sql);
        foreach ($data as $row) {
            $row->payout = \App\Utils\CurrencyUtils::convertFromUsdToDefault($row->payout);
        }
        return $data;
    }

    /**
     * Get top 5 offers by revenue.
     */
    public function top_offers($dod = "Y")
    {
        // Always return data
        $sql = "SELECT C.offer_id, O.offer_name, sum(C.revenue_usd) as revenue, O.offer_status FROM offers O INNER JOIN conversions C ON O.id=C.offer_id GROUP BY C.offer_id, O.offer_name, O.offer_status ORDER BY revenue DESC LIMIT 5 OFFSET 0";
        $data = DB::select($sql);
        foreach ($data as $row) {
            $row->revenue = \App\Utils\CurrencyUtils::convertFromUsdToDefault($row->revenue);
        }
        return $data;
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
        // Conversions now from dedicated conversions table
        return Conversion::where('affiliate_id', Auth::id())->count();
    }

    public function get_total_earnings()
    {
        // Earnings from conversions table
        $total = Conversion::where('affiliate_id', Auth::id())
            ->sum('payout_usd');
        $converted = \App\Utils\CurrencyUtils::convertFromUsdToDefault($total);
        return number_format((float) $converted, 2, '.', '');
    }

    public function get_today_earnings()
    {
        $total = Conversion::where('affiliate_id', Auth::id())
            ->whereDate('created_at', date('Y-m-d'))
            ->sum('payout_usd');
        $converted = \App\Utils\CurrencyUtils::convertFromUsdToDefault($total);
        return number_format((float) $converted, 2, '.', '');
    }

    public function get_month_earnings()
    {
        $total = Conversion::where('affiliate_id', Auth::id())
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->sum('payout_usd');
        $converted = \App\Utils\CurrencyUtils::convertFromUsdToDefault($total);
        return number_format((float) $converted, 2, '.', '');
    }
}

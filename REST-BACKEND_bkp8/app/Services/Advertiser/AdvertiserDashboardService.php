<?php

namespace App\Services\Advertiser;

use Illuminate\Support\Facades\DB;
use App\Http\Traits\CommonFunctions;

class AdvertiserDashboardService
{
    use CommonFunctions;

    public function getDashboardStats($advertiser_id, $logged_in_user_type)
    {
        try {
            // Get advertiser's offers (using advertiser_id)
            $offers = $this->get_advertiser_offers($advertiser_id);

            // Get total spend (from offer_trackings revenue)
            $total_spend = $this->get_total_spend($advertiser_id);

            // Get conversions count
            $conversions = $this->get_conversions_count($advertiser_id);

            // Get pending invoices (placeholder for now)
            $pending_invoices = 0;

            // Get monthly spend data
            $monthly_spend = $this->get_monthly_spend($advertiser_id);

            return [
                'title' => 'Advertiser Dashboard',
                'module_name' => 'Advertiser Dashboard',
                'logged_in_user_type' => $logged_in_user_type,
                'offers' => $offers,
                'total_offers' => count($offers),
                'total_budget' => $total_spend,
                'conversions' => $conversions,
                'pending_invoices' => $pending_invoices,
                'monthly_spend' => $monthly_spend,
                'top_offers' => $this->top_advertiser_offers($advertiser_id)
            ];
        } catch (\Throwable $e) {
            return [
                'error' => $e->getMessage(),
                'total_offers' => 0,
                'total_budget' => 0,
                'conversions' => 0,
                'pending_invoices' => 0,
                'offers' => [],
                'top_offers' => []
            ];
        }
    }

    private function get_advertiser_offers($advertiser_id)
    {
        try {
            // Try to get offers by advertiser_id first
            $sql = "
                SELECT 
                    offers.id,
                    offers.offer_name,
                    offers.offer_status,
                    offers.offer_desc,
                    categories.category_name,
                    offers.id as oid,
                    (SELECT COUNT(*) FROM offer_trackings WHERE offer_id = offers.id) as conversions
                FROM 
                    offers 
                LEFT JOIN 
                    categories 
                ON 
                    offers.category_id = categories.id 
                WHERE 
                    offers.advertiser_id = " . $advertiser_id;

            $offers = DB::select($sql);
            return $offers;
        } catch (\Exception $e) {
            // If user_id doesn't exist, return empty array
            return [];
        }
    }

    private function get_total_spend($advertiser_id)
    {
        try {
            // Calculate total spend from offer_trackings for advertiser's offers
            $sql = "SELECT SUM(COALESCE(ot.revenue_usd, 0)) as total_spend 
                    FROM offer_trackings ot 
                    INNER JOIN offers o ON ot.offer_id = o.id 
                    WHERE o.advertiser_id = " . $advertiser_id;

            $result = DB::select($sql);

            return isset($result[0]) ? floatval($result[0]->total_spend ?? 0) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function get_conversions_count($advertiser_id)
    {
        try {
            $sql = "SELECT COUNT(*) as conversions 
                    FROM offer_trackings ot 
                    INNER JOIN offers o ON ot.offer_id = o.id 
                    WHERE o.advertiser_id = " . $advertiser_id;

            $result = DB::select($sql);

            return isset($result[0]) ? intval($result[0]->conversions) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function get_monthly_spend($advertiser_id)
    {
        try {
            $arrSTR = array();

            $sql = "SELECT sum(ot.revenue_usd) as spend, month(ot.created_at) as month 
                    FROM offer_trackings ot 
                    INNER JOIN offers o ON ot.offer_id = o.id 
                    WHERE o.advertiser_id = " . $advertiser_id . " 
                    GROUP BY month(ot.created_at)";

            $arr = DB::select($sql);

            foreach ($arr as $obj) {
                $month = $obj->month ? $obj->month : 0;
                $arrSTR[$month] = $obj->spend;
            }

            for ($index = 1; $index <= 12; $index++) {
                if (!isset($arrSTR[$index])) {
                    $arrSTR[$index] = 0;
                }
            }

            ksort($arrSTR, SORT_NUMERIC);

            return '[' . implode(',', $arrSTR) . ']';
        } catch (\Exception $e) {
            return '[0,0,0,0,0,0,0,0,0,0,0,0]';
        }
    }

    private function top_advertiser_offers($advertiser_id)
    {
        try {
            $sql = "SELECT 
                        o.id as offer_id, 
                        o.offer_name, 
                        COUNT(ot.id) as conversions, 
                        o.offer_status 
                    FROM 
                        offers o 
                    LEFT JOIN 
                        offer_trackings ot ON o.id = ot.offer_id 
                    WHERE 
                        o.advertiser_id = " . $advertiser_id . "
                    GROUP BY 
                        o.id, o.offer_name, o.offer_status 
                    ORDER BY 
                        conversions DESC 
                    LIMIT 5";

            return DB::select($sql);
        } catch (\Exception $e) {
            return [];
        }
    }
}

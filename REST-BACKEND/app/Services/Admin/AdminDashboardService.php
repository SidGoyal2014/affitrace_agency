<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Offer;
use App\Models\OfferTracking;
use App\Models\Conversion;
use App\Models\User;
use App\Models\OfferAffiliate;
use App\Http\Traits\SystemSettings;
use App\Utils\UrlHelper;

class AdminDashboardService
{
    use SystemSettings;

    public function getDashboardStats($user)
    {
        $logged_in_user_type = $user->user_type;

        $arr = $this->get_category_wise_revenue();

        $arrCats = array();
        $arrRevenue = array();

        foreach ($arr as $obj) {
            $arrCats[] = $obj->category_name;
            $arrRevenue[] = $obj->revenue;
        }

        return [
            'title' => ucfirst($logged_in_user_type) . ' Dashboard',
            'module_name' => ucfirst($logged_in_user_type) . ' Dashboard',
            'logged_in_user_type' => $logged_in_user_type,
            'click_message' => $this->get_clicks_message(),
            'clicks_consumed' => $this->get_total_clicks(),
            'clicks_limit' => $this->get_settings("num_clicks"),
            'total_offers' => $this->get_total_offers(),
            'total_no_of_advertisers' => $this->get_total_no_of_advertisers(),
            'total_no_of_affiliates' => $this->get_total_no_of_affiliates(),
            'total_margin' => $this->get_total_revenue() - $this->get_total_payouts(),
            'overall_clicks' => $this->get_overall_clicks(),
            'overall_conversions' => $this->get_overall_conversions(),
            'total_revenue' => $this->get_total_revenue(),
            'total_payouts' => $this->get_total_payouts(),
            'all_requests' => $this->get_offer_assign_requests(),
            'average_revenue_per_offer' => $this->get_average_revenue_per_offer(),
            'average_revenue_per_affiliate' => $this->get_average_revenue_per_affiliate(),
            'average_payout_per_offer' => $this->get_average_payout_per_offer(),
            'average_payout_per_affiliate' => $this->get_average_payout_per_affiliate(),
            'arrMonthlyRevenue' => $this->get_monthly_revenue(),
            'arrCats' => $arrCats,
            'labelCategories' => $arrCats,
            'strRevenue' => $arrRevenue,
            'topEarningOffers' => $this->top_offers(),
            'topEarningAffiliates' => $this->top_affiliates()
        ];
    }

    public function get_clicks_message()
    {
        $click_message = "";
        if ($this->get_total_clicks() >= $this->get_settings("num_clicks")) {
            $click_message = "Clicks not getting saved as maximum allowed limit as per purchased plan already reached for this month. Please upgrade.";
        } elseif ($this->get_settings("num_clicks") - $this->get_total_clicks() < 1000) {
            $click_message = "Maximum allowed limit of clicks is approaching fast. Less than 1000 clicks remaining for this month. Please upgrade.";
        }
        return $click_message;
    }

    public function top_affiliates($dod = "Y")
    {
        $strLimit = ($dod == "Y") ? " LIMIT 5 OFFSET 0" : "";
        $sql = "SELECT U.id as uid, C.affiliate_id as aff_id, U.name, sum(C.payout) as payout FROM users U INNER JOIN conversions C ON U.id=C.affiliate_id GROUP BY C.affiliate_id, U.id, U.name ORDER BY payout DESC" . $strLimit;

        $data = DB::select($sql);

        return [
            'module_name' => 'Top Affiliates',
            'data' => $data
        ];
    }

    public function top_offers($dod = "Y")
    {
        $sql = "SELECT C.offer_id, O.offer_name, sum(C.revenue) as revenue, O.offer_status FROM offers O INNER JOIN conversions C ON O.id=C.offer_id GROUP BY C.offer_id, O.offer_name, O.offer_status ORDER BY revenue DESC LIMIT 5 OFFSET 0";

        $data = DB::select($sql);

        return [
            'module_name' => 'Top Offers',
            'data' => $data
        ];
    }

    public function rejectOfferAssignRequest($oaid)
    {
        $rec = OfferAffiliate::find($oaid);
        if (!$rec) {
            return ['message' => 'Offer assign request not found'];
        }
        $rec->request_status = 'Rejected';
        $rec->save();
        return ['message' => 'Offer assign request updated successfully'];
    }

    public function approveOfferAssignRequest($oaid)
    {
        $rec = OfferAffiliate::find($oaid);
        if (!$rec) {
            return ['status' => 0, 'message' => 'Offer assign request not found', 'code' => 404];
        }

        $turl = UrlHelper::getBaseUrl() . "/offer/gourl/" . $rec->offer_id . "/" . $rec->affiliate_id . "/?&tid={tid}";

        $rec->request_status = 'Accepted';
        $rec->request_made = 'N';
        $rec->tracking_url = $turl;
        $rec->save();

        return ['status' => 1, 'message' => 'Offer assign request approved successfully', 'tracking_url' => $turl, 'code' => 200];
    }

    private function get_category_wise_revenue()
    {
        $sql = "SELECT 
            EXTRACT(MONTH FROM o.created_at) AS month,
            c.category_name,
            SUM(conv.revenue_usd) AS revenue
        FROM 
            conversions conv
        JOIN 
            offers o ON conv.offer_id = o.id
        JOIN 
            categories c ON o.category_id = c.id
        GROUP BY 
            EXTRACT(MONTH FROM o.created_at), c.category_name
        ORDER BY 
            EXTRACT(MONTH FROM o.created_at), c.category_name
        LIMIT 5 OFFSET 0";

        return DB::select($sql);
    }

    private function get_monthly_revenue()
    {
        $arrSTR = array();
        $sql = "SELECT sum(revenue_usd) as revenue, EXTRACT(MONTH FROM created_at) as month FROM conversions group by EXTRACT(MONTH FROM created_at)";
        $arr = DB::select($sql);

        foreach ($arr as $obj) {
            $month = (trim($obj->month) == "") ? 0 : $obj->month;
            $arrSTR[$month] = $obj->revenue;
        }

        for ($index = 1; $index < 12; $index++) {
            if (!isset($arrSTR[$index])) {
                $arrSTR[$index] = 0;
            }
        }
        ksort($arrSTR, SORT_NUMERIC);
        return '[' . implode(',', $arrSTR) . ']';
    }

    private function get_total_offers()
    {
        return Offer::count();
    }

    private function get_total_revenue()
    {
        $total_rev = Conversion::select(DB::raw('SUM(revenue_usd) as total'))
            ->get()->toArray();
        return $total_rev[0]['total'] ?? 0;
    }

    private function get_total_payouts()
    {
        $to_payout = Conversion::select(DB::raw('SUM(payout_usd) as total'))
            ->get()->toArray();
        return $to_payout[0]['total'] ?? 0;
    }

    private function get_total_no_of_affiliates()
    {
        return User::where('user_type', \App\Enums\UserType::Affiliate->value)->count();
    }

    private function get_total_no_of_advertisers()
    {
        return User::where('user_type', \App\Enums\UserType::Advertiser->value)->count();
    }

    private function get_overall_clicks()
    {
        return OfferTracking::distinct('tid')->count('tid');
    }

    private function get_overall_conversions()
    {
        return Conversion::count();
    }

    private function get_average_revenue_per_offer()
    {
        return ($this->get_total_offers() > 0) ? $this->get_total_revenue() / $this->get_total_offers() : '0';
    }

    private function get_average_revenue_per_affiliate()
    {
        return ($this->get_total_no_of_affiliates() > 0) ? $this->get_total_revenue() / $this->get_total_no_of_affiliates() : '0';
    }

    private function get_average_payout_per_offer()
    {
        return ($this->get_total_offers() > 0) ? $this->get_total_payouts() / $this->get_total_offers() : '0';
    }

    private function get_average_payout_per_affiliate()
    {
        return ($this->get_total_no_of_affiliates() > 0) ? $this->get_total_payouts() / $this->get_total_no_of_affiliates() : '0';
    }

    private function get_offer_assign_requests()
    {
        $sql = "
                SELECT 
                    *, offers.id as oid, users.id as aid, offer_affiliates.id as oaid
                FROM 
                    offers 
                    INNER JOIN 
                        offer_affiliates
                        ON 
                            offers.id = offer_affiliates.offer_id
                    INNER JOIN 
                        users 
                        ON 
                            offer_affiliates.affiliate_id = users.id
                WHERE
                    request_made = 'Y'
                    AND
                    request_status = 'Pending'
            ";
        return DB::select($sql);
    }

    public function getRevenuePayoutChartData(string $period, ?string $from = null, ?string $to = null): array
    {
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        if ($period === 'daily') {
            if ($driver === 'pgsql') {
                $sql = "SELECT date as label,
                               SUM(revenue_usd) as revenue,
                               SUM(payout_usd) as payout
                        FROM conversions
                        WHERE date >= CURRENT_DATE - INTERVAL '30 days'
                        GROUP BY date
                        ORDER BY label ASC";
            } else {
                $sql = "SELECT DATE(date) as label,
                               SUM(revenue_usd) as revenue,
                               SUM(payout_usd) as payout
                        FROM conversions
                        WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                        GROUP BY DATE(date)
                        ORDER BY label ASC";
            }
            $rows = DB::select($sql);
            return $this->buildChartArrays($rows, $this->generateDailyLabels(30));
        }

        if ($period === 'weekly') {
            if ($driver === 'pgsql') {
                $sql = "SELECT CAST(TO_CHAR(date, 'IYYYIW') AS INTEGER) as yw,
                               SUM(revenue_usd) as revenue,
                               SUM(payout_usd) as payout
                        FROM conversions
                        WHERE date >= CURRENT_DATE - INTERVAL '12 weeks'
                        GROUP BY yw
                        ORDER BY yw ASC";
            } else {
                $sql = "SELECT YEARWEEK(date, 1) as yw,
                               SUM(revenue_usd) as revenue,
                               SUM(payout_usd) as payout
                        FROM conversions
                        WHERE date >= DATE_SUB(CURDATE(), INTERVAL 12 WEEK)
                        GROUP BY yw
                        ORDER BY yw ASC";
            }
            $rows = DB::select($sql);
            return $this->buildWeeklyChartArrays($rows);
        }

        if ($period === 'custom' && $from && $to) {
            if ($driver === 'pgsql') {
                $sql = "SELECT date as label,
                               SUM(revenue_usd) as revenue,
                               SUM(payout_usd) as payout
                        FROM conversions
                        WHERE date BETWEEN ? AND ?
                        GROUP BY date
                        ORDER BY label ASC";
            } else {
                $sql = "SELECT DATE(date) as label,
                               SUM(revenue_usd) as revenue,
                               SUM(payout_usd) as payout
                        FROM conversions
                        WHERE date BETWEEN ? AND ?
                        GROUP BY DATE(date)
                        ORDER BY label ASC";
            }
            $rows = DB::select($sql, [$from, $to]);
            return $this->buildChartArrays($rows, $this->generateCustomDailyLabels($from, $to));
        }

        // Default: monthly (last 12 months)
        if ($driver === 'pgsql') {
            $sql = "SELECT TO_CHAR(date, 'YYYY-MM') as label,
                           SUM(revenue_usd) as revenue,
                           SUM(payout_usd) as payout
                    FROM conversions
                    WHERE date >= CURRENT_DATE - INTERVAL '12 months'
                    GROUP BY label
                    ORDER BY label ASC";
        } else {
            $sql = "SELECT DATE_FORMAT(date, '%Y-%m') as label,
                           SUM(revenue_usd) as revenue,
                           SUM(payout_usd) as payout
                    FROM conversions
                    WHERE date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    GROUP BY label
                    ORDER BY label ASC";
        }
        $rows = DB::select($sql);
        return $this->buildChartArrays($rows, $this->generateMonthlyLabels(12));
    }

    private function buildChartArrays(array $rows, array $expectedLabels): array
    {
        $map = [];
        foreach ($rows as $row) {
            $map[(string) $row->label] = [
                'revenue' => (float) ($row->revenue ?? 0),
                'payout' => (float) ($row->payout ?? 0),
            ];
        }

        $labels = [];
        $revenue = [];
        $payout = [];

        foreach ($expectedLabels as $label) {
            $labels[] = $label;
            $revenue[] = $map[$label]['revenue'] ?? 0.00;
            $payout[] = $map[$label]['payout'] ?? 0.00;
        }

        return compact('labels', 'revenue', 'payout');
    }

    private function buildWeeklyChartArrays(array $rows): array
    {
        // Build a map keyed by YEARWEEK integer (e.g. 202620)
        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row->yw] = [
                'revenue' => (float) ($row->revenue ?? 0),
                'payout' => (float) ($row->payout ?? 0),
            ];
        }

        // Generate the last 12 ISO week keys
        $expectedWeeks = [];
        $dt = new \DateTime();
        $dt->modify('monday this week');
        for ($i = 11; $i >= 0; $i--) {
            $weekDt = clone $dt;
            $weekDt->modify("-{$i} weeks");
            $yw = (int) $weekDt->format('oW'); // ISO year + week number, e.g. 202620
            $label = $weekDt->format('o') . '-W' . $weekDt->format('W'); // e.g. 2026-W20
            $expectedWeeks[$yw] = $label;
        }

        $labels = [];
        $revenue = [];
        $payout = [];

        foreach ($expectedWeeks as $yw => $label) {
            $labels[] = $label;
            $revenue[] = $map[$yw]['revenue'] ?? 0.00;
            $payout[] = $map[$yw]['payout'] ?? 0.00;
        }

        return compact('labels', 'revenue', 'payout');
    }

    private function generateDailyLabels(int $days): array
    {
        $labels = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $labels[] = date('Y-m-d', strtotime("-{$i} days"));
        }
        return $labels;
    }

    private function generateCustomDailyLabels(string $from, string $to): array
    {
        $labels = [];
        $current = new \DateTime($from);
        $end = new \DateTime($to);
        while ($current <= $end) {
            $labels[] = $current->format('Y-m-d');
            $current->modify('+1 day');
        }
        return $labels;
    }

    private function generateMonthlyLabels(int $months): array
    {
        $labels = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $labels[] = date('Y-m', strtotime("first day of -{$i} months"));
        }
        return $labels;
    }
}

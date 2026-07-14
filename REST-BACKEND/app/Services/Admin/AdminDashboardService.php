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

    private $user;

    public function getDashboardStats($user)
    {
        $this->user = $user;
        $logged_in_user_type = $user->user_type;

        $arr = $this->get_category_wise_revenue();

        $arrCats = array();
        $arrRevenue = array();

        foreach ($arr as $obj) {
            $arrCats[] = $obj->category_name;
            $arrRevenue[] = \App\Utils\CurrencyUtils::convertFromUsdToDefault($obj->revenue);
        }

        $stats = [
            'title' => ucfirst($logged_in_user_type) . ' Dashboard',
            'module_name' => ucfirst($logged_in_user_type) . ' Dashboard',
            'logged_in_user_type' => $logged_in_user_type,
            'default_currency' => \App\Models\Setting::find(1)->default_currency ?? 'USD',
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
            'topEarningOffers' => $this->top_offers($user),
            'topEarningAffiliates' => $this->top_affiliates($user)
        ];

        if ($user && $user->isObserver()) {
            $stats['total_margin'] = 0;
            $stats['total_revenue'] = 0;
            $stats['total_payouts'] = 0;
            $stats['average_revenue_per_offer'] = 0;
            $stats['average_revenue_per_affiliate'] = 0;
            $stats['average_payout_per_offer'] = 0;
            $stats['average_payout_per_affiliate'] = 0;
            $stats['strRevenue'] = array_fill(0, count($arrCats), 0);
            $stats['arrMonthlyRevenue'] = '[' . implode(',', array_fill(0, 12, 0)) . ']';

            if (isset($stats['topEarningOffers']['data'])) {
                foreach ($stats['topEarningOffers']['data'] as $row) {
                    $row->revenue = 0;
                }
            }
            if (isset($stats['topEarningAffiliates']['data'])) {
                foreach ($stats['topEarningAffiliates']['data'] as $row) {
                    $row->payout = 0;
                }
            }
        }

        return $stats;
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

    public function top_affiliates($user = null, $dod = "Y")
    {
        $query = DB::table('users as U')
            ->join('conversions as C', 'U.id', '=', 'C.affiliate_id')
            ->select('U.id as uid', 'C.affiliate_id as aff_id', 'U.name', DB::raw('sum(C.payout_usd) as payout'))
            ->groupBy('C.affiliate_id', 'U.id', 'U.name')
            ->orderBy('payout', 'desc');

        if ($user) {
            if ($user->isAffiliateManager()) {
                $query->where('U.employee_id', (string) $user->id);
            } elseif ($user->isAdvertiserManager()) {
                $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->join('offers as O', 'C.offer_id', '=', 'O.id')
                      ->whereIn('O.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }

        if ($dod == "Y") {
            $query->limit(5);
        }

        $data = $query->get()->toArray();

        foreach ($data as $row) {
            $row->payout = \App\Utils\CurrencyUtils::convertFromUsdToDefault($row->payout);
        }

        return [
            'module_name' => 'Top Affiliates',
            'data' => $data
        ];
    }

    public function top_offers($user = null, $dod = "Y")
    {
        $query = DB::table('offers as O')
            ->join('conversions as C', 'O.id', '=', 'C.offer_id')
            ->select('C.offer_id', 'O.offer_name', DB::raw('sum(C.revenue_usd) as revenue'), 'O.offer_status')
            ->groupBy('C.offer_id', 'O.offer_name', 'O.offer_status')
            ->orderBy('revenue', 'desc')
            ->limit(5);

        if ($user) {
            if ($user->isAffiliateManager()) {
                $query->join('users as U', 'C.affiliate_id', '=', 'U.id')
                      ->where('U.employee_id', (string) $user->id);
            } elseif ($user->isAdvertiserManager()) {
                $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('O.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }

        $data = $query->get()->toArray();

        foreach ($data as $row) {
            $row->revenue = \App\Utils\CurrencyUtils::convertFromUsdToDefault($row->revenue);
        }

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
        $query = DB::table('conversions as conv')
            ->join('offers as o', 'conv.offer_id', '=', 'o.id')
            ->join('categories as c', 'o.category_id', '=', 'c.id')
            ->select(
                DB::raw('EXTRACT(MONTH FROM o.created_at) AS month'),
                'c.category_name',
                DB::raw('SUM(conv.revenue_usd) AS revenue')
            )
            ->groupBy(DB::raw('EXTRACT(MONTH FROM o.created_at)'), 'c.category_name')
            ->orderBy(DB::raw('EXTRACT(MONTH FROM o.created_at)'), 'c.category_name')
            ->limit(5);

        if ($this->user) {
            if ($this->user->isAffiliateManager()) {
                $query->join('users as u', 'conv.affiliate_id', '=', 'u.id')
                      ->where('u.employee_id', (string) $this->user->id);
            } elseif ($this->user->isAdvertiserManager()) {
                $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $this->user->id)->pluck('id')->toArray();
                $query->whereIn('o.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }

        return $query->get();
    }

    private function get_monthly_revenue()
    {
        $arrSTR = array();
        $query = DB::table('conversions')
            ->select(DB::raw('sum(revenue_usd) as revenue'), DB::raw('EXTRACT(MONTH FROM created_at) as month'))
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'));

        if ($this->user) {
            if ($this->user->isAffiliateManager()) {
                $query->join('users as u', 'conversions.affiliate_id', '=', 'u.id')
                      ->where('u.employee_id', (string) $this->user->id);
            } elseif ($this->user->isAdvertiserManager()) {
                $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $this->user->id)->pluck('id')->toArray();
                $query->join('offers as o', 'conversions.offer_id', '=', 'o.id')
                      ->whereIn('o.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }

        $arr = $query->get();

        foreach ($arr as $obj) {
            $month = (trim($obj->month) == "") ? 0 : $obj->month;
            $arrSTR[$month] = \App\Utils\CurrencyUtils::convertFromUsdToDefault($obj->revenue);
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
        $query = Offer::query();
        if ($this->user && $this->user->isAdvertiserManager()) {
            $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $this->user->id)->pluck('id')->toArray();
            $query->whereIn('advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
        }
        return $query->count();
    }

    private function get_total_revenue()
    {
        $query = Conversion::query();
        if ($this->user) {
            if ($this->user->isAffiliateManager()) {
                $query->join('users as u', 'conversions.affiliate_id', '=', 'u.id')
                      ->where('u.employee_id', (string) $this->user->id);
            } elseif ($this->user->isAdvertiserManager()) {
                $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $this->user->id)->pluck('id')->toArray();
                $query->join('offers as o', 'conversions.offer_id', '=', 'o.id')
                      ->whereIn('o.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }
        $usdAmount = $query->sum('revenue_usd');
        return \App\Utils\CurrencyUtils::convertFromUsdToDefault($usdAmount);
    }

    private function get_total_payouts()
    {
        $query = Conversion::query();
        if ($this->user) {
            if ($this->user->isAffiliateManager()) {
                $query->join('users as u', 'conversions.affiliate_id', '=', 'u.id')
                      ->where('u.employee_id', (string) $this->user->id);
            } elseif ($this->user->isAdvertiserManager()) {
                $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $this->user->id)->pluck('id')->toArray();
                $query->join('offers as o', 'conversions.offer_id', '=', 'o.id')
                      ->whereIn('o.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }
        $usdAmount = $query->sum('payout_usd');
        return \App\Utils\CurrencyUtils::convertFromUsdToDefault($usdAmount);
    }

    private function get_total_no_of_affiliates()
    {
        $query = User::where('user_type', \App\Enums\UserType::Affiliate->value);
        if ($this->user && $this->user->isAffiliateManager()) {
            $query->where('employee_id', (string) $this->user->id);
        }
        return $query->count();
    }

    private function get_total_no_of_advertisers()
    {
        $query = User::where('user_type', \App\Enums\UserType::Advertiser->value);
        if ($this->user && $this->user->isAdvertiserManager()) {
            $query->where('employee_id', (string) $this->user->id);
        }
        return $query->count();
    }

    private function get_overall_clicks()
    {
        $query = OfferTracking::query();
        if ($this->user) {
            if ($this->user->isAffiliateManager()) {
                $query->join('users as u', 'offer_trackings.affiliate_id', '=', 'u.id')
                      ->where('u.employee_id', (string) $this->user->id);
            } elseif ($this->user->isAdvertiserManager()) {
                $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $this->user->id)->pluck('id')->toArray();
                $query->join('offers as o', 'offer_trackings.offer_id', '=', 'o.id')
                      ->whereIn('o.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }
        return $query->distinct('tid')->count('tid');
    }

    private function get_overall_conversions()
    {
        $query = Conversion::query();
        if ($this->user) {
            if ($this->user->isAffiliateManager()) {
                $query->join('users as u', 'conversions.affiliate_id', '=', 'u.id')
                      ->where('u.employee_id', (string) $this->user->id);
            } elseif ($this->user->isAdvertiserManager()) {
                $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $this->user->id)->pluck('id')->toArray();
                $query->join('offers as o', 'conversions.offer_id', '=', 'o.id')
                      ->whereIn('o.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }
        return $query->count();
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
        $query = DB::table('offers')
            ->join('offer_affiliates', 'offers.id', '=', 'offer_affiliates.offer_id')
            ->join('users', 'offer_affiliates.affiliate_id', '=', 'users.id')
            ->select('offers.*', 'offers.id as oid', 'users.id as aid', 'offer_affiliates.id as oaid')
            ->where('request_made', 'Y')
            ->where('request_status', 'Pending');

        if ($this->user) {
            if ($this->user->isAffiliateManager()) {
                $query->where('users.employee_id', (string) $this->user->id);
            } elseif ($this->user->isAdvertiserManager()) {
                $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $this->user->id)->pluck('id')->toArray();
                $query->whereIn('offers.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }

        return $query->get()->toArray();
    }

    public function getRevenuePayoutChartData(string $period, ?string $from = null, ?string $to = null, $user = null): array
    {
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $query = DB::table('conversions');

        if ($user) {
            if ($user->isAffiliateManager()) {
                $query->join('users as u', 'conversions.affiliate_id', '=', 'u.id')
                      ->where('u.employee_id', (string) $user->id);
            } elseif ($user->isAdvertiserManager()) {
                $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->join('offers as o', 'conversions.offer_id', '=', 'o.id')
                      ->whereIn('o.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }

        if ($period === 'daily') {
            if ($driver === 'pgsql') {
                $query->select(DB::raw('date as label'), DB::raw('SUM(revenue_usd) as revenue'), DB::raw('SUM(payout_usd) as payout'))
                      ->where('date', '>=', DB::raw("CURRENT_DATE - INTERVAL '30 days'"))
                      ->groupBy('date')
                      ->orderBy('label', 'ASC');
            } else {
                $query->select(DB::raw('DATE(date) as label'), DB::raw('SUM(revenue_usd) as revenue'), DB::raw('SUM(payout_usd) as payout'))
                      ->where('date', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL 30 DAY)'))
                      ->groupBy(DB::raw('DATE(date)'))
                      ->orderBy('label', 'ASC');
            }
            $rows = $query->get()->toArray();
            $result = $this->buildChartArrays($rows, $this->generateDailyLabels(30));
        } elseif ($period === 'weekly') {
            if ($driver === 'pgsql') {
                $query->select(DB::raw("CAST(TO_CHAR(date, 'IYYYIW') AS INTEGER) as yw"), DB::raw('SUM(revenue_usd) as revenue'), DB::raw('SUM(payout_usd) as payout'))
                      ->where('date', '>=', DB::raw("CURRENT_DATE - INTERVAL '12 weeks'"))
                      ->groupBy('yw')
                      ->orderBy('yw', 'ASC');
            } else {
                $query->select(DB::raw('YEARWEEK(date, 1) as yw'), DB::raw('SUM(revenue_usd) as revenue'), DB::raw('SUM(payout_usd) as payout'))
                      ->where('date', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL 12 WEEK)'))
                      ->groupBy('yw')
                      ->orderBy('yw', 'ASC');
            }
            $rows = $query->get()->toArray();
            $result = $this->buildWeeklyChartArrays($rows);
        } elseif ($period === 'custom' && $from && $to) {
            if ($driver === 'pgsql') {
                $query->select(DB::raw('date as label'), DB::raw('SUM(revenue_usd) as revenue'), DB::raw('SUM(payout_usd) as payout'))
                      ->whereBetween('date', [$from, $to])
                      ->groupBy('date')
                      ->orderBy('label', 'ASC');
            } else {
                $query->select(DB::raw('DATE(date) as label'), DB::raw('SUM(revenue_usd) as revenue'), DB::raw('SUM(payout_usd) as payout'))
                      ->whereBetween('date', [$from, $to])
                      ->groupBy(DB::raw('DATE(date)'))
                      ->orderBy('label', 'ASC');
            }
            $rows = $query->get()->toArray();
            $result = $this->buildChartArrays($rows, $this->generateCustomDailyLabels($from, $to));
        } else {
            // Default: monthly (last 12 months)
            if ($driver === 'pgsql') {
                $query->select(DB::raw("TO_CHAR(date, 'YYYY-MM') as label"), DB::raw('SUM(revenue_usd) as revenue'), DB::raw('SUM(payout_usd) as payout'))
                      ->where('date', '>=', DB::raw("CURRENT_DATE - INTERVAL '12 months'"))
                      ->groupBy('label')
                      ->orderBy('label', 'ASC');
            } else {
                $query->select(DB::raw("DATE_FORMAT(date, '%Y-%m') as label"), DB::raw('SUM(revenue_usd) as revenue'), DB::raw('SUM(payout_usd) as payout'))
                      ->where('date', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL 12 MONTH)'))
                      ->groupBy('label')
                      ->orderBy('label', 'ASC');
            }
            $rows = $query->get()->toArray();
            $result = $this->buildChartArrays($rows, $this->generateMonthlyLabels(12));
        }

        if ($user && $user->isObserver()) {
            if (isset($result['revenue'])) {
                $result['revenue'] = array_fill(0, count($result['revenue']), 0);
            }
            if (isset($result['payout'])) {
                $result['payout'] = array_fill(0, count($result['payout']), 0);
            }
        }

        return $result;
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
            $revUsd = $map[$label]['revenue'] ?? 0.00;
            $payUsd = $map[$label]['payout'] ?? 0.00;
            $revenue[] = \App\Utils\CurrencyUtils::convertFromUsdToDefault($revUsd);
            $payout[] = \App\Utils\CurrencyUtils::convertFromUsdToDefault($payUsd);
        }

        return compact('labels', 'revenue', 'payout');
    }

    private function buildWeeklyChartArrays(array $rows): array
    {
        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row->yw] = [
                'revenue' => (float) ($row->revenue ?? 0),
                'payout' => (float) ($row->payout ?? 0),
            ];
        }

        $expectedWeeks = [];
        $dt = new \DateTime();
        $dt->modify('monday this week');
        for ($i = 11; $i >= 0; $i--) {
            $weekDt = clone $dt;
            $weekDt->modify("-{$i} weeks");
            $yw = (int) $weekDt->format('oW');
            $label = $weekDt->format('o') . '-W' . $weekDt->format('W');
            $expectedWeeks[$yw] = $label;
        }

        $labels = [];
        $revenue = [];
        $payout = [];

        foreach ($expectedWeeks as $yw => $label) {
            $labels[] = $label;
            $revUsd = $map[$yw]['revenue'] ?? 0.00;
            $payUsd = $map[$yw]['payout'] ?? 0.00;
            $revenue[] = \App\Utils\CurrencyUtils::convertFromUsdToDefault($revUsd);
            $payout[] = \App\Utils\CurrencyUtils::convertFromUsdToDefault($payUsd);
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

<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Offer;
use App\Models\OfferTracking;
use App\Models\Conversion;
use App\Models\User;
use App\Models\OfferAffiliate;
use App\Http\Traits\SystemSettings;

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
            'total_revenue' => $this->get_total_revenue(),
            'total_payouts' => $this->get_total_payouts(),
            'total_no_of_affiliates' => $this->get_total_no_of_affiliates(),
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
        $rec->request_status = 'Rejected';
        $rec->save();
        return ['message' => 'Offer assign request updated successfully'];
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
                    request_status != 'Accepted'
            ";
        return DB::select($sql);
    }
}

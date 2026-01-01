<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Offer;
use App\Models\OfferTracking;
use App\Models\Conversion;
use App\Models\User;

class RootDashboardService
{
    public function getTopAffiliates($dod = "Y")
    {
        $strLimit = " LIMIT 0,5";
        $sql = "SELECT U.id as uid, C.affiliate_id as aff_id, U.name, sum(C.payout) as payout FROM users U INNER JOIN conversions C ON U.id=C.affiliate_id GROUP BY C.affiliate_id, U.id, U.name ORDER BY payout DESC" . $strLimit;
        return DB::select($sql);
    }

    public function getTopOffers($dod = "Y")
    {
        $sql = "SELECT C.offer_id, O.offer_name, sum(C.revenue) as revenue, O.offer_status FROM offers O INNER JOIN conversions C ON O.id=C.offer_id GROUP BY C.offer_id, O.offer_name, O.offer_status ORDER BY revenue DESC LIMIT 0,5";
        return DB::select($sql);
    }

    public function getCategoryWiseRevenue($affiliate_id = "")
    {
        $strWhereClause = "";
        if ($affiliate_id > 0) {
            $strWhereClause = " AND conv.affiliate_id = " . $affiliate_id;
        }

        $sql = "SELECT 
            MONTH(o.created_at) AS month,
            c.category_name,
            SUM(conv.revenue_usd) AS revenue
        FROM 
            conversions conv
        JOIN 
            offers o ON conv.offer_id = o.id
        JOIN 
            categories c ON o.category_id = c.id
        WHERE
            1=1
            " . $strWhereClause . "
        GROUP BY 
            MONTH(o.created_at), c.category_name
        ORDER BY 
            MONTH(o.created_at), c.category_name
        LIMIT 0,5";

        return DB::select($sql);
    }

    public function getMonthlyRevenue($affiliate_id = "")
    {
        $strWhereClause = "";
        if ($affiliate_id > 0) {
            $strWhereClause = " AND affiliate_id = " . $affiliate_id;
        }

        $arrSTR = array();
        $sql = "SELECT sum(revenue_usd) as revenue, month(created_at) as month FROM conversions where 1=1 " . $strWhereClause . " group by month(created_at)";

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

    // Currently these methods in controller check Auth::check() and redirect if not logged in.
    // In Service layer, we assume Auth check is done by middleware or controller.
    // We will just return the data.

    public function getTotalOffers()
    {
        return Offer::count();
    }

    public function getTotalRevenue()
    {
        $total_rev = Conversion::select(DB::raw('SUM(revenue_usd) as total'))
            ->get()->toArray();
        return $total_rev[0]['total'] ?? 0;
    }

    public function getTotalPayouts()
    {
        $to_payout = Conversion::select(DB::raw('SUM(payout_usd) as total'))
            ->get()->toArray();
        return $to_payout[0]['total'] ?? 0;
    }

    public function getTotalAffiliates()
    {
        return User::where('user_type', \App\Enums\UserType::Affiliate->value)->count();
    }

    public function getAverageRevenuePerOffer()
    {
        $totalOffers = $this->getTotalOffers();
        return ($totalOffers > 0) ? $this->getTotalRevenue() / $totalOffers : '0';
    }

    public function getAverageRevenuePerAffiliate()
    {
        $totalAffiliates = $this->getTotalAffiliates();
        return ($totalAffiliates > 0) ? $this->getTotalRevenue() / $totalAffiliates : '0';
    }

    public function getAveragePayoutPerOffer()
    {
        $totalOffers = $this->getTotalOffers();
        return ($totalOffers > 0) ? $this->getTotalPayouts() / $totalOffers : '0';
    }

    public function getAveragePayoutPerAffiliate()
    {
        $totalAffiliates = $this->getTotalAffiliates();
        return ($totalAffiliates > 0) ? $this->getTotalPayouts() / $totalAffiliates : '0';
    }
}

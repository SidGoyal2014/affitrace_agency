<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Offer;
use App\Models\OfferAffiliate;
use App\Models\OfferTracking;
use App\Models\User;
use App\Http\Traits\SystemSettings;

class AdminDashboardController extends Controller
{
	use SystemSettings;

	public function dashboard(Request $request)
	{

		Log::info($request->all());
		//exit;
		// User is already authenticated via Sanctum
		$user = $request->user();
		$logged_in_user_type = $user->user_type;

		$arr = $this->get_category_wise_revenue();

		$arrCats = array();
		$arrRevenue = array();

		foreach ($arr as $obj) {
			$arrCats[] = $obj->category_name;
			$arrRevenue[] = $obj->revenue;
		}

		return response()->json([
			'title' => ucfirst($logged_in_user_type) . ' Dashboard',
			'module_name' => ucfirst($logged_in_user_type) . ' Dashboard',
			'logged_in_user_type' => $logged_in_user_type,
			'click_message' => $this->get_clicks_message(),

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
		]);
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
		$strLimit = ($dod == "Y") ? " LIMIT 0,5" : "";
		$sql = "SELECT U.id as uid, OT.aff_id, U.name, sum(OT.payout) as payout FROM  users U INNER JOIN offer_trackings OT ON U.id=OT.aff_id GROUP BY OT.aff_id, U.id, U.name ORDER BY payout DESC" . $strLimit;

		$data = DB::select($sql);

		return response()->json([
			'module_name' => 'Top Affiliates',
			'data' => $data
		]);
	}

	public function top_offers($dod = "Y")
	{
		$sql = "SELECT OT.offer_id, O.offer_name, sum(OT.revenue) as revenue, O.offer_status FROM  offers O INNER JOIN offer_trackings OT ON O.id=OT.offer_id GROUP BY OT.offer_id, O.offer_name, O.offer_status ORDER BY revenue DESC LIMIT 0,5";

		$data = DB::select($sql);

		return response()->json([
			'module_name' => 'Top Offers',
			'data' => $data
		]);
	}

	private function get_category_wise_revenue()
	{
		$sql = "SELECT 
            MONTH(o.created_at) AS month,
            c.category_name,
            SUM(ot.revenue_usd) AS revenue
        FROM 
            offer_trackings ot
        JOIN 
            offers o ON ot.offer_id = o.id
        JOIN 
            categories c ON o.category_id = c.id
        GROUP BY 
            MONTH(o.created_at), c.category_name
        ORDER BY 
            MONTH(o.created_at), c.category_name
        LIMIT 0,5";

		return DB::select($sql);
	}

	private function get_monthly_revenue()
	{
		$arrSTR = array();
		$sql = "SELECT sum(revenue_usd) as revenue, month(created_at) as month FROM offer_trackings where aff_click_id != '' group by month(created_at)";
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
		$total_rev = OfferTracking::select(DB::raw('SUM(revenue_usd) as total'))
			->where('aff_click_id', '!=', '')
			->get()->toArray();
		return $total_rev[0]['total'];
	}

	private function get_total_payouts()
	{
		$to_payout = OfferTracking::select(DB::raw('SUM(payout_usd) as total'))
			->where('aff_click_id', '!=', '')
			->get()->toArray();
		return $to_payout[0]['total'];
	}

	private function get_total_no_of_affiliates()
	{
		return User::where('user_type', 'affiliate')->count();
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

	public function reject_offer_assign_requests($oaid)
	{
		$rec = OfferAffiliate::find($oaid);
		$rec->request_status = 'Rejected';
		$rec->save();
		return response()->json(['message' => 'Offer assign request updated successfully']);
	}
}

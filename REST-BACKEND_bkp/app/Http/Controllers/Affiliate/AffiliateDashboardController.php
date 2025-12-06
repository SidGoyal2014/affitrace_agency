<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DB, Auth, Http, Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\Offer;
use App\Models\OfferAffiliate;
use App\Models\OfferAssignRequest;
use App\Models\OfferTracking;
use App\Models\User;
use App\Models\Category;
use App\Models\AffiliateUserDetail;
use App\Models\AdvertiserUserDetail;

use App\Http\Traits\CommonFunctions;

class AffiliateDashboardController extends Controller
{

	use CommonFunctions;

	public function dashboard(Request $request)
	{

		$logged_in_user_type = $request->user()->user_type; // From Token

		$arr = $this->get_category_wise_revenue();

		$arrCats = array();
		$arrRevenue = array();

		foreach ($arr as $obj) {
			$arrCats[] = $obj->category_name;
			$arrRevenue[] = $obj->revenue;
		}//foreach

		$obj_offers = $this->get_affiliate_offers();

		// Safe check for all_offer_ids
		$arr_affiliate_offers = [];
		if ($obj_offers && isset($obj_offers->all_offer_ids)) {
			$arr_affiliate_offers = explode(",", $obj_offers->all_offer_ids);
		}

		return response()->json([
			'title' => ucfirst($logged_in_user_type) . ' Dashboard',
			'module_name' => ucfirst($logged_in_user_type) . ' Dashboard',
			'logged_in_user_type' => $logged_in_user_type,
			'all_offers' => $this->get_all_offers(),
			'arr_affiliate_offers' => $arr_affiliate_offers,
			'offers_assigned' => $this->get_offers_assigned(),

			'arrMonthlyRevenue' => $this->get_monthly_revenue(),
			'arrCats' => $arrCats, // Send array, frontend can stringify if needed
			'labelCategories' => $arrCats,
			'strRevenue' => $arrRevenue,
			'topEarningOffers' => $this->top_offers(),
			'topEarningAffiliates' => $this->top_affiliates()
		]);

	}//dashboard


	public function top_affiliates($dod = "Y")
	{
		// Always return data
		$strLimit = " LIMIT 0,5";
		$sql = "SELECT U.id as uid, OT.aff_id, U.name, sum(OT.payout) as payout FROM  users U INNER JOIN offer_trackings OT ON U.id=OT.aff_id GROUP BY OT.aff_id ORDER BY revenue DESC" . $strLimit;
		$data = DB::select($sql);
		return $data;
	}//top_affiliates


	public function top_offers($dod = "Y")
	{
		// Always return data
		$sql = "SELECT OT.offer_id, O.offer_name, sum(OT.revenue) as revenue, O.offer_status FROM  offers O INNER JOIN offer_trackings OT ON O.id=OT.offer_id GROUP BY OT.offer_id ORDER BY revenue DESC LIMIT 0,5";
		$arr = DB::select($sql);
		return $arr;
	}//top_offers




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
		WHERE
			ot.aff_id = " . Auth::id() . "
		GROUP BY 
		    MONTH(o.created_at), c.category_name
		ORDER BY 
		    MONTH(o.created_at), c.category_name
		LIMIT 0,5";


		$arr = DB::select($sql);

		return $arr;

	}//get_category_wise_revenue


	private function get_monthly_revenue()
	{

		//where year(campaign_start_date) = year(now()) 

		$arrSTR = array();

		$sql = "SELECT sum(revenue_usd) as revenue, month(created_at) as month FROM offer_trackings where aff_id = " . Auth::id() . " AND aff_click_id != '' group by month(created_at); ";

		$arr = DB::select($sql);

		foreach ($arr as $obj) {

			$month = 0;

			if (trim($obj->month) == "") {
				$month = 0;
			} else {
				$month = $obj->month;

			}

			$arrSTR[$month] = $obj->revenue;

		}//foreach



		for ($index = 1; $index < 12; $index++) {

			if (!isset($arrSTR[$index])) {
				$arrSTR[$index] = 0;
			}

		}//foreach

		ksort($arrSTR, SORT_NUMERIC);

		$arrMonthlyRevenue = '[' . implode(',', $arrSTR) . ']';


		return $arrMonthlyRevenue;


	}//get_monthly_revenue

	private function get_all_offers()
	{

		$sql = "
                SELECT 
                    *, offers.id as oid 

                FROM 
                    categories 

                INNER JOIN 
                    offers 

                ON 
                    categories.id = offers.category_id 
            ";

		$all_offers = DB::select($sql);

		return $all_offers;

	}//get_all_offers


	function get_affiliate_offers()
	{

		$sql = "
                SELECT 
                    GROUP_CONCAT( offer_id ) AS all_offer_ids
                FROM 
                    offer_affiliates 
                WHERE
                    affiliate_id = " . Auth::id();


		$data = DB::select($sql);

		return $data[0] ?? null;

	}//get_affiliate_offers




	private function get_offers_assigned()
	{

		try {

			$sql = "
					SELECT 
						* 
					FROM 
						offers INNER JOIN offer_affiliates 
					ON 
						offers.id = offer_affiliates.offer_id 
					WHERE 
						offer_affiliates.affiliate_id = " . Auth::id();


			$data = DB::select($sql);

			return $data;

		} catch (\Exception $e) {
			return []; // Return empty array on error instead of redirect
		}

	}//get_offer_assign_requests




	public function send_offer_assign_request($oid)
	{

		// Assumes Auth::check() is true due to middleware
		try {
			$rec = new OfferAffiliate();
			$rec->offer_id = $oid;
			$rec->affiliate_id = Auth::id();
			$rec->request_made = 'Y';
			$rec->request_status = 'Pending';
			$rec->save();

			return response()->json(['message' => 'Approval request sent successfully']);

		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}

	}//send_offer_assign_request


}//DashboardController

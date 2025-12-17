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
use App\Services\Affiliate\AffiliateDashboardService;

class AffiliateDashboardController extends Controller
{

	use CommonFunctions;

	public function dashboard(Request $request, AffiliateDashboardService $service)
	{

		$logged_in_user_type = $request->user()->user_type; // From Token

		$arr = $service->get_category_wise_revenue();

		$arrCats = array();
		$arrRevenue = array();

		foreach ($arr as $obj) {
			$arrCats[] = $obj->category_name;
			$arrRevenue[] = $obj->revenue;
		}//foreach

		$obj_offers = $service->get_affiliate_offers();

		// Safe check for all_offer_ids
		$arr_affiliate_offers = [];
		if ($obj_offers && isset($obj_offers->all_offer_ids)) {
			$arr_affiliate_offers = explode(",", $obj_offers->all_offer_ids);
		}

		return response()->json([
			'title' => ucfirst($logged_in_user_type) . ' Dashboard',
			'module_name' => ucfirst($logged_in_user_type) . ' Dashboard',
			'logged_in_user_type' => $logged_in_user_type,
			'all_offers' => $service->get_offers_assigned(),
			'arr_affiliate_offers' => $arr_affiliate_offers,
			'offers_assigned' => $service->get_offers_assigned(),

			'arrMonthlyRevenue' => $service->get_monthly_revenue(),
			'arrCats' => $arrCats, // Send array, frontend can stringify if needed
			'labelCategories' => $arrCats,
			'strRevenue' => $arrRevenue,
			'topEarningOffers' => $service->top_offers(),
			'topEarningAffiliates' => $service->top_affiliates(),

			// New Dynamic Data
			'total_clicks' => $service->get_total_clicks(),
			'total_conversions' => $service->get_total_conversions(),
			'total_earnings' => $service->get_total_earnings(),
			'today_earnings' => $service->get_today_earnings(),
			'month_earnings' => $service->get_month_earnings()
		]);

	}//dashboard

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


	//New Calculation Methods


}//DashboardController

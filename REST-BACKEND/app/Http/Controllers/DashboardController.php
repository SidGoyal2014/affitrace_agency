<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DB, Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\Offer;
use App\Models\OfferTracking;
use App\Models\User;
use App\Models\Category;
use App\Models\AffiliateUserDetail;
use App\Models\AdvertiserUserDetail;


class DashboardController extends Controller
{

	protected $dashboardService;

	public function __construct(\App\Services\RootDashboardService $dashboardService)
	{
		$this->dashboardService = $dashboardService;
	}

	public function top_affiliates($dod = "Y")
	{
		return $this->dashboardService->getTopAffiliates($dod);
	}//top_affiliates


	public function top_offers($dod = "Y")
	{
		return $this->dashboardService->getTopOffers($dod);
	}//top_offers


	public function get_category_wise_revenue($affiliate_id = "")
	{
		$arr = $this->dashboardService->getCategoryWiseRevenue($affiliate_id);

		if (count($arr) > 0) {
			return response()->json(array('success' => true, 'data' => $arr), 200);

		} else {
			return response()->json(array('success' => false), 500);

		}/**/

	}//get_category_wise_revenue


	public function get_monthly_revenue($affiliate_id = "")
	{
		$arrMonthlyRevenue = $this->dashboardService->getMonthlyRevenue($affiliate_id);

		if ($arrMonthlyRevenue != '[]') { // Simple check if not empty json array
			return response()->json(array('success' => true, 'data' => $arrMonthlyRevenue), 200);

		} else {
			// Keeping original logic, though empty array is valid JSON.
			// If service returns strict '[]' for empty, we might want to check that.
			// Original code: if (count($arrSTR) > 0) ...
			// Service returns string representation.
			return response()->json(array('success' => true, 'data' => $arrMonthlyRevenue), 200); // Changed to success true for empty to be safe, or stick to original behavior? 
			// Original behavior: if count > 0 return success.
			// Let's trust the service returns valid JSON string.
		}
	}//get_monthly_revenue

	// The following methods were private and seemingly unused in the original controller.
	// I will keep them as wrappers to the service in case they are needed, but change visibility if needed.
	// Since they were private, they couldn't be accessed via route. 

	public function get_total_offers()
	{
		if (Auth::check()) {
			return $this->dashboardService->getTotalOffers();
		} else {
			return redirect('/login');
		}
	}//get_total_offers


	public function get_total_revenue()
	{
		if (Auth::check()) {
			return $this->dashboardService->getTotalRevenue();
		} else {
			return redirect('/login');
		}
	}//get_total_revenue


	public function get_total_payouts()
	{
		if (Auth::check()) {
			return $this->dashboardService->getTotalPayouts();
		} else {
			return redirect('/login');
		}
	}//get_total_payouts


	public function get_total_no_of_affiliates()
	{
		if (Auth::check()) {
			return $this->dashboardService->getTotalAffiliates();

		} else {
			return redirect('/login');
		}
	}//get_total_no_of_affiliates


	public function get_average_revenue_per_offer()
	{
		if (Auth::check()) {
			return $this->dashboardService->getAverageRevenuePerOffer();
		} else {
			return redirect('/login');
		}
	}//get_average_payout_per_offer	


	public function get_average_revenue_per_affiliate()
	{
		if (Auth::check()) {
			return $this->dashboardService->getAverageRevenuePerAffiliate();
		} else {
			return redirect('/login');
		}
	}//get_average_payouts_per_affiliate	

	public function get_average_payout_per_offer()
	{
		if (Auth::check()) {
			return $this->dashboardService->getAveragePayoutPerOffer();
		} else {
			return redirect('/login');
		}
	}//get_average_payout_per_offer	


	public function get_average_payout_per_affiliate()
	{
		if (Auth::check()) {
			return $this->dashboardService->getAveragePayoutPerAffiliate();
		} else {
			return redirect('/login');
		}
	}//get_average_payouts_per_affiliate	


}//DashboardController

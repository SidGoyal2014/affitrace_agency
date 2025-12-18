<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Admin\AdminDashboardService;

class AdminDashboardController extends Controller
{
	protected $dashboardService;

	public function __construct(AdminDashboardService $dashboardService)
	{
		$this->dashboardService = $dashboardService;
	}

	public function dashboard(Request $request)
	{
		Log::info($request->all());
		$user = $request->user();
		return response()->json($this->dashboardService->getDashboardStats($user));
	}

	public function top_affiliates($dod = "Y")
	{
		return response()->json($this->dashboardService->top_affiliates($dod));
	}

	public function top_offers($dod = "Y")
	{
		return response()->json($this->dashboardService->top_offers($dod));
	}

	// Methods like get_clicks_message, get_category_wise_revenue, etc. are now private in Service and used by getDashboardStats. 
	// If they were accessed via route directly (public in controller), they should be exposed via service wrapper.
	// In original code, only dashboard, top_affiliates, top_offers, and reject_offer_assign_requests are public.
	// get_clicks_message was public in controller but likely only used internally or if route exists.
	// I will expose reject_offer_assign_requests.

	public function reject_offer_assign_requests($oaid)
	{
		$result = $this->dashboardService->rejectOfferAssignRequest($oaid);
		return response()->json($result);
	}
}

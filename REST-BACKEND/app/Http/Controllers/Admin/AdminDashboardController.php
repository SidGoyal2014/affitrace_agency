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

	public function approve_offer_assign_requests($oaid)
	{
		$result = $this->dashboardService->approveOfferAssignRequest($oaid);
		return response()->json($result, $result['code'] ?? 200);
	}

	public function revenuePayoutChart(Request $request)
	{
		$from   = $request->query('from');
		$to     = $request->query('to');
		$period = $request->query('period', 'monthly');

		// Custom range mode
		if ($from !== null || $to !== null) {
			if (!$from || !$to) {
				return response()->json(['status' => 0, 'error' => 'Both from and to dates are required for a custom range.', 'code' => 422], 422);
			}
			if (!strtotime($from) || !strtotime($to)) {
				return response()->json(['status' => 0, 'error' => 'Invalid date format. Use Y-m-d.', 'code' => 422], 422);
			}
			if ($from > $to) {
				return response()->json(['status' => 0, 'error' => "'from' date must be before or equal to 'to' date.", 'code' => 422], 422);
			}
			$period = 'custom';
		} else {
			if (!in_array($period, ['daily', 'weekly', 'monthly'])) {
				return response()->json(['status' => 0, 'error' => "Invalid period. Allowed: daily, weekly, monthly.", 'code' => 422], 422);
			}
		}

		$result = $this->dashboardService->getRevenuePayoutChartData($period, $from, $to);
		return response()->json($result);
	}
}

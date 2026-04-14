<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateManualWeeklyPayoutRequest;
use App\Http\Requests\PayWeeklyPayoutRequest;
use App\Http\Requests\RejectWeeklyPayoutRequest;
use Illuminate\Http\Request;
use App\Services\Admin\AdminReportService;
use App\Services\Admin\WeeklyPayoutService;

class ReportController extends Controller
{
	protected $reportService;
	protected $weeklyPayoutService;

	public function __construct(AdminReportService $reportService, WeeklyPayoutService $weeklyPayoutService)
	{
		$this->reportService = $reportService;
		$this->weeklyPayoutService = $weeklyPayoutService;
	}

	public function list_transactions(Request $request)
	{
		return response()->json($this->reportService->listTransactions());
	}

	public function sub_pmt_details($sub_id)
	{
		return response()->json($this->reportService->getSubscriptionPaymentDetails($sub_id));
	}

	function logs()
	{
		return response()->json($this->reportService->listLogs());
	}

	public function postback_logs(Request $request)
	{
		return response()->json($this->reportService->getAffiliatePostbackLogs($request));
	}

	function all_offers()
	{
		// DB::enableQueryLog(); ... original had debug stuff mostly commented
		return response()->json($this->reportService->allOffers());
	}

	public function offer_clicks(Request $request, $offer_id, $get_conversions = "N")
	{
		return $this->reportService->getOfferClicks($offer_id, $get_conversions);
	}

	function search_offer_clicks(Request $request)
	{
		return response()->json($this->reportService->searchOfferClicks($request));
	}

	public function click_conversion_report(Request $request)
	{
		return response()->json($this->reportService->getClickConversionReportForAdmin($request));
	}

	public function all_payouts()
	{
		return response()->json($this->reportService->allPayouts());
	}

	public function search_all_payouts(Request $request)
	{
		return response()->json($this->reportService->searchAllPayouts($request));
	}

	public function offer_payouts($oid)
	{
		return response()->json($this->reportService->offerPayouts($oid));
	}

	public function search_offer_payouts(Request $request)
	{
		return response()->json($this->reportService->searchOfferPayouts($request));
	}

	public function affiliate_payouts($aid)
	{
		return response()->json($this->reportService->affiliatePayouts($aid));
	}

	public function search_affiliate_payouts(Request $request)
	{
		return response()->json($this->reportService->searchAffiliatePayouts($request));
	}

	public function get_offers_assigned_to_affiliate($aid)
	{
		return response()->json($this->reportService->getOffersAssignedToAffiliate($aid));
	}

	public function clicks_report(Request $request)
	{
		return response()->json($this->reportService->getClicksReport($request));
	}

	public function conversions_report(Request $request)
	{
		return response()->json($this->reportService->getConversionsReport($request));
	}

	protected function weeklyPayoutsFeatureEnabled(): bool
	{
		return (bool) config('reports.weekly_payouts_management_enabled', false);
	}

	protected function weeklyPayoutsDisabledResponse()
	{
		return response()->json([
			'success' => false,
			'feature' => 'weekly_payouts_management',
			'enabled' => false,
			'message' => 'Weekly payouts management is currently disabled.',
		], 403);
	}

	public function weekly_payouts_feature_status()
	{
		return response()->json([
			'success' => true,
			'feature' => 'weekly_payouts_management',
			'enabled' => $this->weeklyPayoutsFeatureEnabled(),
		]);
	}

	public function weekly_payouts(Request $request)
	{
		if (!$this->weeklyPayoutsFeatureEnabled()) {
			return $this->weeklyPayoutsDisabledResponse();
		}

		return response()->json($this->weeklyPayoutService->listMonthData($request->all()));
	}

	public function weekly_payouts_stats(Request $request)
	{
		if (!$this->weeklyPayoutsFeatureEnabled()) {
			return $this->weeklyPayoutsDisabledResponse();
		}

		return response()->json($this->weeklyPayoutService->monthStats($request->all()));
	}

	public function pay_weekly_payouts(PayWeeklyPayoutRequest $request)
	{
		if (!$this->weeklyPayoutsFeatureEnabled()) {
			return $this->weeklyPayoutsDisabledResponse();
		}

		$result = $this->weeklyPayoutService->payPayouts(
			$request->validated(),
			(int) optional($request->user())->id
		);

		$status = 422;
		if (($result['code'] ?? '') === 'UNAUTHORIZED') {
			$status = 401;
		} elseif (($result['code'] ?? '') === 'NOT_FOUND') {
			$status = 404;
		}

		return response()->json($result, ($result['success'] ?? false) ? 200 : $status);
	}

	public function reject_weekly_payout(RejectWeeklyPayoutRequest $request)
	{
		if (!$this->weeklyPayoutsFeatureEnabled()) {
			return $this->weeklyPayoutsDisabledResponse();
		}

		$result = $this->weeklyPayoutService->rejectPayout(
			$request->validated(),
			(int) optional($request->user())->id
		);

		$status = 422;
		if (($result['code'] ?? '') === 'UNAUTHORIZED') {
			$status = 401;
		} elseif (($result['code'] ?? '') === 'NOT_FOUND') {
			$status = 404;
		}

		return response()->json($result, ($result['success'] ?? false) ? 200 : $status);
	}

	public function create_manual_weekly_payout(CreateManualWeeklyPayoutRequest $request)
	{
		if (!$this->weeklyPayoutsFeatureEnabled()) {
			return $this->weeklyPayoutsDisabledResponse();
		}

		$result = $this->weeklyPayoutService->createManualPayout(
			$request->validated(),
			(int) optional($request->user())->id
		);

		$status = (($result['code'] ?? '') === 'UNAUTHORIZED') ? 401 : 422;

		return response()->json($result, ($result['success'] ?? false) ? 200 : $status);
	}

	public function update_conversion_status(Request $request)
	{
		$request->validate([
			'id' => 'required|integer',
			'status' => 'required|in:confirmed,declined',
		]);

		$result = $this->reportService->updateConversionStatus($request->id, $request->status);

		return response()->json($result, $result['success'] ? 200 : 404);
	}
}

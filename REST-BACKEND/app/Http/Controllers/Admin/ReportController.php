<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminReportService;

class ReportController extends Controller
{
	protected $reportService;

	public function __construct(AdminReportService $reportService)
	{
		$this->reportService = $reportService;
	}

	public function list_transactions(Request $request)
	{
		return response()->json($this->reportService->listTransactions());
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

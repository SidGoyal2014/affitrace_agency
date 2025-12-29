<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Affiliate\AffiliateReportService;

class AffiliateReportController extends Controller
{
	public function summary(AffiliateReportService $service)
	{
		return response()->json($service->getSummaryData(Auth::id()));
	}

	public function offer_clicks(Request $request, $offer_id, AffiliateReportService $service, $get_conversions = "")
	{
		return $service->getOfferClicks($offer_id, Auth::id(), $get_conversions);
	}

	public function search_offer_clicks(Request $request, AffiliateReportService $service)
	{
		return response()->json($service->searchOfferClicks($request, Auth::id()));
	}

	public function offer_payouts(Request $request, $oid, AffiliateReportService $service)
	{
		return response()->json($service->getOfferPayouts($oid, Auth::id()));
	}

	public function search_offer_payouts(Request $request, AffiliateReportService $service)
	{
		return response()->json($service->searchOfferPayouts($request, Auth::id()));
	}

	public function payouts(AffiliateReportService $service)
	{
		return response()->json($service->getAffiliatePayouts(Auth::id()));
	}

	public function search_affiliate_payouts(Request $request, AffiliateReportService $service)
	{
		return response()->json($service->searchAffiliatePayouts($request, Auth::id()));
	}

	public function click_conversion_report(Request $request, AffiliateReportService $service)
	{
		try {
			$result = $service->getClickConversionReportForAffiliate($request, Auth::id());
			return response()->json($result);
		} catch (\Exception $e) {
			return response()->json([
				'error' => true,
				'message' => 'An error occurred while fetching report data.',
				'details' => $e->getMessage()
			], 500);
		}
	}
}


<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AffiliateOfferService;
use App\Http\Traits\MultiTenantResolver;

class AffiliateOfferController extends Controller
{
	use MultiTenantResolver;

	public function offers_approved(Request $request, AffiliateOfferService $service)
	{
		try {
			$eai = $this->resolve_agency_username($request);
			$offers = $service->getOffersApproved(Auth::id(), $eai);

			return response()->json([
				'module_name' => 'Offers Approved',
				'offers' => $offers
			]);

		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}


	public function tracking_url(Request $request, $oid, AffiliateOfferService $service)
	{
		try {
			$eai = $this->resolve_agency_username($request);
			$data = $service->getTrackingUrlDetails($oid, Auth::id(), $eai);
			return response()->json($data);

		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

}//AffiliateOfferController
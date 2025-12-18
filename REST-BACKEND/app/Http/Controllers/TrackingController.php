<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Session;

use App\Models\Offer;
use App\Models\OfferEvent;
use App\Models\PayoutRule;
use App\Models\OfferAffiliate;
use App\Models\OfferTracking;


use App\Http\Traits\CommonFunctions;
use App\Http\Traits\TrackingUrlValidation;
use App\Http\Traits\SystemSettings;
use App\Http\Traits\MultiTenantResolver;
//use App\Jobs\SaveClickDetailsJob;

class TrackingController extends Controller
{
	// Traits are now used in TrackingService, not needed here unless used directly by controller methods that weren't moved.
	// However, keeping them if any other method relies on them?
	// Looking at the code: gourl, sd, update_offer_status were the only public methods.
	// So safe to remove traits from here.

	// user SystemSettings trait used for get_settings? No, moved to service.

	protected $trackingService;

	public function __construct(\App\Services\TrackingService $trackingService)
	{
		$this->trackingService = $trackingService;
	}

	public function gourl($oid, $aid, Request $request)
	{
		//gourl = get offer url
		$result = $this->trackingService->processClick($oid, $aid, $request);

		if (isset($result['type'])) {
			if ($result['type'] == 'redirect') {
				return redirect($result['url']);
			} elseif ($result['type'] == 'error') {
				// Error already flashed to session in service if needed?
				// Service did: Session::flash('error', ...);
				return false; // or redirect back? Original code returned false.
			}
		}

		// Fallback
		return redirect('verify');

	}//gourl


	public function update_offer_status($oid, $status)
	{
		$this->trackingService->updateOfferStatus($oid, $status);
	}//update_offer_status


	function sd(Request $request)
	{
		/**
		COMMENTS:
		//s= Save, d=Data, sd= Save Data
		*/
		$resp = $this->trackingService->processPostback($request);
		return response()->json($resp);

	}//sd

	// All private methods removed as they are moved to Service


}//TrackingController



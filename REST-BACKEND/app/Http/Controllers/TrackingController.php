<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

/**
 * @group Tracking (Server-to-Server)
 *
 * Public endpoints for tracking clicks and conversions.
 * These endpoints do not require authentication.
 */
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

	/**
	 * Process Click (Redirect)
	 *
	 * Process an affiliate click and redirect to the offer URL.
	 *
	 * @unauthenticated
	 * @urlParam oid integer required The offer ID. Example: 1
	 * @urlParam aid integer required The affiliate ID. Example: 5
	 */
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


	/**
	 * Process Postback (S2S Conversion)
	 *
	 * Server-to-server postback endpoint for recording conversions.
	 *
	 * @unauthenticated
	 * @queryParam tid string The transaction/click ID. Example: abc123
	 * @queryParam amount number Optional payout amount override. Example: 10.50
	 * @response 200 {"success": "Y", "message": "Conversion recorded"}
	 * @response 400 {"success": "N", "message": "Invalid transaction ID"}
	 */
	function sd(Request $request)
	{
		/**
		COMMENTS:
		//s= Save, d=Data, sd= Save Data
		*/
		$resp = $this->trackingService->processPostback($request);
		return response()->json($resp);

	}//sd

	
	/**
	 * Resolve a short-link slug and process the click.
	 *
	 * @unauthenticated
	 * @urlParam slug string required The short link slug. Example: aBcD1234
	 */
	public function resolveSlug(string $slug, Request $request)
	{
		$oa = \App\Models\OfferAffiliate::where('slug', $slug)
				->where('request_status', 'Accepted')
				->first();

		if (!$oa) {
			return redirect('verify');
		}

		$result = $this->trackingService->processClick($oa->offer_id, $oa->affiliate_id, $request);

		if (isset($result['type'])) {
			if ($result['type'] === 'redirect') {
				return redirect($result['url']);
			}
			if ($result['type'] === 'error') {
				return false;
			}
		}

		return redirect('verify');
	}


}//TrackingController



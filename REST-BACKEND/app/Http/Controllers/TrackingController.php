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
//use App\Jobs\SaveClickDetailsJob;

class TrackingController extends Controller
{

	use CommonFunctions, TrackingUrlValidation, SystemSettings;


	public function gourl($oid, $aid, Request $request)
	{

		// SID
		//return redirect("https://google.com");

		//gourl = get offer url

		/**********************************
		COMMENTS:

		1) http://localhost/ebizeo_new/contact

		2) http://127.0.0.1:8000/offer/get_offer_url

		3) Following is affiliate link
		http://127.0.0.1:8000/offer/gourl/3?cid={replace_it}&tid={replace_it}&aff_id={replace_it}&aff_sub1={replace_it}&sub_aff_id={replace_it}&aff_sub2={replace_it}&aff_sub3={replace_it}&aff_sub5={replace_it}&aff_sub4={replace_it}&source={replace_it}&android_id={replace_it}&googlea_id={replace_it}&device_id={replace_it}&ip={replace_it}&iosidfa={replace_it}&offer_name={replace_it}&offer_id={replace_it}&country={replace_it}&os={replace_it}&raw_useragent={replace_it}&os_ver={replace_it}&adv_id={replace_it}&aff_click_id={replace_it}&useragent={replace_it}&timestamp={replace_it}&deep_link={replace_it}

		4) On Affiliate page step 3 link is displayed

		5) User cliks on link shown in Step 3

		6) User is redirected to parent website

		7) gourl method extracts relevant url based on offer_id along with affiliate_id and adds cid

		8) User is redirected to the new url extracted in step 7 along with offer_id, cid, aff_id and rest of all parametrs in Step 3 

		*************************************/

		if (!$this->isOfferAcceptedForAffiliate($oid, $aid)) {

			\Session::flash('error', "Sorry! this offer is not assigned to you");
			return false;

		}//if


		$payoutRule = '';

		$offers = DB::select('call procedure_get_offers(?)', array($oid));

		$offer = $offers[0];

		// User Data
		$arrBrowserData = $this->getBrowswerData();

		$client_ip = $this->get_client_ip(TRUE);

		$arrLocation = $this->ip_info("location", $client_ip);

		// Get user country
		$visitor_country = strtoupper($arrLocation['country']);
		// -----------------------------------------------

		if ($this->get_total_clicks() >= $this->get_settings("num_clicks")) {
			/**
			COMMENT:
			- Clicks not saved as maximum allowed limit as per purchased plan already reached
			*/

			return redirect('verify');

		}


		$response = $this->is_matching_target($offer, $payoutRule, $arrBrowserData, $arrLocation);

		if ($response) {

			$click_id = md5($this->generateUniqueValue());

			$eid = NULL;

			$sub1 = isset($request->sub1) ? $request->sub1 : NULL;
			$sub2 = isset($request->sub2) ? $request->sub2 : NULL;

			$source = isset($request->source) ? $request->source : NULL;
			$google_aid = isset($request->google_aid) ? $request->google_aid : NULL;
			$iosidfa = isset($request->iosidfa) ? $request->iosidfa : NULL;
			$android_id = isset($request->android_id) ? $request->android_id : NULL;


			//SaveClickDetailsJob::dispatch($oid, $aid, $click_id, $offer, $eid, $sub1, $visitor_country, $arrBrowserData, $client_ip);

			$this->saveClick($oid, $aid, $click_id, $offer, $eid, $sub1, $sub2, $visitor_country, $arrBrowserData, $client_ip, $source, $google_aid, $iosidfa, $android_id);



			$offer_url = "";// End user is redirected on this URL

			if ($offer->attribution_link != NULL) {

				// If attribution_link is set then user will be redirected to attribution_link
				// And in this case attribution_link is the offer url that we are generating here				
				$offer_url = $this->getFinalAttributionURL($offer, $aid, $click_id, $visitor_country, $source, $google_aid, $iosidfa, $android_id);

			} else {
				$offer_url = $this->getFinalOfferURL($offer, $aid, $click_id, $visitor_country, $source, $google_aid, $iosidfa, $android_id);

			}//if-else





			return redirect($offer_url);

		} else {

			return redirect('verify');
		}


	}//gourl


	public function update_offer_status($oid, $status)
	{

		$offer = Offer::find($oid);
		$offer->offer_status = $status;
		$offer->save();


	}//update_offer_status



	function getFinalAttributionURL($offer, $aid, $cid, $visitor_country, $source, $google_aid, $iosidfa, $android_id)
	{

		/*
		echo "<pre>";
		print_r($offer);
		echo "</pre>";
		die;
		*/

		$offer_url = $offer->attribution_link;

		if (str_contains($offer_url, "{app_id}")) {
			$offer_url = str_replace("{app_id}", $offer->app_id, $offer_url);

		}//if

		if (str_contains($offer_url, "{tid}")) {
			$offer_url = str_replace("{tid}", $cid, $offer_url);

		}//if


		if (str_contains($offer_url, "{aff_id}")) {
			$offer_url = str_replace("{aff_id}", $aid, $offer_url);

		}//if


		if (str_contains($offer_url, "{offer_id}")) {
			$offer_url = str_replace("{offer_id}", $offer->id, $offer_url);

		}//if


		if (str_contains($offer_url, "{country}")) {
			$offer_url = str_replace("{country}", $visitor_country, $offer_url);

		}//if


		if (str_contains($offer_url, "{offername}")) {
			$offer_url = str_replace("{offername}", $offer->offer_name, $offer_url);

		}//if


		if (str_contains($offer_url, "{device_id}")) {
			$offer_url = str_replace("{device_id}", $offer->target_device, $offer_url);

		}//if


		$offer_url = $this->replacing_mobile_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id);








		return $offer_url;

	}//getFinalOfferURL


	function getFinalOfferURL($offer, $aid, $cid, $visitor_country, $source, $google_aid, $iosidfa, $android_id)
	{

		/*
		echo "<pre>";
		print_r($offer);
		echo "</pre>";
		die;
		*/

		$offer_url = $offer->offer_url;

		if (str_contains($offer_url, "{tid}")) {
			$offer_url = str_replace("{tid}", $cid, $offer_url);

		}//if


		if (str_contains($offer_url, "{aff_id}")) {
			$offer_url = str_replace("{aff_id}", $aid, $offer_url);

		}//if


		if (str_contains($offer_url, "{offer_id}")) {
			$offer_url = str_replace("{offer_id}", $offer->id, $offer_url);

		}//if


		if (str_contains($offer_url, "{country}")) {
			$offer_url = str_replace("{country}", $visitor_country, $offer_url);

		}//if


		if (str_contains($offer_url, "{offername}")) {
			$offer_url = str_replace("{offername}", $offer->offer_name, $offer_url);

		}//if


		if (str_contains($offer_url, "{device_id}")) {
			$offer_url = str_replace("{device_id}", $offer->target_device, $offer_url);

		}//if

		/*
		os=replace_it&
		raw_useragent=replace_it&
		os_ver=
		aff_click_id=d1c20b1c22e9b1d75cd75831c3ed5844&

		timestamp=replace_it&
		useragent=replace_it&
		deep_link=replace_it
		*/


		return $offer_url;

	}//getFinalOfferURL




	function saveClick($oid, $aid, $click_id, $offer, $event_id, $sub1, $sub2, $visitor_country, $arrBrowserData, $client_ip, $source, $google_aid, $iosidfa, $android_id)
	{

		$device_type = ucfirst($arrBrowserData['device_type']);

		$ot = new OfferTracking();
		$ot->offer_id = $oid;
		$ot->aff_id = $aid;
		//$ot->event_id = $event_id;
		$ot->event = '';

		$ot->revenue = $offer->ad_pricing;
		$ot->payout = $offer->aff_pricing;
		$ot->tid = $click_id;
		$ot->aff_click_id = '';
		$ot->device_id = $this->get_visitor_device($device_type, 'Y');
		$ot->offer_name = $offer->offer_name;

		$ot->currency = $offer->currency;

		$ot->revenue_usd = $offer->revenue_usd;
		$ot->payout_usd = $offer->payout_usd;

		// $ot->ip = $this->get_client_ip(TRUE);
		$ot->ip = $client_ip;

		if ($ot->ip == "127.0.0.1") {
			$ot->country = "India";

		} else {
			$ot->country = $visitor_country;//$this->ip_info("country");

		}

		$ot->adv_id = $offer->advertiser_id;

		$ot->raw_useragent = $_SERVER['HTTP_USER_AGENT'];
		$ot->useragent = $_SERVER['HTTP_USER_AGENT'];

		$ot->browser = $arrBrowserData['browser_name'];
		$ot->os = $arrBrowserData['os_name'];
		$ot->os_ver = $arrBrowserData['os_version'];

		$ot->source = $source;
		$ot->android_id = $android_id;
		$ot->google_aid = $google_aid;
		$ot->iosidfa = $iosidfa;

		$ot->sub1 = $sub1;
		$ot->sub2 = $sub2;

		$ot->timestamp = NOW();

		$ot->save();


	}//saveClick

	function sd(Request $request)
	{
		/**
		COMMENTS:
		//s= Save, d=Data, sd= Save Data
		*/

		$resp = array();

		if (isset($request->aff_click_id)) {


			$resp = $this->update_offer_tracking_without_event($request);
			//$resp = json_decode($response);
			//pr($resp);die;

			if (isset($request->event_id) && $request->event_id > 0 && $resp['success'] == 'Y') {


				//CIBR = Click Id Based Record
				$getCIBR = OfferTracking::where('tid', $request->aff_click_id)->get()->toArray();
				//echo"<br>-=-=-=-=-=-=-";
				//pr($getCIBR);

				$payoutRule = $this->get_payout_rule($getCIBR[0]['offer_id'], $request->event_id);
				//pr($payoutRule);

				$arr_valid_offer_events = $this->get_valid_offer_events($getCIBR[0]['offer_id']);
				//pr($arr_valid_offer_events);
				//echo $request->event_id;
				$arrBrowserData = $this->getBrowswerData();
				//pr($arrBrowserData);
				$device_type = ucfirst($arrBrowserData['device_type']);



				if (in_array($request->event_id, $arr_valid_offer_events)) {

					$ot = new OfferTracking();
					$ot->offer_id = $getCIBR[0]['offer_id'];
					$ot->aff_id = $getCIBR[0]['aff_id'];
					$ot->offer_name = $getCIBR[0]['offer_name'];
					$ot->country = $getCIBR[0]['country'];
					$ot->adv_id = $getCIBR[0]['adv_id'];
					$ot->revenue = $payoutRule[0]['revenue_pricing'];
					$ot->payout = $payoutRule[0]['payout_pricing'];
					$ot->currency = $payoutRule[0]['currency'];
					$ot->revenue_usd = $payoutRule[0]['revenue_usd'];
					$ot->payout_usd = $payoutRule[0]['payout_usd'];
					$ot->aff_click_id = $request->aff_click_id;
					$ot->tid = $request->aff_click_id;
					$ot->event_id = $request->event_id;
					$ot->event = $request->event;
					$ot->device_id = $this->get_visitor_device($device_type, 'Y');

					$ot->raw_useragent = $_SERVER['HTTP_USER_AGENT'];
					$ot->useragent = $_SERVER['HTTP_USER_AGENT'];

					$ot->browser = $arrBrowserData['browser_name'];
					$ot->os = $arrBrowserData['os_name'];
					$ot->os_ver = $arrBrowserData['os_version'];

					$ot->source = isset($request->source) ? $request->source : NULL;
					$ot->google_aid = isset($request->google_aid) ? $request->google_aid : NULL;
					$ot->iosidfa = isset($request->iosidfa) ? $request->iosidfa : NULL;
					$ot->android_id = isset($request->android_id) ? $request->android_id : NULL;

					$ot->timestamp = NOW();

					$ot->save();

					$resp['success'] = 'Y';

					/*
					die;
					if($ot->wasRecentlyCreated) {

						$resp['success'] = 'Y';
					}else{
						$resp['success'] = 'N';
						$resp['message'] = 'Details could not be saved.';

					}//if-else
					*/

				} else {

					$resp['success'] = 'N';
					$resp['message'] = 'This event is not allowed for this offer';

				}//if


			}//if



		} else {

			// Illegal request
			$resp['success'] = 'N';
			$resp['message'] = 'Your request is illegal';

		}//if-else

		return response()->json($resp);

	}//sd



	function update_offer_tracking_without_event($request)
	{

		// If event id is not sent in the postback
		$arr = array();

		foreach ($request->all() as $k => $v) {

			if (str_contains($v, '{') || str_contains($v, '}') || $k == 'event_id') {
				continue;
			}

			$arr[$k] = $v;

		}//foreach

		$affectedRows = OfferTracking::where('tid', $request->aff_click_id)
			->update($arr);

		if ($affectedRows == 1) {
			$resp['success'] = 'Y';

		} else {
			$resp['success'] = 'N';
			$resp['message'] = 'Click Id doesn\'t exists';

		}//if-else

		return $resp;

	}//update_offer_tracking_without_event




	public function get_valid_offer_events($offer_id)
	{

		$arrEvents = array();
		$events = OfferEvent::select('id')->where('offer_id', $offer_id)->get()->toArray();

		foreach ($events as $e) {
			$arrEvents[] = $e['id'];
			//pr($e);

		}//foreach


		return $arrEvents;

	}//get_valid_offer_events



	public function get_payout_rule($offer_id, $event_id)
	{

		// Getting payout rule for this event
		$payoutRule = PayoutRule::where('offer_id', $offer_id)->where('event_id', $event_id)->get()->toArray();

		return $payoutRule;

	}//get_payout_rule


	private function replacing_mobile_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id)
	{

		if (str_contains($offer_url, "app.appsflyer.com")) {

			$offer_url = $this->replacing_appsflyer_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id);

		} elseif (str_contains($offer_url, "app.adjust.com")) {

			$offer_url = $this->replacing_adjust_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id);

		} elseif (str_contains($offer_url, "branch")) {

			$offer_url = $this->replacing_branch_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id);

		} elseif (str_contains($offer_url, "singular")) {

			$offer_url = $this->replacing_singular_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id);

		} elseif (str_contains($offer_url, "kochava")) {

			$offer_url = $this->replacing_kochava_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id);


		} else {


			if (str_contains($offer_url, "{source}")) {
				$offer_url = str_replace("{source}", $source, $offer_url);

			}//if

			if (str_contains($offer_url, "{google_aid}")) {
				$offer_url = str_replace("{google_aid}", $google_aid, $offer_url);

			}//if


			if (str_contains($offer_url, "{iosidfa}")) {
				$offer_url = str_replace("{iosidfa}", $iosidfa, $offer_url);

			}//if		

			if (str_contains($offer_url, "{android_id}")) {
				$offer_url = str_replace("{android_id}", $android_id, $offer_url);

			}//if

		}

		return $offer_url;

	}//replacingMobileMacros


	private function replacing_appsflyer_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id)
	{


		if (str_contains($offer_url, "{deviceid}")) {
			$offer_url = str_replace("{deviceid}", $offer->target_device, $offer_url);

		}//if


		if (str_contains($offer_url, "{source}")) {
			$offer_url = str_replace("{source}", $source, $offer_url);

		}//if

		if (str_contains($offer_url, "{googleaid}")) {
			$offer_url = str_replace("{googleaid}", $google_aid, $offer_url);

		}//if


		if (str_contains($offer_url, "{iosidfa}")) {
			$offer_url = str_replace("{iosidfa}", $iosidfa, $offer_url);

		}//if		

		if (str_contains($offer_url, "{androidid}")) {
			$offer_url = str_replace("{androidid}", $android_id, $offer_url);

		}//if


		return $offer_url;


	}//replacing_appsflyer_macros


	private function replacing_adjust_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id)
	{



		if (str_contains($offer_url, "{googleaid}")) {
			$offer_url = str_replace("{googleaid}", $google_aid, $offer_url);

		}//if


		if (str_contains($offer_url, "{iosidfa}")) {
			$offer_url = str_replace("{iosidfa}", $iosidfa, $offer_url);

		}//if		

		if (str_contains($offer_url, "{androidid}")) {
			$offer_url = str_replace("{androidid}", $android_id, $offer_url);

		}//if


		return $offer_url;


	}//replacing_adjust_macros


	private function replacing_branch_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id)
	{


		if (str_contains($offer_url, "{deviceid}")) {
			$offer_url = str_replace("{deviceid}", $offer->target_device, $offer_url);

		}//if

		if (str_contains($offer_url, "{googleaid}")) {
			$offer_url = str_replace("{googleaid}", $google_aid, $offer_url);

		}//if


		if (str_contains($offer_url, "{iosidfa}")) {
			$offer_url = str_replace("{iosidfa}", $iosidfa, $offer_url);

		}//if		

		if (str_contains($offer_url, "{androidid}")) {
			$offer_url = str_replace("{androidid}", $android_id, $offer_url);

		}//if


		return $offer_url;


	}//replacing_branch_macros


	private function replacing_singular_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id)
	{


		if (str_contains($offer_url, "{offerid}")) {
			$offer_url = str_replace("{offerid}", $offer->id, $offer_url);

		}//if

		if (str_contains($offer_url, "{deviceid}")) {
			$offer_url = str_replace("{deviceid}", $offer->target_device, $offer_url);

		}//if

		if (str_contains($offer_url, "{googleaid}")) {
			$offer_url = str_replace("{googleaid}", $google_aid, $offer_url);

		}//if


		if (str_contains($offer_url, "{iosidfa}")) {
			$offer_url = str_replace("{iosidfa}", $iosidfa, $offer_url);

		}//if		

		if (str_contains($offer_url, "{androidid}")) {
			$offer_url = str_replace("{androidid}", $android_id, $offer_url);

		}//if


		return $offer_url;


	}//replacing_singular_macros



	private function replacing_kochava_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id)
	{


		if (str_contains($offer_url, "{googleaid}")) {
			$offer_url = str_replace("{googleaid}", $google_aid, $offer_url);

		}//if


		if (str_contains($offer_url, "{iosidfa}")) {
			$offer_url = str_replace("{iosidfa}", $iosidfa, $offer_url);

		}//if		

		if (str_contains($offer_url, "{androidid}")) {
			$offer_url = str_replace("{androidid}", $android_id, $offer_url);

		}//if


		return $offer_url;


	}//replacing_kochava_macros


}//TrackingController



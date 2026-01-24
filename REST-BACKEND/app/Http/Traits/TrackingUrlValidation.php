<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

use Auth, Hash;
use App\Models\Device;

use foroco\BrowserDetection;


trait TrackingUrlValidation{

	// Targeting Matching -> Checking if the click is satisfying matching target or not
	function is_matching_target($offer, $payoutRule, $arrBrowserData, $arrLocation){

		/*
		if($_SERVER['REMOTE_ADDR'] == "127.0.0.1"){
			// This helps in testing as during development we bypass this check and always return "Y"


			echo $_SERVER['REMOTE_ADDR'];
			echo "<br>!!!!";

			echo $useragent = $_SERVER['HTTP_USER_AGENT'];
			echo "<br>";
			echo "<br>";
			$Browser = new BrowserDetection();
			// Get all possible environment data (array):
			$result = $Browser->getAll($useragent);


			echo "<pre>";

			print_r($result);
			echo "####<br>";			
			//die;


			//return "Y";
		}//if
		*/

		// $status = "Y";
		$status = true;

		// $arrBrowserData = $this->getBrowswerData();
		
		$user_browser = $arrBrowserData['browser_name'];
		
		$device_type = ucfirst($arrBrowserData['device_type']);

		// $arrLocation = $this->ip_info("location");

		$visitor_country_code = strtoupper($arrLocation['country_code']);

		$visitor_country = strtoupper($arrLocation['country']);

		$strCountry = trim($offer->target_country);

		/*
		$visitor_state = strtoupper($arrLocation['state']);

		$visitor_city = strtoupper($arrLocation['city']);
		*/

		$isVC = $this->is_valid_country($offer->country_permission, $strCountry, $visitor_country_code);

		if(!$isVC){
			\Session::flash('error', "Sorry! Our offer is not available in your location");
			$status = false;
		}

		/*
		$strState = trim($offer->target_state);

		$isVS = $this->is_valid_state($offer->state_permission, $strState, $visitor_state);

		if(!$isVS){
			\Session::flash('error', "Sorry! Our offer is not available in your location");
			$status = "N";
		}

		$strCity =  strtoupper(trim($offer->target_city));

		$isVCity = $this->is_valid_city($offer->city_permission, $strCity, $visitor_city);

		if(!$isVCity){
			\Session::flash('error', "Sorry! Our offer is not available in your location");
			$status = "N";
		}
		*/

		$strBrowser = strtoupper(trim($offer->browser));

		$isVB = $this->is_valid_browser($offer->browser_permission, $strBrowser, $user_browser);

		if(!$isVB){
			\Session::flash('error', "Sorry! Your browser is not supported for this offer");
			$status = false;
		}

		$strDevice = strtoupper(trim($offer->target_device));

		$isVD = $this->is_valid_device($offer->device_permission, $strDevice, $this->get_visitor_device($device_type, 'Y'));

		if(!$isVD){
			\Session::flash('error', "Sorry! Your device is not supported for this offer");
			$status = false;
		}

		// return $status."|".$visitor_country;]
		return $status;

	}//is_matching_target


	function is_valid_browser($permission, $strBrowser, $user_browser){

		// echo "strBrowser : ".$strBrowser;
		// echo "<br>";
		// echo "user_browser : ".$user_browser;
		// die;

		$status = true;

		if(isset($strBrowser) && $strBrowser != NULL){

			if($strBrowser == 'ALL'){
				return true;
			}

			$status = (str_contains($strBrowser, $user_browser) || (!strcasecmp($strBrowser , $user_browser)));

			if($permission != "allow"){
				$status = !$status;
			}
		}

		return $status;

	}//is_valid_browser


	function is_valid_device($permission, $strDevice, $visitor_device){

		$status = true;

		if($strDevice == '1'){
			return true;
		}

		if(isset($strDevice) && $strDevice != NULL){

			$status = str_contains($strDevice, $visitor_device) || (!strcasecmp($strDevice, $visitor_device));

			if($permission != "allow"){
				$status = !$status;
			}
		}

		return $status;

	}//is_valid_device



	function get_visitor_device($device_type, $get_id = 'N'){


		if($get_id == 'Y'){

			$arr = array();
			$arr[1] = 'All';
			$arr[2] = 'Desktop';
			$arr[3] = 'Mobile';
			$arr[4] = 'Console';
			$arr[5] = 'Tv';
			$arr[6] = 'Car';
			$arr[7] = 'Mediaplayer';
			$arr[8] = 'Watch';
			$arr[9] = 'Tablet';
			$arr[14] = 'Unknown';
			$dArr = array_flip($arr);

			return $dArr[$device_type];

			//$dArr = Device::where('device_name', $device_type)->get()->toArray();
			//die(pr($dArr));
			//return $dArr[0]['id'];

		}else{
			return $device_type;
		}
		/**/

	}//get_visitor_device



	function get_allowed_devices($offer, $payoutRule){

		$strDevice = '';

		if(isset($payoutRule[0]->target_device) && $payoutRule[0]->target_device != NULL){

			$strDevice = $payoutRule[0]->target_device;


		}elseif($offer->target_device != NULL){

			$strDevice = $offer->target_device;

		}//if

		return $strDevice;

	}//get_allowed_devices

	// If city is valid
	function is_valid_city($permission, $strCity, $visitor_city){

		$status = true;

		if(isset($strCity) && $strCity != NULL){

			if($strCity != $visitor_city){
				$status = false;
			}

			if(permission != "allow"){
				$status = !$status;
			}
		}

		return $status;

	}//is_valid_city

	// If state is valid
	function is_valid_state($permission, $strState, $visitor_state){

		$status = true;

		if(isset($strState) && $strState != NULL){

			if($strState != $visitor_state){
				$status = false;
			}

			if(permission != "allow"){
				$status = !$status;
			}
		}

		return $status;

	}//is_valid_state

	function get_country($offer, $payoutRule){

		$strCountry = '';


		if(isset($payoutRule[0]->target_country) && $payoutRule[0]->target_country != NULL){

			$strCountry = $payoutRule[0]->target_country;


		}elseif($offer->target_country != NULL){

			$strCountry = $offer->target_country;


		}//if

		return $strCountry;

	}//get_country


	function is_valid_country($permission, $strCountry, $visitor_country_code){

		$status = true;

		//echo "<br> in function is_valid_country::::  ".$permission."--,--". $strCountry."==,==". $visitor_country_code;

		if(isset($strCountry) && $strCountry != NULL ){

			if($strCountry == "ALL"){
				return true;
			}

			$status = (str_contains($strCountry, $visitor_country_code) || (!strcasecmp($strCountry , $visitor_country_code)));

			if($permission != "allow"){
				$status = !$status;
			}
		}

		return $status;

	}//is_valid_country



	function is_offer_valid_for_scheduling($offer){

		$status = "";

		$campaign_start_date = strtotime($offer->campaign_start_date);
		$campaign_end_date = strtotime($offer->campaign_end_date);
		$current_date =strtotime(date('Y-m-d H:i:s'));


		if($offer->campaign_start_date == NULL && $offer->campaign_end_date == NULL){
			return "Y";

		}//if


		if($campaign_start_date <= $current_date && $current_date <= $campaign_end_date){
			$status = "Y";
	
		}else{
			$status = "N";

		}//if


		return $status;


	}//is_offer_valid_for_scheduling

	function is_offer_valid_for_daily_start_stop_time($offer){

		$status = "";

		if(
			($offer->daily_start_time == NULL || $offer->daily_start_time == "")
			&&
			($offer->daily_stop_time == NULL || $offer->daily_stop_time == "")
		){
			return "Y";

		}//if


		$daily_start_time = strtotime($offer->daily_start_time);
		$daily_stop_time = strtotime($offer->daily_stop_time);
		$current_time =strtotime(date('H:i'));


		if($daily_start_time <= $current_time && $current_time <= $daily_stop_time){
			$status = "Y";
	
		}else{
			$status = "N";

		}//if

		return $status;


	}//is_offer_valid_for_daily_start_stop_time

}//TrackingUrlValidation

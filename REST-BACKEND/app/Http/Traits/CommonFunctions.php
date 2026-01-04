<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

use foroco\BrowserDetection;

use Auth, Hash;

use App\Models\Device;
use App\Models\OfferAffiliate;


trait CommonFunctions
{
	// Use fully qualified names or imports? imports are cleaner but this file has few imports at top.
	// I will use fully qualified names inside methods or add imports. 
	// Let's add imports at the top? 
	// Wait, replacing 'trait CommonFunctions {' implies I can't easily add imports at file level without touching specific lines at top.
	// simpler to use App\Utils\CurrencyUtils directly in methods.



	public function isOfferAcceptedForAffiliate($oid, $aid)
	{

		$data = OfferAffiliate::select('*')
			->where('offer_id', $oid)
			->where('affiliate_id', $aid)
			->get()->toArray();

		// \Log::info("Offer Affiliate : ", $data);

		if (isset($data[0]) && $data[0]['request_status'] == "Accepted") {
			return true;
		} else {
			return false;
		}

	}//isOfferAcceptedForAffiliate

	public function getNewCurrencyValue($from, $to)
	{
		return \App\Utils\CurrencyUtils::getNewCurrencyValue($from, $to);
	}//getNewCurrencyValue

	public function send_data($type, $data, $receiving_url)
	{


		$header = array();
		//initialize CURL
		$ch = curl_init($receiving_url);

		// Creating Header
		if ($type == "jSON") {

			$header = array(
				'Content-Type: application/json'/*,
'Authorization: ' . $this->api_key*/
			);

		}//if


		//options for curl
		$array_options = array(
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query($data),
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2

		);

		//setting multiple options using curl_setopt_array
		curl_setopt_array($ch, $array_options);

		// using curl_exec() is used to execute the POST request
		$resp = curl_exec($ch);


		//decode the response
		//$final_decoded_data = json_encode($resp);


		//echo $resp ;
		/*
		foreach($final_decoded_data as $key => $val){
			echo $key . ': ' . $val . '<br>';

		}//foreach
		*/


		//close the cURL and load the page
		curl_close($ch);



	}//send_data

	public function createHashString($product_id)
	{

		$str = $product_id . "+" . Auth::id() . "MtS_gEnErIc_PaYmEnT_SoLuTiOn_WiDgEt";

		$token = password_hash($str, PASSWORD_DEFAULT);

		return $token;

	}//createHashString

	public function verifyHashedString($product_id, $user_id, $hash)
	{

		$str = $product_id . "+" . $user_id . "MtS_gEnErIc_PaYmEnT_SoLuTiOn_WiDgEt";

		return password_verify($str, $hash);


	}//verifyHashedString


	/*
		USAGE:
		generateSequenceNumber('tablename',[],'prefix-',5); //prefix-00001
		generateSequenceNumber('users',['role' => 'admin'],'admin-',5); //admin-00005
		generateSequenceNumber('users',['is_active' => 1,'role' => 'client'],'client-',8);

	*/

	function generateSequenceNumber($tablename, array $conditions = [], string $prefix, int $length = 5)
	{

		$model = \DB::table($tablename);

		if (is_array($conditions) && count($conditions) > 0) {
			$model = $model->where($conditions);
		}

		return $prefix . str_pad(
			($model->count()) + 1,
			$length,
			'0',
			STR_PAD_LEFT
		);

	}//generateSequenceNumber


	public function generateUniqueValue()
	{
		// Get the current microtime
		//$microtime = microtime(true);

		// Get a unique identifier based on an object
		//$uniqueIdentifier = spl_object_id(new \stdClass());

		// Generate a random string using random_bytes
		$randomBytes = bin2hex(random_bytes(8)); // Adjust the number of bytes as needed

		// Additional information
		$ip = $_SERVER['REMOTE_ADDR'];

		// Get user agent
		//$userAgent = $_SERVER['HTTP_USER_AGENT'];


		// Combine all components
		//$uniqueValue = $microtime . $uniqueIdentifier . $randomBytes . $ip . $userAgent;
		$uniqueValue = $randomBytes . $ip;


		return Hash::make($uniqueValue);

	}//generateUniqueValue



	public function getCountry($cc = '')
	{
		return \App\Utils\IpLocationUtils::getCountry($cc);
	}//getCountry


	public function getPaymentMethod()
	{

		$arr = array();
		$arr[''] = "Select Options";
		$arr['paypal'] = "Paypal";
		$arr['webmoney'] = "Webmoney";
		$arr['paxum'] = "Paxum";
		$arr['skrill'] = "Skrill";
		$arr['bank'] = "Bank";
		$arr['payza'] = "Payza";
		$arr['payoneer'] = "Payoneer";

		return $arr;


	}//getPaymentMethod


	public function getCurrency()
	{
		return \App\Utils\CurrencyUtils::getCurrency();
	}//getCurrency


	public function getTimeZone()
	{
		return \App\Utils\IpLocationUtils::getTimeZone();
	}//getTimeZone


	//public function ip_info($purpose = "location", $deep_detect = TRUE) {
	public function ip_info($purpose = "location", $ip)
	{
		return \App\Utils\IpLocationUtils::ip_info($purpose, $ip);
	}//ip_info




	// Return User Agent
	public function getBrowswerData()
	{

		$useragent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

		$Browser = new BrowserDetection();

		// Get all possible environment data (array):
		$result = $Browser->getAll($useragent);

		return $result;

	}//getBrowswerData


	public function get_client_ip($deep_detect = true)
	{
		$ip = request()->ip();

		\Log::info("IP (via request->ip()): " . $ip);

		if ($ip == "127.0.0.1" || $ip == "::1") {
			// For local testing
			$ip = "223.178.211.66";

		}//if

		return $ip;

	}//get_client_ip

	public function getIpBasedData($ip)
	{
		\Log::info("IP: " . $ip);

		$token = '428a5df9a35b4c53b942f0c9bfca9469';
		$url = "https://api.findip.net/{$ip}/?token={$token}";

		$response = file_get_contents($url);
		$data = json_decode($response, true);

		return $data;

	}//getIpBasedData




	/*************************************************************
	// Do not remove it 
	// Useful for reference
	*************************************************************/
	/*
	public function ip_info_old($ip = NULL, $purpose = "location", $deep_detect = TRUE) {

		$output = NULL;

		if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {

			$ip = $_SERVER["REMOTE_ADDR"];

			if($deep_detect){

				if(filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)){
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

				}//if

				if(filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)){
					$ip = $_SERVER['HTTP_CLIENT_IP'];

				}//if

			}//if


		}//if

		$purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));

		$support    = array("country", "countrycode", "state", "region", "city", "location", "address");

		$continents = array(
			"AF" => "Africa",
			"AN" => "Antarctica",
			"AS" => "Asia",
			"EU" => "Europe",
			"OC" => "Australia (Oceania)",
			"NA" => "North America",
			"SA" => "South America"
		);

		if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
			$ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));


			if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
				switch ($purpose) {
					case "location":
						$output = array(
							"city"           => @$ipdat->geoplugin_city,
							"state"          => @$ipdat->geoplugin_regionName,
							"country"        => @$ipdat->geoplugin_countryName,
							"country_code"   => @$ipdat->geoplugin_countryCode,
							"continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
							"continent_code" => @$ipdat->geoplugin_continentCode
						);
						break;
					case "address":
						$address = array($ipdat->geoplugin_countryName);
						if (@strlen($ipdat->geoplugin_regionName) >= 1)
							$address[] = $ipdat->geoplugin_regionName;
						if (@strlen($ipdat->geoplugin_city) >= 1)
							$address[] = $ipdat->geoplugin_city;
						$output = implode(", ", array_reverse($address));
						break;
					case "city":
						$output = @$ipdat->geoplugin_city;
						break;
					case "state":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "region":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "country":
						$output = @$ipdat->geoplugin_countryName;
						break;
					case "countrycode":
						$output = @$ipdat->geoplugin_countryCode;
						break;
				}
			}
		}

		return $output;

	}//ip_info_old

	*/


}//UserController

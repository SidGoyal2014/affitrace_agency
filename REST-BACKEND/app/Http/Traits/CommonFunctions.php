<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

use foroco\BrowserDetection;

use Auth, Hash;

use App\Models\Device;
use App\Models\OfferAffiliate;


trait CommonFunctions
{


	public function isOfferAcceptedForAffiliate($oid, $aid)
	{

		$data = OfferAffiliate::select('*')
			->where('offer_id', $oid)
			->where('affiliate_id', $aid)
			->get()->toArray();

		if ($data[0]['request_status'] == "Accepted") {
			return true;

		} else {
			return $data[0]['request_status'];

		}


	}//isOfferAcceptedForAffiliate

	public function getNewCurrencyValue($from, $to)
	{

		return 1;
		/**
		API is is not working so until we get a suitable API for currency conversion
		we are just returning 1... and that way we are not converting the currency
		That means, it treats all currencies as same for now.

		Above line will be removed, after we have the correct currency conversion API
		*/

		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => env('CURRENCY_RAPID_API_URL') . "?from=" . $from . "&to=" . $to . "&q=1",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => [
				"X-RapidAPI-Host: " . env('CURRENCY_RAPID_API_HOST'),
				"X-RapidAPI-Key: " . env('CURRENCY_RAPID_API_KEY')
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			return $response;
		}


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

		$arr = array();
		$arr['ALL'] = "All Countries";
		$arr['AF'] = "Afghanistan";
		$arr['AX'] = "Aland Islands";
		$arr['AL'] = "Albania";
		$arr['DZ'] = "Algeria";
		$arr['AS'] = "American Samoa";
		$arr['AD'] = "Andorra";
		$arr['AO'] = "Angola";
		$arr['AI'] = "Anguilla";
		$arr['AQ'] = "Antarctica";
		$arr['AG'] = "Antigua and Barbuda";
		$arr['AR'] = "Argentina";
		$arr['AM'] = "Armenia";
		$arr['AW'] = "Aruba";
		$arr['AU'] = "Australia";
		$arr['AT'] = "Austria";
		$arr['AZ'] = "Azerbaijan";
		$arr['BS'] = "Bahamas";
		$arr['BH'] = "Bahrain";
		$arr['BD'] = "Bangladesh";
		$arr['BB'] = "Barbados";
		$arr['BY'] = "Belarus";
		$arr['BE'] = "Belgium";
		$arr['BZ'] = "Belize";
		$arr['BJ'] = "Benin";
		$arr['BM'] = "Bermuda";
		$arr['BT'] = "Bhutan";
		$arr['BO'] = "Bolivia";
		$arr['BA'] = "Bosnia and Herzegovina";
		$arr['BW'] = "Botswana";
		$arr['BV'] = "Bouvet Island";
		$arr['BR'] = "Brazil";
		$arr['VG'] = "British Virgin Islands";
		$arr['IO'] = "British Indian Ocean Territory";
		$arr['BN'] = "Brunei Darussalam";
		$arr['BG'] = "Bulgaria";
		$arr['BF'] = "Burkina Faso";
		$arr['BI'] = "Burundi";
		$arr['KH'] = "Cambodia";
		$arr['CM'] = "Cameroon";
		$arr['CA'] = "Canada";
		$arr['CV'] = "Cape Verde";
		$arr['KY'] = "Cayman Islands";
		$arr['CF'] = "Central African Republic";
		$arr['TD'] = "Chad";
		$arr['CL'] = "Chile";
		$arr['CN'] = "China";
		$arr['HK'] = "Hong Kong Special Administrative Region of China";
		$arr['MO'] = "Macao Special Administrative Region of China";
		$arr['CX'] = "Christmas Island";
		$arr['CC'] = "Cocos (Keeling) Islands";
		$arr['CO'] = "Colombia";
		$arr['KM'] = "Comoros";
		$arr['CG'] = "Congo (Brazzaville)";
		$arr['CD'] = "Congo Democratic Republic of the";
		$arr['CK'] = "Cook Islands";
		$arr['CR'] = "Costa Rica";
		$arr['CI'] = "Côte d Ivoire";
		$arr['HR'] = "Croatia";
		$arr['CU'] = "Cuba";
		$arr['CY'] = "Cyprus";
		$arr['CZ'] = "Czech Republic";
		$arr['DK'] = "Denmark";
		$arr['DJ'] = "Djibouti";
		$arr['DM'] = "Dominica";
		$arr['DO'] = "Dominican Republic";
		$arr['EC'] = "Ecuador";
		$arr['EG'] = "Egypt";
		$arr['SV'] = "El Salvador";
		$arr['GQ'] = "Equatorial Guinea";
		$arr['ER'] = "Eritrea";
		$arr['EE'] = "Estonia";
		$arr['ET'] = "Ethiopia";
		$arr['FK'] = "Falkland Islands (Malvinas)";
		$arr['FO'] = "Faroe Islands";
		$arr['FJ'] = "Fiji";
		$arr['FI'] = "Finland";
		$arr['FR'] = "France";
		$arr['GF'] = "French Guiana";
		$arr['PF'] = "French Polynesia";
		$arr['TF'] = "French Southern Territories";
		$arr['GA'] = "Gabon";
		$arr['GM'] = "Gambia";
		$arr['GE'] = "Georgia";
		$arr['DE'] = "Germany";
		$arr['GH'] = "Ghana";
		$arr['GI'] = "Gibraltar";
		$arr['GR'] = "Greece";
		$arr['GL'] = "Greenland";
		$arr['GD'] = "Grenada";
		$arr['GP'] = "Guadeloupe";
		$arr['GU'] = "Guam";
		$arr['GT'] = "Guatemala";
		$arr['GG'] = "Guernsey";
		$arr['GN'] = "Guinea";
		$arr['GW'] = "Guinea-Bissau";
		$arr['GY'] = "Guyana";
		$arr['HT'] = "Haiti";
		$arr['HM'] = "Heard Island and Mcdonald Islands";
		$arr['VA'] = "Holy See (Vatican City State)";
		$arr['HN'] = "Honduras";
		$arr['HU'] = "Hungary";
		$arr['IS'] = "Iceland";
		$arr['IN'] = "India";
		$arr['ID'] = "Indonesia";
		$arr['IR'] = "Iran Islamic Republic of";
		$arr['IQ'] = "Iraq";
		$arr['IE'] = "Ireland";
		$arr['IM'] = "Isle of Man";
		$arr['IL'] = "Israel";
		$arr['IT'] = "Italy";
		$arr['JM'] = "Jamaica";
		$arr['JP'] = "Japan";
		$arr['JE'] = "Jersey";
		$arr['JO'] = "Jordan";
		$arr['KZ'] = "Kazakhstan";
		$arr['KE'] = "Kenya";
		$arr['KI'] = "Kiribati";
		$arr['KP'] = "Korea - North";
		$arr['KR'] = "Korea - South";
		$arr['KW'] = "Kuwait";
		$arr['KG'] = "Kyrgyzstan";
		$arr['LA'] = "Lao PDR";
		$arr['LV'] = "Latvia";
		$arr['LB'] = "Lebanon";
		$arr['LS'] = "Lesotho";
		$arr['LR'] = "Liberia";
		$arr['LY'] = "Libya";
		$arr['LI'] = "Liechtenstein";
		$arr['LT'] = "Lithuania";
		$arr['LU'] = "Luxembourg";
		$arr['MK'] = "Macedonia Republic of";
		$arr['MG'] = "Madagascar";
		$arr['MW'] = "Malawi";
		$arr['MY'] = "Malaysia";
		$arr['MV'] = "Maldives";
		$arr['ML'] = "Mali";
		$arr['MT'] = "Malta";
		$arr['MH'] = "Marshall Islands";
		$arr['MQ'] = "Martinique";
		$arr['MR'] = "Mauritania";
		$arr['MU'] = "Mauritius";
		$arr['YT'] = "Mayotte";
		$arr['MX'] = "Mexico";
		$arr['FM'] = "Micronesia Federated States of";
		$arr['MD'] = "Moldova";
		$arr['MC'] = "Monaco";
		$arr['MN'] = "Mongolia";
		$arr['ME'] = "Montenegro";
		$arr['MS'] = "Montserrat";
		$arr['MA'] = "Morocco";
		$arr['MZ'] = "Mozambique";
		$arr['MM'] = "Myanmar";
		$arr['NA'] = "Namibia";
		$arr['NR'] = "Nauru";
		$arr['NP'] = "Nepal";
		$arr['NL'] = "Netherlands";
		$arr['AN'] = "Netherlands Antilles";
		$arr['NC'] = "New Caledonia";
		$arr['NZ'] = "New Zealand";
		$arr['NI'] = "Nicaragua";
		$arr['NE'] = "Niger";
		$arr['NG'] = "Nigeria";
		$arr['NU'] = "Niue";
		$arr['NF'] = "Norfolk Island";
		$arr['MP'] = "Northern Mariana Islands";
		$arr['NO'] = "Norway";
		$arr['OM'] = "Oman";
		$arr['PK'] = "Pakistan";
		$arr['PW'] = "Palau";
		$arr['PS'] = "Palestinian Territory Occupied";
		$arr['PA'] = "Panama";
		$arr['PG'] = "Papua New Guinea";
		$arr['PY'] = "Paraguay";
		$arr['PE'] = "Peru";
		$arr['PH'] = "Philippines";
		$arr['PN'] = "Pitcairn";
		$arr['PL'] = "Poland";
		$arr['PT'] = "Portugal";
		$arr['PR'] = "Puerto Rico";
		$arr['QA'] = "Qatar";
		$arr['RE'] = "Réunion";
		$arr['RO'] = "Romania";
		$arr['RU'] = "Russian Federation";
		$arr['RW'] = "Rwanda";
		$arr['BL'] = "Saint-Barthélemy";
		$arr['SH'] = "Saint Helena";
		$arr['KN'] = "Saint Kitts and Nevis";
		$arr['LC'] = "Saint Lucia";
		$arr['MF'] = "Saint-Martin (French part)";
		$arr['PM'] = "Saint Pierre and Miquelon";
		$arr['VC'] = "Saint Vincent and Grenadines";
		$arr['WS'] = "Samoa";
		$arr['SM'] = "San Marino";
		$arr['ST'] = "Sao Tome and Principe";
		$arr['SA'] = "Saudi Arabia";
		$arr['SN'] = "Senegal";
		$arr['RS'] = "Serbia";
		$arr['SC'] = "Seychelles";
		$arr['SL'] = "Sierra Leone";
		$arr['SG'] = "Singapore";
		$arr['SK'] = "Slovakia";
		$arr['SI'] = "Slovenia";
		$arr['SB'] = "Solomon Islands";
		$arr['SO'] = "Somalia";
		$arr['ZA'] = "South Africa";
		$arr['GS'] = "South Georgia and the South Sandwich Islands";
		$arr['SS'] = "South Sudan";
		$arr['ES'] = "Spain";
		$arr['LK'] = "Sri Lanka";
		$arr['SD'] = "Sudan";
		$arr['SR'] = "Suriname *";
		$arr['SJ'] = "Svalbard and Jan Mayen Islands";
		$arr['SZ'] = "Swaziland";
		$arr['SE'] = "Sweden";
		$arr['CH'] = "Switzerland";
		$arr['SY'] = "Syrian Arab Republic (Syria)";
		$arr['TW'] = "Taiwan Republic of China";
		$arr['TJ'] = "Tajikistan";
		$arr['TZ'] = "Tanzania * United Republic of";
		$arr['TH'] = "Thailand";
		$arr['TL'] = "Timor-Leste";
		$arr['TG'] = "Togo";
		$arr['TK'] = "Tokelau";
		$arr['TO'] = "Tonga";
		$arr['TT'] = "Trinidad and Tobago";
		$arr['TN'] = "Tunisia";
		$arr['TR'] = "Turkey";
		$arr['TM'] = "Turkmenistan";
		$arr['TC'] = "Turks and Caicos Islands";
		$arr['TV'] = "Tuvalu";
		$arr['UG'] = "Uganda";
		$arr['UA'] = "Ukraine";
		$arr['AE'] = "United Arab Emirates";
		$arr['GB'] = "United Kingdom";
		$arr['US'] = "United States of America";
		$arr['UM'] = "United States Minor Outlying Islands";
		$arr['UY'] = "Uruguay";
		$arr['UZ'] = "Uzbekistan";
		$arr['VU'] = "Vanuatu";
		$arr['VE'] = "Venezuela (Bolivarian Republic of)";
		$arr['VN'] = "Viet Nam";
		$arr['VI'] = "Virgin Islands US";
		$arr['WF'] = "Wallis and Futuna Islands";
		$arr['EH'] = "Western Sahara";
		$arr['YE'] = "Yemen";
		$arr['ZM'] = "Zambia";
		$arr['ZW'] = "Zimbabwe";


		if ($cc != "") {
			return $arr[$cc];
		}


		return $arr;

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

		$arr = array();

		$arr['AED'] = "AED";
		$arr['AFN'] = "AFN";
		$arr['ALL'] = "ALL";
		$arr['AMD'] = "AMD";
		$arr['ANG'] = "ANG";
		$arr['AOA'] = "AOA";
		$arr['ARS'] = "ARS";
		$arr['AUD'] = "AUD";
		$arr['AWG'] = "AWG";
		$arr['AZN'] = "AZN";
		$arr['BAM'] = "BAM";
		$arr['BBD'] = "BBD";
		$arr['BDT'] = "BDT";
		$arr['BGN'] = "BGN";
		$arr['BHD'] = "BHD";
		$arr['BIF'] = "BIF";
		$arr['BMD'] = "BMD";
		$arr['BND'] = "BND";
		$arr['BOB'] = "BOB";
		$arr['BRL'] = "BRL";
		$arr['BSD'] = "BSD";
		$arr['BTC'] = "BTC";
		$arr['BTN'] = "BTN";
		$arr['BWP'] = "BWP";
		$arr['BYN'] = "BYN";
		$arr['BYR'] = "BYR";
		$arr['BZD'] = "BZD";
		$arr['CAD'] = "CAD";
		$arr['CDF'] = "CDF";
		$arr['CHF'] = "CHF";
		$arr['CLF'] = "CLF";
		$arr['CLP'] = "CLP";
		$arr['CNY'] = "CNY";
		$arr['COP'] = "COP";
		$arr['CRC'] = "CRC";
		$arr['CUC'] = "CUC";
		$arr['CUP'] = "CUP";
		$arr['CVE'] = "CVE";
		$arr['CZK'] = "CZK";
		$arr['DJF'] = "DJF";
		$arr['DKK'] = "DKK";
		$arr['DOP'] = "DOP";
		$arr['DZD'] = "DZD";
		$arr['EEK'] = "EEK";
		$arr['EGP'] = "EGP";
		$arr['ERN'] = "ERN";
		$arr['ETB'] = "ETB";
		$arr['EUR'] = "EUR";
		$arr['FJD'] = "FJD";
		$arr['FKP'] = "FKP";
		$arr['GBP'] = "GBP";
		$arr['GEL'] = "GEL";
		$arr['GGP'] = "GGP";
		$arr['GHS'] = "GHS";
		$arr['GIP'] = "GIP";
		$arr['GMD'] = "GMD";
		$arr['GNF'] = "GNF";
		$arr['GTQ'] = "GTQ";
		$arr['GYD'] = "GYD";
		$arr['HKD'] = "HKD";
		$arr['HNL'] = "HNL";
		$arr['HRK'] = "HRK";
		$arr['HTG'] = "HTG";
		$arr['HUF'] = "HUF";
		$arr['IDR'] = "IDR";
		$arr['ILS'] = "ILS";
		$arr['IMP'] = "IMP";
		$arr['INR'] = "INR";
		$arr['IQD'] = "IQD";
		$arr['IRR'] = "IRR";
		$arr['ISK'] = "ISK";
		$arr['JEP'] = "JEP";
		$arr['JMD'] = "JMD";
		$arr['JOD'] = "JOD";
		$arr['JPY'] = "JPY";
		$arr['KES'] = "KES";
		$arr['KGS'] = "KGS";
		$arr['KHR'] = "KHR";
		$arr['KMF'] = "KMF";
		$arr['KPW'] = "KPW";
		$arr['KRW'] = "KRW";
		$arr['KWD'] = "KWD";
		$arr['KYD'] = "KYD";
		$arr['KZT'] = "KZT";
		$arr['LAK'] = "LAK";
		$arr['LBP'] = "LBP";
		$arr['LKR'] = "LKR";
		$arr['LRD'] = "LRD";
		$arr['LSL'] = "LSL";
		$arr['LTL'] = "LTL";
		$arr['LVL'] = "LVL";
		$arr['LYD'] = "LYD";
		$arr['MAD'] = "MAD";
		$arr['MDL'] = "MDL";
		$arr['MGA'] = "MGA";
		$arr['MKD'] = "MKD";
		$arr['MMK'] = "MMK";
		$arr['MNT'] = "MNT";
		$arr['MOP'] = "MOP";
		$arr['MRO'] = "MRO";
		$arr['MUR'] = "MUR";
		$arr['MVR'] = "MVR";
		$arr['MWK'] = "MWK";
		$arr['MXN'] = "MXN";
		$arr['MYR'] = "MYR";
		$arr['MZN'] = "MZN";
		$arr['NAD'] = "NAD";
		$arr['NGN'] = "NGN";
		$arr['NIO'] = "NIO";
		$arr['NOK'] = "NOK";
		$arr['NPR'] = "NPR";
		$arr['NZD'] = "NZD";
		$arr['OMR'] = "OMR";
		$arr['PAB'] = "PAB";
		$arr['PEN'] = "PEN";
		$arr['PGK'] = "PGK";
		$arr['PHP'] = "PHP";
		$arr['PKR'] = "PKR";
		$arr['PLN'] = "PLN";
		$arr['PYG'] = "PYG";
		$arr['QAR'] = "QAR";
		$arr['RON'] = "RON";
		$arr['RSD'] = "RSD";
		$arr['RUB'] = "RUB";
		$arr['RWF'] = "RWF";
		$arr['SAR'] = "SAR";
		$arr['SBD'] = "SBD";
		$arr['SCR'] = "SCR";
		$arr['SDG'] = "SDG";
		$arr['SEK'] = "SEK";
		$arr['SGD'] = "SGD";
		$arr['SHP'] = "SHP";
		$arr['SLL'] = "SLL";
		$arr['SOS'] = "SOS";
		$arr['SRD'] = "SRD";
		$arr['STD'] = "STD";
		$arr['SVC'] = "SVC";
		$arr['SYP'] = "SYP";
		$arr['SZL'] = "SZL";
		$arr['THB'] = "THB";
		$arr['TJS'] = "TJS";
		$arr['TMT'] = "TMT";
		$arr['TND'] = "TND";
		$arr['TOP'] = "TOP";
		$arr['TRY'] = "TRY";
		$arr['TTD'] = "TTD";
		$arr['TWD'] = "TWD";
		$arr['TZS'] = "TZS";
		$arr['UAH'] = "UAH";
		$arr['UGX'] = "UGX";
		$arr['USD'] = "USD";
		$arr['UYU'] = "UYU";
		$arr['UZS'] = "UZS";
		$arr['VEF'] = "VEF";
		$arr['VND'] = "VND";
		$arr['VUV'] = "VUV";
		$arr['WST'] = "WST";
		$arr['XAF'] = "XAF";
		$arr['XAG'] = "XAG";
		$arr['XAU'] = "XAU";
		$arr['XCD'] = "XCD";
		$arr['XDR'] = "XDR";
		$arr['XOF'] = "XOF";
		$arr['XPF'] = "XPF";
		$arr['YER'] = "YER";
		$arr['ZAR'] = "ZAR";
		$arr['ZMK'] = "ZMK";
		$arr['ZMW'] = "ZMW";
		$arr['ZWL'] = "ZWL";

		return $arr;

	}//getCurrency


	public function getTimeZone()
	{

		$arr = array();
		$arr['-12.0'] = "(GMT -12:00) Eniwetok, Kwajalein";
		$arr['-11.0'] = "(GMT -11:00) Midway Island, Samoa";
		$arr['-10.0'] = "(GMT -10:00) Hawaii";
		$arr['-9.0'] = "(GMT -9:00) Alaska";
		$arr['-8.0'] = "(GMT -8:00) Pacific Time (US &amp; Canada)";
		$arr['-7.0'] = "(GMT -7:00) Mountain Time (US &amp; Canada)";
		$arr['-6.0'] = "(GMT -6:00) Central Time (US &amp; Canada), Mexico City";
		$arr['-5.0'] = "(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima";
		$arr['-4.0'] = "(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz";
		$arr['-3.5'] = "(GMT -3:30) Newfoundland";
		$arr['-3.0'] = "(GMT -3:00) Brazil, Buenos Aires, Georgetown";
		$arr['-2.0'] = "(GMT -2:00) Mid-Atlantic";
		$arr['-1.0'] = "(GMT -1:00 hour) Azores, Cape Verde Islands";
		$arr['+0.0'] = "(GMT) Western Europe Time, London, Lisbon, Casablanca";
		$arr['+1.0'] = "(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris";
		$arr['+2.0'] = "(GMT +2:00) Kaliningrad, South Africa";

		$arr['+3.0'] = "(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg";
		$arr['+3.5'] = "(GMT +3:30) Tehran";
		$arr['+4.0'] = "(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi";
		$arr['+4.5'] = "(GMT +4:30) Kabul";
		$arr['+5.0'] = "(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent";
		$arr['+5.5'] = "(GMT +5:30) Bombay, Calcutta, Madras, New Delhi";
		$arr['+5.75'] = "(GMT +5:45) Kathmandu";
		$arr['+6.0'] = "(GMT +6:00) Almaty, Dhaka, Colombo";
		$arr['+6.5'] = "(GMT +6:30) Myanmar";
		$arr['+7.0'] = "(GMT +7:00) Bangkok, Hanoi, Jakarta";
		$arr['+8.0'] = "(GMT +8:00) Beijing, Perth, Singapore, Hong Kong";
		$arr['+9.0'] = "(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk";
		$arr['+9.5'] = "(GMT +9:30) Adelaide, Darwin";
		$arr['+10.0'] = "(GMT +10:00) Eastern Australia, Guam, Vladivostok";
		$arr['+11.0'] = "(GMT +11:00) Magadan, Solomon Islands, New Caledonia";
		$arr['+12.0'] = "(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka";
		return $arr;


	}//getTimeZone


	//public function ip_info($purpose = "location", $deep_detect = TRUE) {
	public function ip_info($purpose = "location", $ip)
	{

		$output = NULL;

		// $ip = $this->get_client_ip($deep_detect);

		$purpose = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));

		$support = array("country", "countrycode", "state", "region", "city", "location", "address", "isp");

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

			$data = $this->getIpBasedData($ip);

			\Log::info("IP Data: " . json_encode($data));

			switch ($purpose) {
				case "location":
					$output = array(
						"city" => @$data['city']['names']['en'],
						"state" => @$data['subdivisions'][0]['names']['en'],
						"country" => @$data['country']['names']['en'],
						"country_code" => @$data['country']['iso_code'],
						"continent" => @$data['continent']['names']['en'],
						"continent_code" => @$data['continent']['code']
					);
					break;
				case "address":
					$address = array($data['country']['names']['en']);

					if (@strlen($data['subdivisions'][0]['names']['en']) >= 1)
						$address[] = $data['subdivisions'][0]['names']['en'];

					if (@strlen($data['city']['names']['en']) >= 1)
						$address[] = $data['city']['names']['en'];

					$output = implode(", ", array_reverse($address));

					break;

				case "city":
					$output = @$data['city']['names']['en'];
					break;
				case "state":
					$output = @$data['subdivisions'][0]['names']['en'];
					break;
				case "region":
					$output = @$data['subdivisions'][0]['names']['en'];
					break;
				case "country":
					$output = @$data['country']['names']['en'];
					break;
				case "countrycode":
					$output = @$data['country']['iso_code'];
					break;

				case "isp":
					$output = @$data['traits']['isp'];
					break;
			}//switch

		}//if

		return $output;

	}//ip_info




	// Return User Agent
	public function getBrowswerData()
	{

		$useragent = $_SERVER['HTTP_USER_AGENT'];

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

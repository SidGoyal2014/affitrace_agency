<?php

namespace App\Utils;

use Illuminate\Support\Facades\Log;

class IpLocationUtils
{


    public static function getTimeZone()
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
    }

    public static function getCountry($cc = '')
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
    }

    public static function ip_info($purpose = "location", $ip)
    {
        $output = NULL;
        $purpose = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
        $support = array("country", "countrycode", "state", "region", "city", "location", "address", "isp");

        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {

            // NOTE: We need to implement or copy getIpBasedData() here or in a separate helper.
            // Since I haven't found getIpBasedData yet, and this method relies on it.
            // I will assume for now we might need to copy it from CommonFunctions 
            // BUT I haven't read it yet.
            // To proceed safely, I will NOT call it yet and will finish this file creation
            // then go find getIpBasedData and update this file.

            $data = self::getIpBasedData($ip);

            // \Log::info("IP Data: " . json_encode($data)); // Removed log or kept? kept for parity.
            Log::info("IP Data: " . json_encode($data));

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
    }

    /**
     * Get IP geolocation data using MaxMind GeoLite2 with fallback to external API.
     * 
     * Strategy:
     * 1. Try MaxMind local database (fast, ~1ms)
     * 2. If MaxMind fails, fallback to findip.net API (slow, ~100-500ms)
     * 
     * @param string $ip
     * @return array|null
     */
    private static function getIpBasedData($ip)
    {
        Log::info("IP Geolocation lookup for: " . $ip);

        // Try MaxMind GeoLite2 first (local database - very fast)
        try {
            $geoip = geoip($ip);

            if ($geoip && !$geoip->default) {
                Log::info("MaxMind GeoIP hit for: " . $ip);

                // Convert torann/geoip format to our expected format
                return [
                    'city' => ['names' => ['en' => $geoip->city]],
                    'subdivisions' => [['names' => ['en' => $geoip->state_name]]],
                    'country' => [
                        'names' => ['en' => $geoip->country],
                        'iso_code' => $geoip->iso_code
                    ],
                    'continent' => [
                        'names' => ['en' => $geoip->continent],
                        'code' => $geoip->continent
                    ],
                    'traits' => ['isp' => null] // MaxMind GeoLite2 doesn't include ISP
                ];
            }
        } catch (\Exception $e) {
            Log::warning("MaxMind GeoIP failed for {$ip}: " . $e->getMessage());
        }

        // Fallback to external API (findip.net)
        Log::info("Falling back to findip.net API for: " . $ip);
        return self::getIpBasedDataFromApi($ip);
    }

    /**
     * Get IP data from external findip.net API (original method, kept as fallback)
     * 
     * @param string $ip
     * @return array|null
     */
    private static function getIpBasedDataFromApi($ip)
    {
        try {
            $token = '428a5df9a35b4c53b942f0c9bfca9469';
            $url = "https://api.findip.net/{$ip}/?token={$token}";

            // Add timeout to prevent hanging
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5 // 5 second timeout
                ]
            ]);

            $response = file_get_contents($url, false, $context);
            $data = json_decode($response, true);

            Log::info("findip.net API response for {$ip}");
            return $data;
        } catch (\Exception $e) {
            Log::error("findip.net API failed for {$ip}: " . $e->getMessage());
            return null;
        }
    }

}

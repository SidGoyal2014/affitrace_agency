<?php

namespace App\Utils;

use Illuminate\Support\Facades\Log;

class CurrencyUtils
{
    private static $rates = [
        "USD" => 1,
        "AED" => 3.67,
        "AFN" => 66.24,
        "ALL" => 82.32,
        "AMD" => 381.58,
        "ANG" => 1.79,
        "AOA" => 920.99,
        "ARS" => 1453.17,
        "AUD" => 1.51,
        "AWG" => 1.79,
        "AZN" => 1.7,
        "BAM" => 1.67,
        "BBD" => 2,
        "BDT" => 122.26,
        "BGN" => 1.67,
        "BHD" => 0.376,
        "BIF" => 2963.42,
        "BMD" => 1,
        "BND" => 1.29,
        "BOB" => 6.91,
        "BRL" => 5.52,
        "BSD" => 1,
        "BTN" => 90.34,
        "BWP" => 13.67,
        "BYN" => 2.94,
        "BZD" => 2,
        "CAD" => 1.38,
        "CDF" => 2264.13,
        "CHF" => 0.794,
        "CLF" => 0.0232,
        "CLP" => 917.84,
        "CNH" => 7.04,
        "CNY" => 7.05,
        "COP" => 3859.77,
        "CRC" => 498.07,
        "CUP" => 24,
        "CVE" => 94.01,
        "CZK" => 20.78,
        "DJF" => 177.72,
        "DKK" => 6.36,
        "DOP" => 62.83,
        "DZD" => 129.56,
        "EGP" => 47.58,
        "ERN" => 15,
        "ETB" => 154.55,
        "EUR" => 0.853,
        "FJD" => 2.29,
        "FKP" => 0.747,
        "FOK" => 6.36,
        "GBP" => 0.747,
        "GEL" => 2.7,
        "GGP" => 0.747,
        "GHS" => 11.56,
        "GIP" => 0.747,
        "GMD" => 74.26,
        "GNF" => 8723.63,
        "GTQ" => 7.66,
        "GYD" => 209.16,
        "HKD" => 7.78,
        "HNL" => 26.32,
        "HRK" => 6.42,
        "HTG" => 130.86,
        "HUF" => 330.6,
        "IDR" => 16729.2,
        "ILS" => 3.21,
        "IMP" => 0.747,
        "INR" => 90.34,
        "IQD" => 1310.11,
        "IRR" => 42339.28,
        "ISK" => 126.18,
        "JEP" => 0.747,
        "JMD" => 159.78,
        "JOD" => 0.709,
        "JPY" => 155.61,
        "KES" => 128.88,
        "KGS" => 87.4,
        "KHR" => 4012.15,
        "KID" => 1.51,
        "KMF" => 419.43,
        "KRW" => 1475.26,
        "KWD" => 0.307,
        "KYD" => 0.833,
        "KZT" => 515.87,
        "LAK" => 21683.36,
        "LBP" => 89500,
        "LKR" => 309.24,
        "LRD" => 176.79,
        "LSL" => 16.75,
        "LYD" => 5.42,
        "MAD" => 9.16,
        "MDL" => 16.84,
        "MGA" => 4474.84,
        "MKD" => 52.37,
        "MMK" => 2101.48,
        "MNT" => 3523.83,
        "MOP" => 8.01,
        "MRU" => 39.89,
        "MUR" => 46.01,
        "MVR" => 15.43,
        "MWK" => 1741.62,
        "MXN" => 17.99,
        "MYR" => 4.09,
        "MZN" => 63.89,
        "NAD" => 16.75,
        "NGN" => 1454.96,
        "NIO" => 36.77,
        "NOK" => 10.17,
        "NPR" => 144.54,
        "NZD" => 1.73,
        "OMR" => 0.384,
        "PAB" => 1,
        "PEN" => 3.37,
        "PGK" => 4.27,
        "PHP" => 58.61,
        "PKR" => 280.25,
        "PLN" => 3.59,
        "PYG" => 6688.26,
        "QAR" => 3.64,
        "RON" => 4.34,
        "RSD" => 100.06,
        "RUB" => 79.79,
        "RWF" => 1457.45,
        "SAR" => 3.75,
        "SBD" => 8.04,
        "SCR" => 14.02,
        "SDG" => 511.75,
        "SEK" => 9.29,
        "SGD" => 1.29,
        "SHP" => 0.747,
        "SLE" => 23.68,
        "SLL" => 23678.78,
        "SOS" => 570.62,
        "SRD" => 38.56,
        "SSP" => 4658.97,
        "STN" => 20.89,
        "SYP" => 11017.21,
        "SZL" => 16.75,
        "THB" => 31.43,
        "TJS" => 9.24,
        "TMT" => 3.5,
        "TND" => 2.91,
        "TOP" => 2.39,
        "TRY" => 42.79,
        "TTD" => 6.77,
        "TVD" => 1.51,
        "TWD" => 31.56,
        "TZS" => 2450.78,
        "UAH" => 42.37,
        "UGX" => 3542.74,
        "UYU" => 38.97,
        "UZS" => 12087,
        "VES" => 282.51,
        "VND" => 26228.5,
        "VUV" => 121.21,
        "WST" => 2.76,
        "XAF" => 559.24,
        "XCD" => 2.7,
        "XCG" => 1.79,
        "XDR" => 0.728,
        "XOF" => 559.24,
        "XPF" => 101.74,
        "YER" => 238.34,
        "ZAR" => 16.74,
        "ZMW" => 22.92,
        "ZWG" => 26.11,
        "ZWL" => 26.11
    ];

    public static function getNewCurrencyValue($amount, $from)
    {
        return self::convertToUsd($amount, $from);
    }

    public static function convertToUsd($amount, $from)
    {
        $from = strtoupper($from);
        if ($from === 'USD') {
            return (float) $amount;
        }
        if (isset(self::$rates[$from])) {
            return (float) $amount / self::$rates[$from];
        }
        return (float) $amount;
    }

    public static function convertFromUsd($amount, $to)
    {
        $to = strtoupper($to);
        if ($to === 'USD') {
            return (float) $amount;
        }
        if (isset(self::$rates[$to])) {
            return (float) $amount * self::$rates[$to];
        }
        return (float) $amount;
    }

    public static function getDefaultCurrency()
    {
        try {
            $setting = \App\Models\Setting::find(1);
            if ($setting && !empty($setting->default_currency)) {
                return strtoupper($setting->default_currency);
            }
        } catch (\Exception $e) {
            // fallback
        }
        return 'USD';
    }

    public static function convertFromUsdToDefault($amount)
    {
        $default = self::getDefaultCurrency();
        return self::convertFromUsd($amount, $default);
    }

    public static function getCurrency()
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
    }
}

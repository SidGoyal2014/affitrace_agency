<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use App\Models\Offer;
use App\Models\OfferTracking;
use App\Models\Conversion;
use App\Models\OfferEvent;
use App\Models\PayoutRule;
use App\Http\Traits\CommonFunctions;
use App\Http\Traits\TrackingUrlValidation;
use App\Http\Traits\SystemSettings;
use App\Http\Traits\MultiTenantResolver;

class TrackingService
{
    use CommonFunctions, TrackingUrlValidation, SystemSettings, MultiTenantResolver;

    public function processClick($oid, $aid, Request $request)
    {
        if (!$this->isOfferAcceptedForAffiliate($oid, $aid)) {
            Session::flash('error', "Sorry! this offer is not assigned to you");
            return ['type' => 'error'];
        }

        $payoutRule = '';

        // Cache offer data for 5 minutes to reduce DB load
        $offers = Cache::remember("offer:{$oid}", 300, function () use ($oid) {
            return DB::table('offers')->where('id', $oid)->get()->all();
        });

        if (empty($offers)) {
            // Handle case where offer doesn't exist
            return ['type' => 'redirect', 'url' => 'verify'];
        }

        $offer = $offers[0];

        \Log::info("Offer: " . json_encode($offer));

        // User Data
        $arrBrowserData = $this->getBrowswerData();
        $client_ip = $this->get_client_ip(TRUE);
        $arrLocation = $this->ip_info("location", $client_ip);

        \Log::info("Location: " . json_encode($arrLocation));

        // Get user country
        $visitor_country = strtoupper($arrLocation['country']);

        // Check Click Limit
        if ($this->get_total_clicks() >= $this->get_settings("num_clicks")) {
            \Log::info("Clicks not saved as maximum allowed limit as per purchased plan already reached");
            return ['type' => 'redirect', 'url' => 'verify'];
        }

        $response = $this->is_matching_target($offer, $payoutRule, $arrBrowserData, $arrLocation);

        \Log::info('' . json_encode($response));

        if ($response) {

            $agency_prefix = $this->resolve_agency_username($request);
            if (!$agency_prefix) {
                $agency_prefix = 'unknown';
            }

            $click_id = sprintf('%s-%s', $agency_prefix, md5($this->generateUniqueValue()));

            $eid = NULL;
            $sub1 = $request->sub1 ?? NULL;
            $sub2 = $request->sub2 ?? NULL;
            $source = $request->source ?? NULL;
            $google_aid = $request->google_aid ?? NULL;
            $iosidfa = $request->iosidfa ?? NULL;
            $android_id = $request->android_id ?? NULL;

            $this->saveClick($oid, $aid, $click_id, $offer, $eid, $sub1, $sub2, $visitor_country, $arrBrowserData, $client_ip, $source, $google_aid, $iosidfa, $android_id);

            $offer_url = "";
            if ($offer->attribution_link != NULL) {
                $offer_url = $this->getFinalAttributionURL($offer, $aid, $click_id, $visitor_country, $source, $google_aid, $iosidfa, $android_id);
            } else {
                $offer_url = $this->getFinalOfferURL($offer, $aid, $click_id, $visitor_country, $source, $google_aid, $iosidfa, $android_id);
            }

            return ['type' => 'redirect', 'url' => $offer_url];

        } else {
            return ['type' => 'redirect', 'url' => 'verify'];
        }
    }

    public function processPostback(Request $request)
    {
        $resp = ['success' => 'N'];

        if (!isset($request->aff_click_id) || empty($request->aff_click_id)) {
            $resp['message'] = 'Missing aff_click_id';
            return $resp;
        }

        // Find the original click
        $click = OfferTracking::where('tid', $request->aff_click_id)->first();

        if (!$click) {
            $resp['message'] = 'Click not found for: ' . $request->aff_click_id;
            return $resp;
        }

        // Get browser/device data
        $arrBrowserData = $this->getBrowswerData();
        $device_type = ucfirst($arrBrowserData['device_type']);

        // Check for payout rules if event_id provided
        $payoutRule = [];
        if (isset($request->event_id) && $request->event_id > 0) {
            $arr_valid_offer_events = $this->get_valid_offer_events($click->offer_id);

            if (!in_array($request->event_id, $arr_valid_offer_events)) {
                $resp['message'] = 'This event is not allowed for this offer';
                return $resp;
            }

            $payoutRule = $this->get_payout_rule($click->offer_id, $request->event_id);
        }

        // Create conversion record with all available data
        $conv = new Conversion();

        // === Identifiers ===
        $conv->tid = $request->aff_click_id;
        $conv->aff_click_id = $request->aff_click_id;
        $conv->i_id = $request->i_id ?? null;

        // === Offer Data ===
        $conv->offer_id = $click->offer_id;
        $conv->offer_name = $click->offer_name;
        $conv->s_offer = $request->s_offer ?? null;

        // === Affiliate Data ===
        $conv->affiliate_id = $click->aff_id;
        $conv->affiliate_name = $request->affiliate_name ?? null;
        $conv->s_affiliate = $request->s_affiliate ?? null;
        $conv->aff_manager = $request->aff_manager ?? null;

        // === Advertiser Data ===
        $conv->advertiser_id = $click->adv_id;
        $conv->advertiser_name = $request->advertiser_name ?? null;
        $conv->adv_manager = $request->adv_manager ?? null;

        // === Metrics ===
        $conv->clicks = 1;
        $conv->conversion = 1;
        $conv->impressions = $request->impressions ?? 0;
        $conv->unique_clicks = $request->unique_clicks ?? 0;

        // === Pricing ===
        if (count($payoutRule) > 0) {
            $conv->revenue = $payoutRule[0]['revenue_pricing'];
            $conv->payout = $payoutRule[0]['payout_pricing'];
            $conv->advertiser_price = $payoutRule[0]['revenue_pricing'];
            $conv->affiliate_price = $payoutRule[0]['payout_pricing'];
            $conv->currency = $payoutRule[0]['currency'];
            $conv->revenue_usd = \App\Utils\CurrencyUtils::getNewCurrencyValue($payoutRule[0]['revenue_pricing'], $payoutRule[0]['currency']);
            $conv->payout_usd = \App\Utils\CurrencyUtils::getNewCurrencyValue($payoutRule[0]['payout_pricing'], $payoutRule[0]['currency']);
        } else {
            $conv->revenue = $request->revenue ?? $click->revenue ?? 0;
            $conv->payout = $request->payout ?? $click->payout ?? 0;
            $conv->advertiser_price = $request->advertiser_price ?? $click->revenue ?? 0;
            $conv->affiliate_price = $request->affiliate_price ?? $click->payout ?? 0;
            $conv->currency = $request->currency ?? $click->currency ?? 'USD';
            $conv->revenue_usd = $click->revenue_usd ?? 0;
            $conv->payout_usd = $click->payout_usd ?? 0;
        }
        $conv->affiliate_model = $request->affiliate_model ?? null;
        $conv->advertiser_model = $request->advertiser_model ?? null;
        $conv->sale = $request->sale ?? 0;
        $conv->profit = ($conv->revenue ?? 0) - ($conv->payout ?? 0);

        // === Event Data ===
        $conv->event_id = $request->event_id ?? null;
        $conv->event = $request->event ?? 'default';
        $conv->status = $request->status ?? 'approved';
        $conv->status_type = $request->status_type ?? null;
        $conv->filter_log = $request->filter_log ?? null;

        // === Time Data ===
        $conv->date = now()->toDateString();
        $conv->time = now()->toTimeString();
        $conv->hour = now()->format('H');
        $conv->conversion_time = now();

        // === Geo Data ===
        $conv->ip = $request->ip ?? $click->ip ?? $this->get_client_ip(true);
        $conv->country = $request->country ?? $click->country;
        $conv->location = $request->location ?? null;
        $conv->isp = $request->isp ?? $click->isp ?? null;
        $conv->conn_speed = $request->conn_speed ?? null;

        // === Device Data ===
        $conv->device = $request->device ?? $device_type;
        $conv->device_brand = $request->device_brand ?? $click->brand ?? null;
        $conv->device_model = $request->device_model ?? null;
        $conv->device_id = $this->get_visitor_device($device_type, 'Y');
        $conv->os = $request->os ?? $arrBrowserData['os_name'] ?? $click->os;
        $conv->os_ver = $request->os_ver ?? $arrBrowserData['os_version'] ?? $click->os_ver;
        $conv->browser = $request->browser ?? $arrBrowserData['browser_name'] ?? $click->browser;
        $conv->useragent = $_SERVER['HTTP_USER_AGENT'] ?? $click->useragent ?? null;

        // === Tracking Data ===
        $conv->referral_url = $request->referral_url ?? null;
        $conv->referral_domain = $request->referral_domain ?? null;
        $conv->source = $request->source ?? $click->source ?? null;

        // === Affiliate Sub Parameters ===
        $conv->aff_sub1 = $request->aff_sub1 ?? $request->sub1 ?? $click->sub1 ?? null;
        $conv->aff_sub2 = $request->aff_sub2 ?? $request->sub2 ?? $click->sub2 ?? null;
        $conv->aff_sub3 = $request->aff_sub3 ?? $click->aff_sub3 ?? null;
        $conv->aff_sub4 = $request->aff_sub4 ?? $click->aff_sub4 ?? null;
        $conv->aff_sub5 = $request->aff_sub5 ?? $click->aff_sub5 ?? null;

        // === Advertiser Sub Parameters ===
        $conv->adv_sub1 = $request->adv_sub1 ?? null;
        $conv->adv_sub2 = $request->adv_sub2 ?? null;
        $conv->adv_sub3 = $request->adv_sub3 ?? null;
        $conv->adv_sub4 = $request->adv_sub4 ?? null;
        $conv->adv_sub5 = $request->adv_sub5 ?? null;

        // === MMP Data ===
        $conv->ios_idfa = $request->iosidfa ?? $request->ios_idfa ?? $click->iosidfa ?? null;
        $conv->google_aid = $request->google_aid ?? $click->google_aid ?? null;
        $conv->android_id = $request->android_id ?? $click->android_id ?? null;

        // === Raw Data (store full postback for debugging) ===
        $conv->raw_data = json_encode($request->all());

        $conv->save();

        $resp['success'] = 'Y';
        $resp['conversion_id'] = $conv->id;

        return $resp;
    }



    public function processPixelConversion(Request $request)
    {
        $resp = ['success' => 'N'];

        // Get tid from request
        $tid = $request->input('tid') ?? $request->input('aff_click_id');

        if (!$tid) {
            return $resp;
        }

        // Retrieve the original click
        $click = OfferTracking::where('tid', $tid)->first();
        if (!$click) {
            return $resp;
        }

        // Create conversion record in conversions table
        $arrBrowserData = $this->getBrowswerData();
        $device_type = ucfirst($arrBrowserData['device_type']);

        $conv = new Conversion();
        $conv->tid = $tid;
        $conv->aff_click_id = $tid;
        $conv->offer_id = $click->offer_id;
        $conv->affiliate_id = $click->aff_id;
        $conv->offer_name = $click->offer_name;
        $conv->country = $click->country;
        $conv->advertiser_id = $click->adv_id;

        // Use click pricing (no event-based payout for pixel)
        $conv->revenue = $click->revenue ?? 0;
        $conv->payout = $click->payout ?? 0;
        $conv->currency = $click->currency ?? 'USD';
        $conv->revenue_usd = $click->revenue_usd ?? 0;
        $conv->payout_usd = $click->payout_usd ?? 0;

        $conv->event = $request->event ?? 'pixel';
        $conv->device_id = $this->get_visitor_device($device_type, 'Y');
        $conv->device = $device_type;

        $conv->useragent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $conv->browser = $arrBrowserData['browser_name'];
        $conv->os = $arrBrowserData['os_name'];
        $conv->os_ver = $arrBrowserData['os_version'];

        $conv->aff_sub1 = $click->sub1 ?? null;
        $conv->aff_sub2 = $click->sub2 ?? null;

        $conv->date = now()->toDateString();
        $conv->time = now()->toTimeString();
        $conv->hour = now()->format('H');
        $conv->conversion_time = now();
        $conv->conversion = 1;

        $conv->save();

        $resp['success'] = 'Y';

        // Fetch affiliate pixel to fire back
        $pixel = DB::table('affiliate_pixels')
            ->where('affiliate_id', $click->aff_id)
            ->where('status', 'active')
            ->where(function ($q) use ($click) {
                $q->where('offer_id', $click->offer_id)
                    ->orWhereNull('offer_id');
            })
            ->orderBy('offer_id', 'desc')
            ->first();

        if ($pixel) {
            $resp['pixel_type'] = $pixel->type;
            $resp['pixel_code'] = $this->replacePixelMacros($pixel->pixel_code, $click);
        }

        return $resp;
    }


    private function replacePixelMacros($code, $tracking)
    {
        $code = str_replace('{payout}', $tracking->payout, $code);
        $code = str_replace('{tid}', $tracking->tid, $code);
        // Add more macros as needed
        return $code;
    }

    public function updateOfferStatus($oid, $status)
    {
        $offer = Offer::find($oid);
        if ($offer) {
            $offer->offer_status = $status;
            $offer->save();
        }
    }

    // Private helpers moved from Controller

    private function saveClick($oid, $aid, $click_id, $offer, $event_id, $sub1, $sub2, $visitor_country, $arrBrowserData, $client_ip, $source, $google_aid, $iosidfa, $android_id)
    {
        $device_type = ucfirst($arrBrowserData['device_type']);

        $ot = new OfferTracking();
        $ot->offer_id = $oid;
        $ot->aff_id = $aid;
        $ot->event = '';

        $ot->revenue = $offer->ad_pricing;
        $ot->payout = $offer->aff_pricing;
        $ot->tid = $click_id;
        $ot->aff_click_id = '';
        $ot->device_id = $this->get_visitor_device($device_type, 'Y');
        $ot->offer_name = $offer->offer_name;

        $ot->currency = $offer->currency;

        $ot->revenue_usd = \App\Utils\CurrencyUtils::getNewCurrencyValue($offer->ad_pricing, $offer->currency);
        $ot->payout_usd = \App\Utils\CurrencyUtils::getNewCurrencyValue($offer->aff_pricing, $offer->currency);

        $ot->ip = $client_ip;

        // Validation trait might have this logic but original controller had it inline too
        // or get_client_ip handled strict IP, but country assignment was here.
        if ($ot->ip == "127.0.0.1" || $ot->ip == "::1") {
            $ot->country = "India";
        } else {
            $ot->country = $visitor_country;
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

        $ot->timestamp = now();

        $ot->save();
    }

    private function update_offer_tracking_without_event($request)
    {
        $arr = array();
        foreach ($request->all() as $k => $v) {
            if (str_contains($v, '{') || str_contains($v, '}') || $k == 'event_id') {
                continue;
            }
            $arr[$k] = $v;
        }

        $affectedRows = OfferTracking::where('tid', $request->aff_click_id)->update($arr);

        if ($affectedRows == 1) {
            $resp['success'] = 'Y';
        } else {
            $resp['success'] = 'N';
            $resp['message'] = 'Click Id doesn\'t exists';
        }
        return $resp;
    }

    public function get_valid_offer_events($offer_id)
    {
        $arrEvents = array();
        $events = OfferEvent::select('id')->where('offer_id', $offer_id)->get()->toArray();

        foreach ($events as $e) {
            $arrEvents[] = $e['id'];
        }
        return $arrEvents;
    }

    public function get_payout_rule($offer_id, $event_id)
    {
        return PayoutRule::where('offer_id', $offer_id)->where('event_id', $event_id)->get()->toArray();
    }

    private function getFinalAttributionURL($offer, $aid, $cid, $visitor_country, $source, $google_aid, $iosidfa, $android_id)
    {
        $offer_url = $offer->attribution_link;

        $offer_url = str_replace("{app_id}", $offer->app_id, $offer_url); // Optional checks removed for brevity, check shouldn't hurt
        $offer_url = str_replace("{tid}", $cid, $offer_url);
        $offer_url = str_replace("{aff_id}", $aid, $offer_url);
        $offer_url = str_replace("{offer_id}", $offer->id, $offer_url);
        $offer_url = str_replace("{country}", $visitor_country, $offer_url);
        $offer_url = str_replace("{offername}", $offer->offer_name, $offer_url);
        $offer_url = str_replace("{device_id}", $offer->target_device, $offer_url);

        $offer_url = $this->replacing_mobile_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id);

        return $offer_url;
    }

    private function getFinalOfferURL($offer, $aid, $cid, $visitor_country, $source, $google_aid, $iosidfa, $android_id)
    {
        $offer_url = $offer->offer_url;

        $offer_url = str_replace("{tid}", $cid, $offer_url);
        $offer_url = str_replace("{aff_id}", $aid, $offer_url);
        $offer_url = str_replace("{offer_id}", $offer->id, $offer_url);
        $offer_url = str_replace("{country}", $visitor_country, $offer_url);
        $offer_url = str_replace("{offername}", $offer->offer_name, $offer_url);
        $offer_url = str_replace("{device_id}", $offer->target_device, $offer_url);

        return $offer_url;
    }

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
            $offer_url = str_replace("{source}", $source, $offer_url);
            $offer_url = str_replace("{google_aid}", $google_aid, $offer_url);
            $offer_url = str_replace("{iosidfa}", $iosidfa, $offer_url);
            $offer_url = str_replace("{android_id}", $android_id, $offer_url);
        }
        return $offer_url;
    }

    private function replacing_appsflyer_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id)
    {
        $offer_url = str_replace("{deviceid}", $offer->target_device, $offer_url);
        $offer_url = str_replace("{source}", $source, $offer_url);
        $offer_url = str_replace("{googleaid}", $google_aid, $offer_url);
        $offer_url = str_replace("{iosidfa}", $iosidfa, $offer_url);
        $offer_url = str_replace("{androidid}", $android_id, $offer_url);
        return $offer_url;
    }

    private function replacing_adjust_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id)
    {
        $offer_url = str_replace("{googleaid}", $google_aid, $offer_url);
        $offer_url = str_replace("{iosidfa}", $iosidfa, $offer_url);
        $offer_url = str_replace("{androidid}", $android_id, $offer_url);
        return $offer_url;
    }

    private function replacing_branch_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id)
    {
        $offer_url = str_replace("{deviceid}", $offer->target_device, $offer_url);
        $offer_url = str_replace("{googleaid}", $google_aid, $offer_url);
        $offer_url = str_replace("{iosidfa}", $iosidfa, $offer_url);
        $offer_url = str_replace("{androidid}", $android_id, $offer_url);
        return $offer_url;
    }

    private function replacing_singular_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id)
    {
        $offer_url = str_replace("{offerid}", $offer->id, $offer_url);
        $offer_url = str_replace("{deviceid}", $offer->target_device, $offer_url);
        $offer_url = str_replace("{googleaid}", $google_aid, $offer_url);
        $offer_url = str_replace("{iosidfa}", $iosidfa, $offer_url);
        $offer_url = str_replace("{androidid}", $android_id, $offer_url);
        return $offer_url;
    }

    private function replacing_kochava_macros($offer, $offer_url, $source, $google_aid, $iosidfa, $android_id)
    {
        $offer_url = str_replace("{googleaid}", $google_aid, $offer_url);
        $offer_url = str_replace("{iosidfa}", $iosidfa, $offer_url);
        $offer_url = str_replace("{androidid}", $android_id, $offer_url);
        return $offer_url;
    }
}

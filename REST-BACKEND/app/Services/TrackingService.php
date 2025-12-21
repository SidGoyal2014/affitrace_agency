<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Offer;
use App\Models\OfferTracking;
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
        $offers = DB::select('call procedure_get_offers(?)', array($oid));

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
        $resp = array();

        if (isset($request->aff_click_id)) {

            $resp = $this->update_offer_tracking_without_event($request);

            if (isset($request->event_id) && $request->event_id > 0 && $resp['success'] == 'Y') {

                $getCIBR = OfferTracking::where('tid', $request->aff_click_id)->get()->toArray();

                if (count($getCIBR) > 0) {
                    $payoutRule = $this->get_payout_rule($getCIBR[0]['offer_id'], $request->event_id);
                    $arr_valid_offer_events = $this->get_valid_offer_events($getCIBR[0]['offer_id']);
                    $arrBrowserData = $this->getBrowswerData();
                    $device_type = ucfirst($arrBrowserData['device_type']);

                    if (in_array($request->event_id, $arr_valid_offer_events)) {

                        $ot = new OfferTracking();
                        $ot->offer_id = $getCIBR[0]['offer_id'];
                        $ot->aff_id = $getCIBR[0]['aff_id'];
                        $ot->offer_name = $getCIBR[0]['offer_name'];
                        $ot->country = $getCIBR[0]['country'];
                        $ot->adv_id = $getCIBR[0]['adv_id'];

                        // Check if payoutRule exists
                        if (count($payoutRule) > 0) {
                            $ot->revenue = $payoutRule[0]['revenue_pricing'];
                            $ot->payout = $payoutRule[0]['payout_pricing'];
                            $ot->currency = $payoutRule[0]['currency'];
                            $ot->revenue_usd = \App\Utils\CurrencyUtils::getNewCurrencyValue($payoutRule[0]['revenue_pricing'], $payoutRule[0]['currency']);
                            $ot->payout_usd = \App\Utils\CurrencyUtils::getNewCurrencyValue($payoutRule[0]['payout_pricing'], $payoutRule[0]['currency']);
                        } else {
                            // Fallback or 0 if no rule found? Original code assumed it exists.
                            $ot->revenue = 0;
                            $ot->payout = 0;
                            $ot->currency = 'USD';
                            $ot->revenue_usd = 0;
                            $ot->payout_usd = 0;
                        }

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

                        $ot->source = $request->source ?? NULL;
                        $ot->google_aid = $request->google_aid ?? NULL;
                        $ot->iosidfa = $request->iosidfa ?? NULL;
                        $ot->android_id = $request->android_id ?? NULL;

                        $ot->timestamp = now(); // Helper now() instead of NOW() macro if not defined

                        $ot->save();

                        $resp['success'] = 'Y';
                    } else {
                        $resp['success'] = 'N';
                        $resp['message'] = 'This event is not allowed for this offer';
                    }
                } else {
                    $resp['success'] = 'N';
                    $resp['message'] = 'Click Details not found';
                }

            }

        } else {
            $resp['success'] = 'N';
            $resp['message'] = 'Your request is illegal';
        }

        return $resp;
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

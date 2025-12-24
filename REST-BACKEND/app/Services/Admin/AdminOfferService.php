<?php

namespace App\Services\Admin;

use App\Models\ActionLogs;
use App\Models\OfferMmpDetail;
use App\Models\MmPlatform;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Offer;
use App\Models\AdModel;
use App\Models\Device;
use App\Models\Category;
use App\Models\OfferAffiliate;
use App\Models\OfferToken;
use App\Models\OfferEvent;
use App\Models\PayoutRule;
use App\Http\Traits\CommonFunctions;
use App\Http\Traits\SystemSettings;

class AdminOfferService
{
    use CommonFunctions, SystemSettings;

    public function getCreateOfferData($user)
    {
        $logged_in_user_type = $user->user_type;

        if ($logged_in_user_type == 'admin') {
            return [
                'module_name' => 'Create Offer',
                'action_url' => 'admin/offer/save',
                'advertisers' => User::where('user_type', 'advertiser')->get(),
                'admodels' => AdModel::all(),
                'offerTokens' => OfferToken::all(),
                'arrCur' => $this->getCurrency(),
                'arrCountry' => $this->getCountry(),
                'arrDevices' => Device::all(),
                'arrAffiliates' => User::where('user_type', 'affiliate')->get(),
                'arrEvents' => [],
                'arrOA' => [],
                'tracking_url' => '',
                'arrCategories' => Category::all(),
                'logged_in_user_type' => $logged_in_user_type
            ];
        } else {
            return ['error' => 'Unauthorized', 'code' => 403];
        }
    }

    public function saveOffer($request)
    {
        $validator = Validator::make($request->all(), [
            'offer_name' => 'required|string',
            'advertiser_id' => 'required|integer',
            'category_id' => 'required|integer',
            'offer_url' => 'required|string',
            'ad_model' => 'required|string',
            'currency' => 'required|string',
            'aff_model' => 'required|string'
        ]);

        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->toArray(), 'code' => 422];
        }

        $offer = new Offer();
        $offer->offer_name = $request->offer_name;
        $offer->advertiser_id = $request->advertiser_id;
        $offer->category_id = $request->category_id;
        $offer->app_id = $request->app_id;
        $offer->external_offer_id = $request->external_offer_id;
        $offer->preview_url = $request->preview_url;
        $offer->offer_url = $request->offer_url;
        $offer->currency = $request->currency;
        $offer->ad_model = $request->ad_model;
        $offer->aff_model = $request->aff_model;
        $offer->ad_pricing = $request->ad_pricing;
        $offer->revenue_usd = $this->getNewCurrencyValue($request->ad_pricing, $request->currency);
        $offer->aff_pricing = $request->aff_pricing;
        $offer->payout_usd = $this->getNewCurrencyValue($request->aff_pricing, $request->currency);
        $offer->campaign_start_date = $request->campaign_start_date;
        $offer->campaign_end_date = $request->campaign_end_date;
        $offer->daily_start_time = $request->daily_start_time;
        $offer->daily_stop_time = $request->daily_stop_time;
        $offer->offer_visibility = $request->offer_visibility;
        $offer->offer_status = $request->offer_status;
        $offer->offer_desc = $request->offer_desc;
        $offer->private_note = $request->private_note;
        $offer->offer_terms = $request->offer_terms;

        $offer->save();

        return [
            'status' => 1,
            'msg' => 'A new offer has been created',
            'offer_id' => $offer->id,
            'code' => 200
        ];
    }

    public function deleteOffer($id)
    {
        Offer::find($id)->delete();
        return ['status' => 1, 'msg' => 'Offer has been deleted'];
    }

    public function restoreOffer($id)
    {
        Offer::withTrashed()->find($id)->restore();
        return ['status' => 1, 'msg' => 'Offer has been restored'];
    }

    public function getEditOfferData($request, $id)
    {
        $offer = Offer::find($id);

        if (!is_null($offer)) {
            $logged_in_user_type = $request->user()->user_type;

            if ($logged_in_user_type == 'admin') {
                $tracking_url = \App\Utils\UrlHelper::getBaseUrl() . "/offer/gourl/" . $offer->id;

                // MMP Data
                $mmpPlatforms = MmPlatform::where('is_active', 'Y')->get();
                $offerMmp = OfferMmpDetail::where('offer_id', $id)->first();

                return [
                    'module_name' => 'Edit Offer',
                    'action_url' => 'admin/offer/update_offer',
                    'offer' => $offer,
                    'offerTokens' => OfferToken::all(),
                    'advertisers' => User::where('user_type', 'advertiser')->get(),
                    'admodels' => AdModel::all(),
                    'arrCur' => $this->getCurrency(),
                    'arrCountry' => $this->getCountry(),
                    'arrDevices' => Device::all(),
                    'arrAffiliates' => User::where('user_type', 'affiliate')->get(),
                    'arrEvents' => OfferEvent::where('offer_id', '=', $id)->get()->toArray(),
                    'arrOA' => OfferAffiliate::select('*')->where('offer_id', '=', $id)->get()->toArray(),
                    'eventsForAffiliates' => PayoutRule::select('affiliate_id', 'event_id')->where('offer_id', $id)->get()->toArray(),
                    'tracking_url' => $tracking_url,
                    'arrCategories' => Category::all(),
                    'mmpPlatforms' => $mmpPlatforms,
                    'offerMmp' => $offerMmp,
                    'logged_in_user_type' => $logged_in_user_type
                ];
            }
        }
        return ['error' => 'Offer not found', 'code' => 404];
    }

    public function updateOffer($request)
    {
        $validator = Validator::make($request->all(), [
            'offer_name' => 'required|string',
            'advertiser_id' => 'required|integer',
            'category_id' => 'required|integer',
            'offer_url' => 'required|string',
            'ad_model' => 'required|string',
            'currency' => 'required|string',
            'aff_model' => 'required|string'
        ]);

        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->toArray(), 'code' => 422];
        }

        $offer = Offer::find($request->offer_id);
        // ... (Update fields similar to saveOffer, plus attribution_link)
        $offer->offer_name = $request->offer_name;
        $offer->advertiser_id = $request->advertiser_id;
        $offer->category_id = $request->category_id;
        $offer->app_id = $request->app_id;
        $offer->external_offer_id = $request->external_offer_id;
        $offer->preview_url = $request->preview_url;
        $offer->offer_url = $request->offer_url;
        $offer->attribution_link = $request->attribution_link;
        $offer->currency = $request->currency;
        $offer->ad_model = $request->ad_model;
        $offer->ad_pricing = $request->ad_pricing;
        $offer->revenue_usd = $this->getNewCurrencyValue($request->ad_pricing, $request->currency);
        $offer->aff_model = $request->aff_model;
        $offer->aff_pricing = $request->aff_pricing;
        $offer->payout_usd = $this->getNewCurrencyValue($request->aff_pricing, $request->currency);
        $offer->campaign_start_date = $request->campaign_start_date;
        $offer->campaign_end_date = $request->campaign_end_date;
        $offer->daily_start_time = $request->daily_start_time;
        $offer->daily_stop_time = $request->daily_stop_time;
        $offer->offer_visibility = $request->offer_visibility;
        $offer->offer_status = $request->offer_status;
        $offer->offer_desc = $request->offer_desc;
        $offer->private_note = $request->private_note;
        $offer->offer_terms = $request->offer_terms;

        $offer->save();

        return ['status' => 1, 'msg' => 'Offer updated successfully', 'code' => 200];
    }

    public function updateTargeting($request)
    {
        $offer = Offer::find($request->offer_id);

        if (!$offer) {
            return ['status' => 0, 'error' => 'Offer not found', 'code' => 404];
        }

        if (strlen(trim($request->target_country)) > 0) {
            $offer->country_permission = $request->country_permission;
            $offer->target_country = $request->target_country;
        }

        if (isset($request->target_state) && strlen(trim($request->target_state)) > 0) {
            $offer->state_permission = $request->state_permission;
            $offer->target_state = $request->target_state;
        }

        if (isset($request->target_city) && strlen(trim($request->target_city)) > 0) {
            $offer->city_permission = $request->city_permission;
            $offer->target_city = $request->target_city;
        }

        if (isset($request->browser) && strlen(trim($request->browser)) > 0) {
            $offer->browser_permission = $request->browser_permission;
            $offer->browser = $request->browser;
        }

        $offer->device_permission = $request->device_permission;
        $offer->target_device = $request->target_device;

        $offer->save();

        return ['status' => 1, 'msg' => 'Offer targeting updated successfully', 'code' => 200];
    }

    public function getOffersByStatus($status)
    {
        $query = DB::table('offers')
            ->join('ad_models', 'offers.ad_model', '=', 'ad_models.id')
            ->select('offers.*', 'ad_models.name');

        if ($status == 'deleted') {
            $query->where('deleted_at', '!=', null);
        } else {
            $query->where('deleted_at', null);

            if ($status == 'approve') {
                $query->where('offer_status', 'approve')
                    ->where(function ($q) {
                        $q->where('campaign_end_date', '>=', NOW())
                            ->orWhere('campaign_end_date', null);
                    });
            } elseif ($status == 'pending') {
                $query->where('offer_status', 'pending');
            } elseif ($status == 'ended') {
                $query->where(function ($q) {
                    $q->where('offer_status', 'ended')
                        ->orWhere('campaign_end_date', '<', NOW());
                });
            }
        }

        $offers = $query->get();
        return ['module_name' => 'Offers ' . ucfirst($status), 'offers' => $offers];
    }

    public function savePayoutRules($request)
    {
        $arr = array();
        $arrW = array();
        $currency = $request->all()['currency'];

        foreach ($request->all() as $k => $v) {
            if ($k == '_token')
                continue;
            elseif ($k == 'event_id' || $k == 'offer_id') {
                $arrW[$k] = $v;
            } elseif ($k == 'affiliate_id' || $k == 'target_country' || $k == 'target_device') {
                $arr[$k] = implode(',', $v);
            } elseif ($k == 'revenue_pricing') {
                $arr['revenue_pricing'] = $v;
                $arr['revenue_usd'] = $this->getNewCurrencyValue($v, $currency);
            } elseif ($k == 'payout_pricing') {
                $arr['payout_pricing'] = $v;
                $arr['payout_usd'] = $this->getNewCurrencyValue($v, $currency);
            } else {
                $arr[$k] = $v;
            }
        }

        DB::table('payout_rules')->updateOrInsert($arrW, $arr);
        return ['status' => 1, 'msg' => 'Payout rules updated successfully', 'code' => 200];
    }

    public function saveTrackingUrl($request)
    {
        $arrParsed['tracking_url'] = $request->tracking_url;
        DB::table('offer_affiliates')->where('offer_id', $request->offer_id)
            ->where('affiliate_id', $request->aff_id)
            ->update($arrParsed);

        return ['status' => 1, 'msg' => 'Tracking URL updated successfully', 'code' => 200];
    }

    public function assignOffer($oid, $aid)
    {
        $turl = \App\Utils\UrlHelper::getBaseUrl() . "/offer/gourl/" . $oid . "/" . $aid . "/?&tid={tid}";
        $query = OfferAffiliate::where('offer_id', $oid)->where('affiliate_id', $aid);

        if ($query->exists()) {
            $query->update(['request_status' => 'Accepted', 'tracking_url' => $turl]);
        } else {
            $oa = new OfferAffiliate();
            $oa->offer_id = $oid;
            $oa->affiliate_id = $aid;
            $oa->tracking_url = $turl;
            $oa->request_made = 'N';
            $oa->request_status = 'Accepted';
            $oa->save();
        }

        return ['status' => 1, 'msg' => 'Offer assigned successfully', 'code' => 200];
    }

    public function rejectOffer($oid, $aid)
    {
        OfferAffiliate::where("offer_id", $oid)
            ->where("affiliate_id", $aid)
            ->update(['request_status' => 'Rejected']);
        return ['status' => 1, 'msg' => 'Offer application rejected', 'code' => 200];
    }

    public function unassignOffer($oid, $aid)
    {
        OfferAffiliate::where("offer_id", $oid)
            ->where("affiliate_id", $aid)
            ->update(['request_status' => 'Unassigned', 'tracking_url' => Null]);
        return ['status' => 1, 'msg' => 'Offer unassigned successfully', 'code' => 200];
    }

    public function saveEvent($request)
    {
        $validator = Validator::make($request->all(), [
            'event_name' => 'required',
            'event_token' => 'required',
            'event_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->toArray(), 'code' => 422];
        }

        $oe = new OfferEvent();
        $oe->offer_id = $request->offer_id;
        $oe->event_name = $request->event_name;
        $oe->event_token = $request->event_token;
        $oe->multi_conv = $request->multi_conv;
        $oe->multi_conv_ip = $request->multi_conv_ip;
        $oe->approval = $request->approval;
        $oe->save();

        return ['status' => 1, 'msg' => 'A new event has been created', 'code' => 200];
    }

    public function updateEvent($request)
    {
        $validator = Validator::make($request->all(), [
            'event_name' => 'required',
            'event_token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->toArray(), 'code' => 422];
        }

        OfferEvent::where('id', $request->event_id)
            ->update([
                'event_name' => $request->event_name,
                'event_token' => $request->event_token,
                'multi_conv' => $request->multi_conv,
                'multi_conv_ip' => $request->multi_conv_ip,
                'approval' => $request->approval
            ]);

        return ['status' => 1, 'msg' => 'Event updated successfully', 'code' => 200];
    }

    public function saveMmpDetails($request)
    {
        $rules = [
            'offer_id' => 'required|integer',
            'mmp_id' => 'required|integer',
            'attribution_url' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->first()];
        }

        try {
            // Update or Create MMP Detail
            OfferMmpDetail::updateOrCreate(
                ['offer_id' => $request->offer_id],
                [
                    'mmp_id' => $request->mmp_id,
                    'attribution_url' => $request->attribution_url
                ]
            );

            // Sync to main Offers table for TrackingService compatibility
            Offer::where('id', $request->offer_id)->update(['attribution_link' => $request->attribution_url]);

            return ['status' => 1, 'msg' => 'MMP Details saved successfully', 'code' => 200];

        } catch (\Exception $e) {
            return ['status' => 0, 'error' => 'Failed: ' . $e->getMessage(), 'code' => 500];
        }
    }
}

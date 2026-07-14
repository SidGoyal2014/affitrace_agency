<?php

namespace App\Services\Admin;

use App\Models\ActionLogs;
use App\Models\OfferMmpDetail;
use App\Models\MmPlatform;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

        if ($logged_in_user_type == \App\Enums\UserType::Admin->value || $logged_in_user_type == \App\Enums\UserType::Employee->value) {
            $advQuery = User::where('user_type', \App\Enums\UserType::Advertiser->value);
            if ($user->isAdvertiserManager()) {
                $advQuery->where('employee_id', (string) $user->id);
            }

            return [
                'module_name' => 'Create Offer',
                'action_url' => 'admin/offer/save',
                'advertisers' => $advQuery->get(),
                'admodels' => AdModel::all(),
                'offerTokens' => OfferToken::all(),
                'arrCur' => $this->getCurrency(),
                'arrCountry' => $this->getCountry(),
                'arrDevices' => Device::all(),
                'arrAffiliates' => User::where('user_type', \App\Enums\UserType::Affiliate->value)->get(),
                'arrEvents' => [],
                'arrOA' => [],
                'tracking_url' => '',
                'arrCategories' => Category::where('is_active', 'Y')->orderBy('category_name')->get(),
                'logged_in_user_type' => $logged_in_user_type
            ];
        } else {
            return ['error' => 'Unauthorized', 'code' => 403];
        }
    }

    public function saveOffer($request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->isAdvertiserManager()) {
            $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            if (!in_array($request->advertiser_id, $assignedAdvIds)) {
                return ['status' => 0, 'error' => 'Unauthorized action.', 'code' => 403];
            }
        }

        $validator = Validator::make($request->all(), [
            'offer_name' => 'required|string',
            'advertiser_id' => 'required|integer',
            'category_id' => 'required|integer',
            'offer_url' => 'required|string',
            'ad_model' => 'required|string',
            'currency' => 'required|string',
            'aff_model' => 'required|string',
            'conversion_tracking_method' => 'in:s2s,image_pixel,iframe_pixel,script_pixel',
            'clicks_cap' => 'nullable|integer|min:0',
            'conversions_cap' => 'nullable|integer|min:0',
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
        $offer->conversion_tracking_method = $request->conversion_tracking_method ?? 's2s';
        $offer->clicks_cap = isset($request->clicks_cap) && $request->clicks_cap !== '' ? (int) $request->clicks_cap : null;
        $offer->conversions_cap = isset($request->conversions_cap) && $request->conversions_cap !== '' ? (int) $request->conversions_cap : null;

        $offer->save();

        // Audit Log: Offer created
        \App\Models\AuditLog::create([
            'actor_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'offer_created',
            'details' => [
                'offer_id' => $offer->id,
                'offer_name' => $offer->offer_name,
                'advertiser_id' => $offer->advertiser_id,
                'category_id' => $offer->category_id,
            ],
            'ip_address' => request()->ip(),
        ]);

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

        // Audit Log: Offer deleted
        \App\Models\AuditLog::create([
            'actor_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'offer_deleted',
            'details' => [
                'offer_id' => $id,
            ],
            'ip_address' => request()->ip(),
        ]);

        return ['status' => 1, 'msg' => 'Offer has been deleted'];
    }

    public function restoreOffer($id)
    {
        Offer::withTrashed()->find($id)->restore();

        // Audit Log: Offer restored
        \App\Models\AuditLog::create([
            'actor_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'offer_restored',
            'details' => [
                'offer_id' => $id,
            ],
            'ip_address' => request()->ip(),
        ]);

        return ['status' => 1, 'msg' => 'Offer has been restored'];
    }

    public function getEditOfferData($request, $id)
    {
        $offer = Offer::find($id);

        if (!is_null($offer)) {
            $user = $request->user();
            $logged_in_user_type = $user->user_type;

            if ($logged_in_user_type == \App\Enums\UserType::Admin->value || $logged_in_user_type == \App\Enums\UserType::Employee->value) {
                if ($user->isAdvertiserManager()) {
                    $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                    if (!in_array($offer->advertiser_id, $assignedAdvIds)) {
                        return ['error' => 'Unauthorized', 'code' => 403];
                    }
                }

                $tracking_url = \App\Utils\UrlHelper::getBaseUrl() . "/offer/gourl/" . $offer->id;

                // MMP Data
                $mmpPlatforms = MmPlatform::where('is_active', 'Y')->get();
                $offerMmp = OfferMmpDetail::where('offer_id', $id)->first();

                $advQuery = User::where('user_type', \App\Enums\UserType::Advertiser->value);
                if ($user->isAdvertiserManager()) {
                    $advQuery->where('employee_id', (string) $user->id);
                }

                return [
                    'module_name' => 'Edit Offer',
                    'action_url' => 'admin/offer/update_offer',
                    'offer' => $offer,
                    'offerTokens' => OfferToken::all(),
                    'advertisers' => $advQuery->get(),
                    'admodels' => AdModel::all(),
                    'arrCur' => $this->getCurrency(),
                    'arrCountry' => $this->getCountry(),
                    'arrDevices' => Device::all(),
                    'arrAffiliates' => User::where('user_type', \App\Enums\UserType::Affiliate->value)->get(),
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
            'aff_model' => 'required|string',
            'conversion_tracking_method' => 'in:s2s,image_pixel,iframe_pixel,script_pixel',
            'clicks_cap' => 'nullable|integer|min:0',
            'conversions_cap' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->toArray(), 'code' => 422];
        }

        $offer = Offer::find($request->offer_id);
        if (!$offer) {
            return ['status' => 0, 'error' => 'Offer not found', 'code' => 404];
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->isAdvertiserManager()) {
            $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            if (!in_array($offer->advertiser_id, $assignedAdvIds) || !in_array($request->advertiser_id, $assignedAdvIds)) {
                return ['status' => 0, 'error' => 'Unauthorized action.', 'code' => 403];
            }
        }
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
        $offer->conversion_tracking_method = $request->conversion_tracking_method ?? 's2s';
        $offer->clicks_cap = isset($request->clicks_cap) && $request->clicks_cap !== '' ? (int) $request->clicks_cap : null;
        $offer->conversions_cap = isset($request->conversions_cap) && $request->conversions_cap !== '' ? (int) $request->conversions_cap : null;

        $offer->save();

        // Audit Log: Offer updated
        \App\Models\AuditLog::create([
            'actor_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'offer_updated',
            'details' => [
                'offer_id' => $offer->id,
                'offer_name' => $offer->offer_name,
                'advertiser_id' => $offer->advertiser_id,
                'category_id' => $offer->category_id,
            ],
            'ip_address' => request()->ip(),
        ]);


        return ['status' => 1, 'msg' => 'Offer updated successfully', 'code' => 200];
    }

    public function updateTargeting($request)
    {
        $offer = Offer::find($request->offer_id);

        if (!$offer) {
            return ['status' => 0, 'error' => 'Offer not found', 'code' => 404];
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->isAdvertiserManager()) {
            $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            if (!in_array($offer->advertiser_id, $assignedAdvIds)) {
                return ['status' => 0, 'error' => 'Unauthorized action.', 'code' => 403];
            }
        }

        if (strlen(trim($request->target_country)) > 0) {
            $offer->country_permission = $request->country_permission;
            //$offer->target_country = $request->target_country;
            $val = $request->target_country;
            $offer->target_country = is_array($val) ? implode(',', array_filter($val)) : $val;

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

        // Audit Log: Offer targeting updated
        \App\Models\AuditLog::create([
            'actor_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'offer_targeting_updated',
            'details' => [
                'offer_id' => $offer->id,
                'target_country' => $offer->target_country,
                'target_device' => $offer->target_device,
            ],
            'ip_address' => request()->ip(),
        ]);

        return ['status' => 1, 'msg' => 'Offer targeting updated successfully', 'code' => 200];
    }

    public function getAllOffers($status = null)
    {
        $query = DB::table('offers')
            ->join('ad_models', DB::raw("CAST(NULLIF(offers.ad_model, '') AS BIGINT)"), '=', 'ad_models.id')
            ->leftJoin('users', DB::raw("CAST(NULLIF(offers.advertiser_id, '') AS BIGINT)"), '=', 'users.id')
            ->select('offers.*', 'ad_models.name', 'users.name as advertiser_name', 'users.id as user_advertiser_id');

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->isAdvertiserManager()) {
            $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            $query->whereIn('offers.advertiser_id', $assignedAdvIds);
        }

        if ($status != null) {

            if ($status == 'deleted') {
                $query->where('deleted_at', '!=', null);

            } else {
                $query->where('deleted_at', null);

                if ($status == \App\Enums\OfferStatus::Approved->value) {
                    $query->where('offer_status', \App\Enums\OfferStatus::Approved->value)
                        ->where(function ($q) {
                            $q->where('campaign_end_date', '>=', NOW())
                                ->orWhere('campaign_end_date', null);
                        });
                } elseif ($status == \App\Enums\OfferStatus::Pending->value) {
                    $query->where('offer_status', \App\Enums\OfferStatus::Pending->value);
                } elseif ($status == \App\Enums\OfferStatus::Ended->value) {
                    $query->where(function ($q) {
                        $q->where('offer_status', \App\Enums\OfferStatus::Ended->value)
                            ->orWhere('campaign_end_date', '<', NOW());
                    });
                }
            }
        }

        $offers = $query->get();
        return ['module_name' => 'Offers ' . ucfirst($status), 'offers' => $offers];

    }//getAllOffers


    public function getOffersByStatus($status)
    {
        $query = DB::table('offers')
            ->join('ad_models', DB::raw("CAST(NULLIF(offers.ad_model, '') AS BIGINT)"), '=', 'ad_models.id')
            ->leftJoin('users', DB::raw("CAST(NULLIF(offers.advertiser_id, '') AS BIGINT)"), '=', 'users.id')
            ->select('offers.*', 'ad_models.name', 'users.name as advertiser_name', 'users.id as user_advertiser_id');

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->isAdvertiserManager()) {
            $assignedAdvIds = User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            $query->whereIn('offers.advertiser_id', $assignedAdvIds);
        }

        if ($status == 'deleted') {
            $query->where('deleted_at', '!=', null);
        } else {
            $query->where('deleted_at', null);

            if ($status == \App\Enums\OfferStatus::Approved->value) {
                $query->where('offer_status', \App\Enums\OfferStatus::Approved->value)
                    ->where(function ($q) {
                        $q->where('campaign_end_date', '>=', NOW())
                            ->orWhere('campaign_end_date', null);
                    });
            } elseif ($status == \App\Enums\OfferStatus::Pending->value) {
                $query->where('offer_status', \App\Enums\OfferStatus::Pending->value);
            } elseif ($status == \App\Enums\OfferStatus::Ended->value) {
                $query->where(function ($q) {
                    $q->where('offer_status', \App\Enums\OfferStatus::Ended->value)
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

        // Audit Log: Offer payout rules updated
        \App\Models\AuditLog::create([
            'actor_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'offer_payout_rules_saved',
            'details' => [
                'offer_id' => $arrW['offer_id'] ?? null,
                'event_id' => $arrW['event_id'] ?? null,
                'affiliate_id' => $arr['affiliate_id'] ?? null,
            ],
            'ip_address' => request()->ip(),
        ]);

        return ['status' => 1, 'msg' => 'Payout rules updated successfully', 'code' => 200];
    }

    public function saveTrackingUrl($request)
    {
        $arrParsed['tracking_url'] = $request->tracking_url;
        DB::table('offer_affiliates')->where('offer_id', $request->offer_id)
            ->where('affiliate_id', $request->aff_id)
            ->update($arrParsed);

        // Audit Log: Tracking URL updated
        \App\Models\AuditLog::create([
            'actor_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'offer_tracking_url_updated',
            'details' => [
                'offer_id' => $request->offer_id,
                'affiliate_id' => $request->aff_id,
            ],
            'ip_address' => request()->ip(),
        ]);

        return ['status' => 1, 'msg' => 'Tracking URL updated successfully', 'code' => 200];
    }

    public function assignOffer($oid, $aid)
    {
        $turl = \App\Utils\UrlHelper::getBaseUrl() . "/offer/gourl/" . $oid . "/" . $aid . "/?&tid={tid}";
        $query = OfferAffiliate::where('offer_id', $oid)->where('affiliate_id', $aid);

        if ($query->exists()) {
            $query->update(['request_status' => 'Accepted', 'request_made' => 'N', 'tracking_url' => $turl]);
        } else {
            $oa = new OfferAffiliate();
            $oa->offer_id = $oid;
            $oa->affiliate_id = $aid;
            $oa->tracking_url = $turl;
            $oa->request_made = 'N';
            $oa->request_status = 'Accepted';
            $oa->save();
        }

        // Audit Log: Offer assigned
        \App\Models\AuditLog::create([
            'actor_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'offer_assigned',
            'details' => [
                'offer_id' => $oid,
                'affiliate_id' => $aid,
            ],
            'ip_address' => request()->ip(),
        ]);


        return ['status' => 1, 'msg' => 'Offer assigned successfully', 'code' => 200];
    }

    public function rejectOffer($oid, $aid)
    {
        OfferAffiliate::where("offer_id", $oid)
            ->where("affiliate_id", $aid)
            ->update(['request_status' => 'Rejected']);

        // Audit Log: Offer application rejected
        \App\Models\AuditLog::create([
            'actor_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'offer_application_rejected',
            'details' => [
                'offer_id' => $oid,
                'affiliate_id' => $aid,
            ],
            'ip_address' => request()->ip(),
        ]);

        return ['status' => 1, 'msg' => 'Offer application rejected', 'code' => 200];
    }

    public function unassignOffer($oid, $aid)
    {
        OfferAffiliate::where("offer_id", $oid)
            ->where("affiliate_id", $aid)
            ->update(['request_status' => 'Unassigned', 'tracking_url' => Null]);

        // Audit Log: Offer unassigned
        \App\Models\AuditLog::create([
            'actor_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'offer_unassigned',
            'details' => [
                'offer_id' => $oid,
                'affiliate_id' => $aid,
            ],
            'ip_address' => request()->ip(),
        ]);

        return ['status' => 1, 'msg' => 'Offer unassigned successfully', 'code' => 200];
    }

    public function saveEvent($request)
    {
        $multiConv = $request->input('multi_conv', 'allow');
        $multiConvIp = $request->input('multi_conv_ip', 'allow');
        $approval = $request->input('approval', 'allow');

        $validator = Validator::make($request->all(), [
            'offer_id' => 'required|integer|exists:offers,id',
            'event_name' => 'required|string',
            'event_token' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9_]+$/',
                Rule::unique('offer_events', 'event_token')->where(function ($q) use ($request) {
                    return $q->where('offer_id', $request->offer_id);
                }),
            ],
            'revenue' => 'nullable|numeric|min:0',
            'payout' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->toArray(), 'code' => 422];
        }

        $oe = new OfferEvent();
        $oe->offer_id = $request->offer_id;
        $oe->event_name = $request->event_name;
        $oe->event_token = $request->event_token;
        $oe->revenue = $request->revenue;
        $oe->payout = $request->payout;
        $oe->multi_conv = $multiConv;
        $oe->multi_conv_ip = $multiConvIp;
        $oe->approval = $approval;
        $oe->save();

        // Audit Log: Offer event created
        \App\Models\AuditLog::create([
            'actor_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'offer_event_created',
            'details' => [
                'offer_id' => $oe->offer_id,
                'event_id' => $oe->id,
                'event_name' => $oe->event_name,
                'event_token' => $oe->event_token,
            ],
            'ip_address' => request()->ip(),
        ]);

        return ['status' => 1, 'msg' => 'A new event has been created', 'code' => 200];
    }

    public function updateEvent($request)
    {
        $eventId = $request->input('event_id', $request->input('id'));
        $multiConv = $request->input('multi_conv', 'allow');
        $multiConvIp = $request->input('multi_conv_ip', 'allow');
        $approval = $request->input('approval', 'pending');

        $event = OfferEvent::find($eventId);

        if (!$event) {
            return ['status' => 0, 'error' => ['event_id' => ['Event not found']], 'code' => 404];
        }

        $validator = Validator::make($request->all(), [
            'event_name' => 'required|string',
            'event_token' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9_]+$/',
                Rule::unique('offer_events', 'event_token')
                    ->ignore($event->id)
                    ->where(function ($q) use ($event) {
                        return $q->where('offer_id', $event->offer_id);
                    }),
            ],
            'revenue' => 'nullable|numeric|min:0',
            'payout' => 'nullable|numeric|min:0',
        ]);

        $validator->after(function ($validator) use ($eventId) {
            if (!$eventId || !filter_var($eventId, FILTER_VALIDATE_INT)) {
                $validator->errors()->add('event_id', 'The event id field is required.');
            }
        });

        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->toArray(), 'code' => 422];
        }

        $event->update([
            'event_name' => $request->event_name,
            'event_token' => $request->event_token,
            'revenue' => $request->revenue,
            'payout' => $request->payout,
            'multi_conv' => $multiConv,
            'multi_conv_ip' => $multiConvIp,
            'approval' => $approval,
        ]);

        // Audit Log: Offer event updated
        \App\Models\AuditLog::create([
            'actor_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'offer_event_updated',
            'details' => [
                'offer_id' => $event->offer_id,
                'event_id' => $event->id,
                'event_name' => $request->event_name,
                'event_token' => $request->event_token,
            ],
            'ip_address' => request()->ip(),
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


    public function cloneOffer($id)
    {
        $source = Offer::find($id);
        if (!$source) {
            return ['status' => 0, 'error' => 'Offer not found', 'code' => 404];
        }

        $clone = $source->replicate();          // copies all Eloquent attributes
        $clone->offer_name = $source->offer_name . ' (Copy)';
        $clone->offer_status = 'pending';  // requires re-approval
        $clone->clicks_cap = null;       // reset usage caps
        $clone->conversions_cap = null;
        $clone->attribution_link = null;       // reset generated tracking link
        $clone->save();

        // Audit Log: Offer cloned
        \App\Models\AuditLog::create([
            'actor_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'offer_cloned',
            'details' => [
                'source_offer_id' => $id,
                'cloned_offer_id' => $clone->id,
                'offer_name' => $clone->offer_name,
            ],
            'ip_address' => request()->ip(),
        ]);

        return [
            'status' => 1,
            'msg' => 'Offer cloned successfully',
            'offer_id' => $clone->id,
            'code' => 200
        ];
    }



}

<?php

namespace App\Services\Affiliate;

use Illuminate\Support\Facades\DB;
use App\Models\OfferToken;
use App\Models\Offer;
use App\Models\OfferAffiliate;
use App\Enums\OfferStatus;

class AffiliateOfferService
{
    public function getBrowseOffers($affiliateId)
    {
        return DB::table('offers')
            ->leftJoin('categories', 'categories.id', '=', 'offers.category_id')
            ->leftJoin('ad_models', DB::raw("CAST(NULLIF(offers.ad_model, '') AS BIGINT)"), '=', 'ad_models.id')
            ->leftJoin('offer_affiliates as oa', function ($join) use ($affiliateId) {
                $join->on('offers.id', '=', 'oa.offer_id')
                    ->where('oa.affiliate_id', '=', $affiliateId);
            })
            ->select(
                'offers.*',
                'categories.category_name',
                'ad_models.name as model_name',
                'oa.request_status',
                'oa.request_made'
            )
            ->whereNull('offers.deleted_at')
            ->where('offers.offer_status', OfferStatus::Approved->value)
            ->orderByDesc('offers.id')
            ->get()
            ->toArray();
    }

    public function sendAccessRequest($offerId, $affiliateId)
    {
        $offer = Offer::where('id', $offerId)
            ->where('offer_status', OfferStatus::Approved->value)
            ->first();

        if (!$offer) {
            return ['status' => 0, 'message' => 'Offer not found or unavailable', 'code' => 404];
        }

        $offerAffiliate = OfferAffiliate::where('offer_id', $offerId)
            ->where('affiliate_id', $affiliateId)
            ->first();

        if ($offerAffiliate) {
            if (strcasecmp((string) $offerAffiliate->request_status, 'Accepted') === 0) {
                return ['status' => 1, 'message' => 'Offer is already approved for you', 'code' => 200];
            }

            $offerAffiliate->request_made = 'Y';
            $offerAffiliate->request_status = 'Pending';
            $offerAffiliate->tracking_url = null;
            $offerAffiliate->save();

            return ['status' => 1, 'message' => 'Access request sent successfully', 'code' => 200];
        }

        $rec = new OfferAffiliate();
        $rec->offer_id = $offerId;
        $rec->affiliate_id = $affiliateId;
        $rec->request_made = 'Y';
        $rec->request_status = 'Pending';
        $rec->tracking_url = null;
        $rec->save();

        return ['status' => 1, 'message' => 'Access request sent successfully', 'code' => 200];
    }

    /**
     * Get offers assigned to the specific affiliate.
     * Logic adapted from CommonOfferFunctions trait.
     */
    public function getOffersApproved($affiliateId, $eai)
    {
        $offers = DB::table('offers')
            ->leftJoin('ad_models', DB::raw("CAST(NULLIF(offers.ad_model, '') AS BIGINT)"), '=', 'ad_models.id')
            ->join('offer_affiliates', 'offers.id', '=', 'offer_affiliates.offer_id')
            ->select('offers.*', 'ad_models.name as model_name')
            ->where('offer_affiliates.affiliate_id', $affiliateId)
            ->get()->toArray();

        foreach ($offers as $offer) {
            $offer->payout_usd = \App\Utils\CurrencyUtils::convertFromUsdToDefault($offer->payout_usd);
            $offer->revenue_usd = \App\Utils\CurrencyUtils::convertFromUsdToDefault($offer->revenue_usd);
        }

        return $offers;
    }

    /**
     * Get tracking URL details and event payouts for a specific offer and affiliate.
     */
    public function getTrackingUrlDetails($offerId, $affiliateId, $eai)
    {
        $offerTokens = OfferToken::all();

        $offer = DB::table('offers')
            ->leftJoin('ad_models', DB::raw("CAST(NULLIF(offers.ad_model, '') AS BIGINT)"), '=', 'ad_models.id')
            ->join('offer_affiliates', 'offers.id', '=', 'offer_affiliates.offer_id')
            ->leftJoin('categories', 'categories.id', '=', 'offers.category_id')
            ->select('offers.*', 'ad_models.name as model_name', 'offer_affiliates.tracking_url', 'category_name')
            ->where('offer_affiliates.affiliate_id', $affiliateId)
            ->where('offer_affiliates.offer_id', $offerId)
            ->get();

        if (isset($offer[0]) && !empty($offer[0]->tracking_url) && $eai) {
            $sep = str_contains($offer[0]->tracking_url, '?') ? '&' : '?';
            $offer[0]->tracking_url .= $sep . 'eai=' . $eai;
        }

        $sql = "Select
                    *
                FROM
                    offer_events OE INNER JOIN payout_rules PR
                    
                    ON 
                    
                    OE.id = PR.event_id

                WHERE 
                    ('" . $affiliateId . "' = ANY(STRING_TO_ARRAY(PR.affiliate_id, ',')) OR PR.affiliate_id = '" . $affiliateId . "')
                AND
                    PR.offer_id = " . $offerId;

        $offerEvents = DB::select($sql);
        foreach ($offerEvents as $evt) {
            $evt->payout_usd = \App\Utils\CurrencyUtils::convertFromUsdToDefault($evt->payout_usd ?? 0);
        }

        return [
            'module_name' => 'Tracking Url',
            'offer' => isset($offer[0]) ? $offer[0] : null,
            'offerEvents' => $offerEvents,
            'offerTokens' => $offerTokens
        ];
    }
}

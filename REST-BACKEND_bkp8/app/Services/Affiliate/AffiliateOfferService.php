<?php

namespace App\Services\Affiliate;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\OfferToken;

class AffiliateOfferService
{
    /**
     * Get offers assigned to the specific affiliate.
     * Logic adapted from CommonOfferFunctions trait.
     */
    public function getOffersApproved($affiliateId, $eai)
    {
        $offers = DB::table('offers')
            ->join('ad_models', 'offers.ad_model', '=', 'ad_models.id')
            ->join('offer_affiliates', 'offers.id', '=', 'offer_affiliates.offer_id')
            ->select('offers.*', 'ad_models.name')
            ->where('offer_affiliates.affiliate_id', $affiliateId)
            ->get()->toArray();

        // Append EAI to tracking links if they exist (though this method doesn't select tracking_url yet?)
        // The frontend for 'Offers Approved' has a 'Get Link' button that calls tracking_url endpoint.
        // But the user said "offer url doesn't have the eai... in the below link".
        // The link provided was `offers_approved.php`.
        // The frontend table doesn't show the link directly, it shows a button.
        // So the EAI is likely missing in the MODAL which calls `tracking_url`.
        // I will update this method anyway if needed, but the main fix is in `getTrackingUrlDetails`.

        return $offers;
    }

    /**
     * Get tracking URL details and event payouts for a specific offer and affiliate.
     */
    public function getTrackingUrlDetails($offerId, $affiliateId, $eai)
    {
        $offerTokens = OfferToken::all();

        $offer = DB::table('offers')
            ->join('ad_models', 'offers.ad_model', '=', 'ad_models.id')
            ->join('offer_affiliates', 'offers.id', '=', 'offer_affiliates.offer_id')
            ->join('categories', 'categories.id', '=', 'offers.category_id')
            ->select('offers.*', 'ad_models.name', 'offer_affiliates.tracking_url', 'category_name')
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
                    " . $affiliateId . " IN (PR.affiliate_id) 
                AND
                    PR.offer_id = " . $offerId;

        $offerEvents = DB::select($sql);

        return [
            'module_name' => 'Tracking Url',
            'offer' => isset($offer[0]) ? $offer[0] : null,
            'offerEvents' => $offerEvents,
            'offerTokens' => $offerTokens
        ];
    }
}

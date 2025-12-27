<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\Integrations\Offer18Provider;
use App\Services\Integrations\TrackierProvider;

class IntegrationService
{
    public function getProvider($providerName)
    {
        switch ($providerName) {
            case 'offer18':
                return new Offer18Provider();
            case 'trackier':
                return new TrackierProvider();
            default:
                throw new \Exception("Unknown provider: $providerName");
        }
    }

    public function syncData($integrationId)
    {
        $integration = Integration::findOrFail($integrationId);

        $provider = $this->getProvider($integration->provider);
        $provider->setConfig([
            'api_key' => $integration->api_key,
            'api_secret' => $integration->api_secret,
            'account_id' => $integration->account_id,
            'api_domain' => $integration->api_domain
        ]);

        if (!$provider->testConnection()) {
            throw new \Exception("Connection failed. Check credentials.");
        }

        DB::beginTransaction();
        try {
            // Sync Advertisers (Create Users with role 'advertiser')
            $this->syncAdvertisers($provider, $integration);

            // Sync Affiliates (Create Users with role 'affiliate')
            $this->syncAffiliates($provider, $integration);

            // Sync Offers
            $this->syncOffers($provider, $integration);

            $integration->last_sync_at = now();
            $integration->save();

            DB::commit();
            return ['status' => true, 'message' => 'Sync completed successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function syncAdvertisers($provider, $integration)
    {
        $advertisers = $provider->getAdvertisers();
        foreach ($advertisers as $advData) {
            // Mapping Logic depending on provider response structure
            // Assuming generic fields for now
            $email = $advData['email'] ?? ($advData['id'] . '@placeholder.com'); // Offer18 might not return email in list

            $user = User::where('email', $email)->first();
            if (!$user) {
                // If checking by external ID
                $user = User::where('external_user_id', $advData['id'])->first();
            }

            if (!$user) {
                $user = new User();
                $user->email = $email;
                $user->password = bcrypt('Password@123'); // Default password
                $user->user_type = 'advertiser';
            }

            $user->name = $advData['name'] ?? $advData['company'] ?? 'Unknown Advertiser';
            $user->company = $advData['company'] ?? '';
            $user->phone = $advData['phone'] ?? $advData['mobile'] ?? null;
            $user->address = $advData['address'] ?? null;
            $user->city = $advData['city'] ?? null;
            $user->state = $advData['state'] ?? null;
            $user->country = $advData['country'] ?? null;
            $user->zipcode = $advData['zipcode'] ?? $advData['zip'] ?? null;
            $user->skype = $advData['skype'] ?? $advData['im'] ?? null;

            // Map Status
            $status = strtolower($advData['status'] ?? 'active');
            $user->status = ($status == 'active' || $status == 'approved') ? 'Active' : (($status == 'pending') ? 'Pending' : 'Blocked');

            $user->external_user_id = $advData['id'];
            $user->save();
        }
    }

    protected function syncAffiliates($provider, $integration)
    {
        $affiliates = $provider->getAffiliates();
        foreach ($affiliates as $affData) {
            $email = $affData['email'] ?? ($affData['id'] . '@placeholder.com');

            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = User::where('external_user_id', $affData['id'])->first();
            }

            if (!$user) {
                $user = new User();
                $user->email = $email;
                $user->password = bcrypt('Password@123');
                $user->user_type = 'affiliate';
            }

            $user->name = $affData['name'] ?? 'Unknown Affiliate';
            $user->company = $affData['company'] ?? '';
            $user->phone = $affData['phone'] ?? $affData['mobile'] ?? null;
            $user->address = $affData['address'] ?? null;
            $user->city = $affData['city'] ?? null;
            $user->state = $affData['state'] ?? null;
            $user->country = $affData['country'] ?? null;
            $user->zipcode = $affData['zipcode'] ?? $affData['zip'] ?? null;
            $user->skype = $affData['skype'] ?? $affData['im'] ?? null;

            // Map Status
            $status = strtolower($affData['status'] ?? 'active');
            $user->status = ($status == 'active' || $status == 'approved') ? 'Active' : (($status == 'pending') ? 'Pending' : 'Blocked');

            $user->external_user_id = $affData['id'];
            $user->save();
        }
    }

    protected function syncOffers($provider, $integration)
    {
        $offers = $provider->getOffers();
        foreach ($offers as $offerData) {
            // Try to find by external ID
            $offer = Offer::where('external_offer_id', $offerData['id'])->first();

            // Or by name if strict checking needed, but ID is safer
            if (!$offer) {
                $offer = new Offer();
                $offer->external_offer_id = $offerData['id'];

                // Set default fields required for creation
                $offer->created_at = now();
            }

            // Map Fields
            $offer->offer_name = $offerData['name'] ?? $offerData['title'] ?? 'Imported Offer';
            $offer->offer_desc = $offerData['description'] ?? '';
            $offer->preview_url = $offerData['preview_url'] ?? '';
            $offer->offer_url = $offerData['tracking_link'] ?? $offerData['tracking_url'] ?? '';

            // Pricing (Need careful mapping of Revenue/Payout)
            // Offer18/Trackier usually provide arrays for payouts, taking default/first for now
            $offer->aff_pricing = $offerData['default_payout'] ?? 0;
            $offer->ad_pricing = $offerData['default_revenue'] ?? 0;
            $offer->currency = $offerData['currency'] ?? 'USD';

            // Check for valid decimal values
            if (!is_numeric($offer->aff_pricing))
                $offer->aff_pricing = 0;
            if (!is_numeric($offer->ad_pricing))
                $offer->ad_pricing = 0;

            // Status Map
            $statusMap = [
                'active' => 'approve',
                'paused' => 'pending',
                'deleted' => 'deleted',
                'expired' => 'ended'
            ];
            $extStatus = strtolower($offerData['status'] ?? 'active');
            $offer->offer_status = $statusMap[$extStatus] ?? 'pending';

            // Default Values for other required fields
            if (!$offer->conversion_tracking_method)
                $offer->conversion_tracking_method = 's2s';
            if (!$offer->category_id)
                $offer->category_id = 1; // Default Category

            // Assign default advertiser if relation needed
            // If data contains advertiser_id, find our internal ID
            if (isset($offerData['advertiser_id'])) {
                $internalAdv = User::where('external_user_id', $offerData['advertiser_id'])->first();
                if ($internalAdv) {
                    $offer->advertiser_id = $internalAdv->id;
                }
            }

            // Fallback advertiser if none found
            if (!$offer->advertiser_id) {
                // Ideally should find a default 'System' advertiser or create one
                // For now, leaving as is or setting validation rule handling
            }

            $offer->save();
        }
    }
}

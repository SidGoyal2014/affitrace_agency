<?php

namespace App\Services\Advertiser;

use Illuminate\Support\Facades\DB;
use App\Http\Traits\CommonOfferFunctions;

class AdvertiserOfferService
{
    use CommonOfferFunctions;

    public function getMyOffers($advertiser_id)
    {
        if (empty($advertiser_id)) {
            throw new \Exception("Advertiser ID is missing.");
        }

        $sql = "
                SELECT 
                    offers.*, 
                    categories.category_name,
                    offers.id as oid,
                    (SELECT COUNT(*) FROM conversions WHERE offer_id = offers.id) as conversions,
                    (SELECT COUNT(*) FROM offer_affiliates WHERE offer_id = offers.id) as affiliates_count
                FROM 
                    offers 
                LEFT JOIN 
                    categories 
                ON 
                    offers.category_id = categories.id 
                WHERE 
                    offers.advertiser_id = ?";

        try {
            $offers = DB::select($sql, [$advertiser_id]);
        } catch (\Exception $e) {
            throw new \Exception("SQL Error in getMyOffers: " . $e->getMessage());
        }

        $baseUrl = \App\Utils\UrlHelper::getBaseUrl();

        return [
            'module_name' => 'My Offers',
            'base_url' => $baseUrl,
            'offers' => $offers
        ];
    }

    public function getApprovedOffers($advertiser_id)
    {
        if (empty($advertiser_id)) {
            throw new \Exception("Advertiser ID is missing.");
        }

        $sql = "
                SELECT 
                    offers.*, 
                    categories.category_name,
                    offers.id as oid,
                    (SELECT COUNT(*) FROM conversions WHERE offer_id = offers.id) as conversions
                FROM 
                    offers 
                LEFT JOIN 
                    categories 
                ON 
                    offers.category_id = categories.id 
                WHERE 
                    offers.advertiser_id = ?
                    AND offers.offer_status = 'Approved'";

        try {
            $offers = DB::select($sql, [$advertiser_id]);
        } catch (\Exception $e) {
            throw new \Exception("SQL Error in getApprovedOffers: " . $e->getMessage());
        }

        $baseUrl = \App\Utils\UrlHelper::getBaseUrl();

        return [
            'module_name' => 'Approved Offers',
            'base_url' => $baseUrl,
            'offers' => $offers
        ];
    }
}

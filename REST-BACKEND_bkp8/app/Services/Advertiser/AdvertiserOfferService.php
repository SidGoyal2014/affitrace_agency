<?php

namespace App\Services\Advertiser;

use Illuminate\Support\Facades\DB;
use App\Http\Traits\CommonOfferFunctions;

class AdvertiserOfferService
{
    use CommonOfferFunctions;

    public function getMyOffers($advertiser_id)
    {
        $sql = "
                SELECT 
                    offers.*, 
                    categories.category_name,
                    offers.id as oid,
                    (SELECT COUNT(*) FROM offer_trackings WHERE offer_id = offers.id) as conversions,
                    (SELECT COUNT(*) FROM offer_affiliates WHERE offer_id = offers.id) as affiliates_count
                FROM 
                    offers 
                LEFT JOIN 
                    categories 
                ON 
                    offers.category_id = categories.id 
                WHERE 
                    offers.advertiser_id = " . $advertiser_id;

        $offers = DB::select($sql);

        return [
            'module_name' => 'My Offers',
            'offers' => $offers
        ];
    }

    public function getApprovedOffers($advertiser_id)
    {
        $sql = "
                SELECT 
                    offers.*, 
                    categories.category_name,
                    offers.id as oid,
                    (SELECT COUNT(*) FROM offer_trackings WHERE offer_id = offers.id) as conversions
                FROM 
                    offers 
                LEFT JOIN 
                    categories 
                ON 
                    offers.category_id = categories.id 
                WHERE 
                    offers.advertiser_id = " . $advertiser_id . "
                    AND offers.offer_status = 'Approved'";

        $offers = DB::select($sql);

        return [
            'module_name' => 'Approved Offers',
            'offers' => $offers
        ];
    }
}

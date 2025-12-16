<?php

namespace App\Http\Controllers\Advertiser;

use DB, Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Offer;
use App\Models\OfferToken;

use App\Http\Traits\CommonOfferFunctions;

class AdvertiserOfferController extends Controller
{

    use CommonOfferFunctions;

    public function my_offers()
    {

        try {

            $advertiser_id = Auth::id();

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

            return response()->json([
                'module_name' => 'My Offers',
                'offers' => $offers
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }


    public function approved_offers()
    {

        try {

            $advertiser_id = Auth::id();

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

            return response()->json([
                'module_name' => 'Approved Offers',
                'offers' => $offers
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

}

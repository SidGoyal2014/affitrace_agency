<?php

namespace App\Http\Controllers\Affiliate;

use DB, Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Offer;
use App\Models\OfferToken;

use App\Http\Traits\CommonOfferFunctions;

class AffiliateOfferController extends Controller
{

	use CommonOfferFunctions;

	public function offers_approved()
	{

		try {

			$offers = $this->getOffersAssignedToAffiliate(Auth::id());

			return response()->json([
				'module_name' => 'Offers Approved',
				'offers' => $offers
			]);

		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}

	}//offers_approved


	public function tracking_url($oid)
	{

		try {

			$offerTokens = OfferToken::all();

			$offer = DB::table('offers')
				->join('ad_models', 'offers.ad_model', '=', 'ad_models.id')
				->join('offer_affiliates', 'offers.id', '=', 'offer_affiliates.offer_id')
				->join('categories', 'categories.id', '=', 'offers.category_id')
				->select('offers.*', 'ad_models.name', 'offer_affiliates.tracking_url', 'category_name')
				->where('offer_affiliates.affiliate_id', Auth::id())
				->where('offer_affiliates.offer_id', $oid)
				->get();

			$sql = "Select
						*
					FROM
						offer_events OE INNER JOIN payout_rules PR
						
						ON 
						
						OE.id = PR.event_id

					WHERE 
						" . Auth::id() . " IN (PR.affiliate_id) 
					AND
						PR.offer_id = " . $oid;

			$offerEvents = DB::select($sql);

			return response()->json([
				'module_name' => 'Tracking Url',
				'offer' => isset($offer[0]) ? $offer[0] : null,
				'offerEvents' => $offerEvents,
				'offerTokens' => $offerTokens
			]);


		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}

	}//tracking_url


}//AffiliateOfferController
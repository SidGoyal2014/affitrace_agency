<?php

namespace App\Http\Controllers\Affiliate;

use DB, Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Offer;
use App\Models\OfferToken;

use App\Http\Traits\CommonOfferFunctions;

class AffiliateOfferController extends Controller{

	use CommonOfferFunctions;

	public function offers_approved(){

        try {

			$offers = $this->getOffersAssignedToAffiliate(Auth::id());
			
			return view('affiliate.offers.list_offers')
				->with('module_name', 'Offers Approved')
				->with('offers', $offers);


        }catch( \Exception $e ){

			return redirect('login')->withErrors($e->getMessage());

        }

	}//offers_approved


	public function tracking_url($oid){

	
        try {

			$offerTokens = OfferToken::all();

			$offer = DB::table('offers')
				->join('ad_models', 'offers.ad_model','=','ad_models.id')
				->join('offer_affiliates', 'offers.id','=','offer_affiliates.offer_id')
				->join('categories', 'categories.id','=','offers.category_id')
				->select('offers.*', 'ad_models.name', 'offer_affiliates.tracking_url', 'category_name')
				->where('offer_affiliates.affiliate_id', Auth::id())
				->where('offer_affiliates.offer_id', $oid)
				->get();

			/*
			$arr[] = 4; $arr[] = 5; $arr[] = 6;
			$offerEvents = DB::table('offer_events')
				->select('*')
				->join('payout_rules', 'offer_events.id','=','payout_rules.event_id')
				->where(Auth::id(), 'IN', 'payout_rules.affiliate_id')
				->where('payout_rules.offer_id', $oid)
				->get();

			dd($offerEvents);
			*/


			$sql = "Select
						*
					FROM
						offer_events OE INNER JOIN payout_rules PR
						
						ON 
						
						OE.id = PR.event_id

					WHERE 
						".Auth::id()." IN (PR.affiliate_id) 
					AND
						PR.offer_id = ".$oid;

			$offerEvents = DB::select($sql);



			return view('affiliate.offers.tracking_url')
				->with('module_name', 'Tracking Url')
				->with('offer', isset($offer[0])?$offer[0]:new Offer)
				->with('offerEvents', $offerEvents)
				->with('offerTokens', $offerTokens);


        }catch( \Exception $e ){

			return redirect('login')->withErrors($e->getMessage());

        }

	
	}//tracking_url





}//AffiliateOfferController
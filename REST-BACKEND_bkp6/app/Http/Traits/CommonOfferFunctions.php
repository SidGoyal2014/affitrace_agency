<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

use DB, Auth;
use App\Models\Offer;


trait CommonOfferFunctions{


	public function getOffersAssignedToAffiliate($affilate_id){
	
		$offers = DB::table('offers')
			->join('ad_models', 'offers.ad_model','=','ad_models.id')
			->join('offer_affiliates', 'offers.id','=','offer_affiliates.offer_id')
			->select('offers.*', 'ad_models.name')
			->where('offer_affiliates.affiliate_id', $affilate_id)
			->get()->toArray();

		return $offers;
	
	}//getOffersAssignedToAffiliate


	public function getAffiliatePayouts($affilate_id){


		$sql = "SELECT 
					OT.id as otid,
					O.offer_name, 
					M.name as model_name, 
					OT.payout as price,
					OT.currency as ot_currency,
					OT.browser as ot_browser,
					OT.updated_at as date_updated,
					OT.*,
					D.*,
					E.event_name
				FROM 
					offers O 
						LEFT JOIN ad_models M ON O.ad_model = M.id
						LEFT JOIN offer_trackings OT ON O.id = OT.offer_id
						LEFT JOIN devices D ON OT.device_id = D.id
						LEFT JOIN offer_events E ON OT.event_id = E.id
				WHERE 
				 	OT.aff_click_id = OT.tid
				 	AND
				 	OT.aff_id = ".$affilate_id;


		$data = DB::select($sql);

		return $data;

	}//getAffiliatePayouts

	public function getAffiliatePayoutsAfterSearch($affilate_id, $str=''){


		$sql = "SELECT 
					OT.id as otid,
					O.offer_name,
					M.name as model_name,
					OT.payout as price,
					OT.currency as ot_currency,
					OT.browser as ot_browser,
					OT.updated_at as date_updated,
					OT.*,
					D.*,
					E.event_name
				FROM 
					offers O 
						LEFT JOIN ad_models M ON O.ad_model = M.id
						LEFT JOIN offer_trackings OT ON O.id = OT.offer_id
						LEFT JOIN devices D ON OT.device_id = D.id
						LEFT JOIN offer_events E ON OT.event_id = E.id
				WHERE 
				 	OT.aff_click_id = OT.tid 
				 	".$str."
				 	AND
				 	OT.aff_id = ".$affilate_id;

		$data = DB::select($sql);

		return $data;

	}//getAffiliatePayoutsAfterSearch



	public function getPayoutsForOffer($offer_id, $affiliate_id = ''){

		$str = "";

		if($affiliate_id > 0){
			$str = "AND OT.aff_id = ".$affiliate_id;

		}//if

		$sql = "SELECT
					OT.aff_id,
					U.name as affiliate_name,
					O.id as offer_id,
					O.offer_name,
					OT.payout as price,
					OT.currency as ot_currency,
					OT.aff_click_id,
					OT.ip,
					OT.country,
					OT.os,
					OT.browser as ot_browser,
					OT.sub1,					
					OT.updated_at as ot_updated_at,
					D.device_name,
					E.event_name,
					M.name as model_name
				FROM
					offers O 
						LEFT JOIN ad_models M ON O.ad_model = M.id
						LEFT JOIN offer_trackings OT ON O.id = OT.offer_id
						LEFT JOIN users U ON OT.aff_id = U.id
						LEFT JOIN offer_events E ON OT.event_id = E.id
						LEFT JOIN devices D ON OT.device_id = D.id
				WHERE
					OT.aff_click_id = OT.tid
					".$str."
					AND
					OT.offer_id = ".$offer_id;


		$data = DB::select($sql);
		return $data;

	}//getPayoutsForOffer



	function getPayoutOffersAfterSearch($oid, $str, $aid=''){

		$strAid = "";

		if($aid > 0){
			$strAid = "AND OT.aff_id = ".$aid;

		}//if

		$query = "
				SELECT 
					OT.aff_id,
					U.name as affiliate_name,
					O.id as offer_id,
					O.offer_name,
					OT.payout as price,
					OT.currency as ot_currency,
					OT.aff_click_id,
					OT.ip,
					OT.country,
					OT.os,
					OT.browser as ot_browser,
					OT.sub1,
					OT.updated_at as ot_updated_at,
					D.*,
					E.event_name,
					M.name as model_name
				FROM
					offers O 
						LEFT JOIN ad_models M ON O.ad_model = M.id
						LEFT JOIN offer_trackings OT ON O.id = OT.offer_id
						LEFT JOIN users U ON OT.aff_id = U.id
						LEFT JOIN offer_events E ON OT.event_id = E.id
						LEFT JOIN devices D ON OT.device_id = D.id
				WHERE 
					OT.aff_click_id = OT.tid 
					".$str."
					".$strAid."
					AND OT.offer_id = ".$oid;


		$data = DB::select($query);
		return $data;


	}//getPayoutOffersAfterSearch

}//CommonOfferFunctions

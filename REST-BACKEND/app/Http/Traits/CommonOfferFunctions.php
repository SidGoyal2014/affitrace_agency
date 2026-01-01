<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

use DB, Auth;
use App\Models\Offer;


trait CommonOfferFunctions
{


	public function getOffersAssignedToAffiliate($affilate_id)
	{

		$offers = DB::table('offers')
			->join('ad_models', 'offers.ad_model', '=', 'ad_models.id')
			->join('offer_affiliates', 'offers.id', '=', 'offer_affiliates.offer_id')
			->select('offers.*', 'ad_models.name')
			->where('offer_affiliates.affiliate_id', $affilate_id)
			->get()->toArray();

		return $offers;

	}//getOffersAssignedToAffiliate


	public function getAffiliatePayouts($affilate_id)
	{

		$sql = "SELECT 
					C.id as conv_id,
					O.offer_name, 
					M.name as model_name, 
					C.payout as price,
					C.currency as ot_currency,
					C.browser as ot_browser,
					C.created_at as date_updated,
					C.*,
					D.device_name,
					C.event as event_name
				FROM 
					conversions C 
						LEFT JOIN offers O ON C.offer_id = O.id
						LEFT JOIN ad_models M ON O.ad_model = M.id
						LEFT JOIN devices D ON C.device_id = D.id
				WHERE 
				 	C.affiliate_id = " . $affilate_id;

		$data = DB::select($sql);

		return $data;

	}//getAffiliatePayouts

	public function getAffiliatePayoutsAfterSearch($affilate_id, $str = '')
	{
		// Convert OT. references in $str to C.
		$str = str_replace('OT.', 'C.', $str);

		$sql = "SELECT 
					C.id as conv_id,
					O.offer_name,
					M.name as model_name,
					C.payout as price,
					C.currency as ot_currency,
					C.browser as ot_browser,
					C.created_at as date_updated,
					C.*,
					D.device_name,
					C.event as event_name
				FROM 
					conversions C 
						LEFT JOIN offers O ON C.offer_id = O.id
						LEFT JOIN ad_models M ON O.ad_model = M.id
						LEFT JOIN devices D ON C.device_id = D.id
				WHERE 
				 	C.affiliate_id = " . $affilate_id . "
				 	" . $str;

		$data = DB::select($sql);

		return $data;

	}//getAffiliatePayoutsAfterSearch



	public function getPayoutsForOffer($offer_id, $affiliate_id = '')
	{

		$str = "";

		if ($affiliate_id > 0) {
			$str = "AND C.affiliate_id = " . $affiliate_id;

		}//if

		$sql = "SELECT
					C.affiliate_id as aff_id,
					U.name as affiliate_name,
					O.id as offer_id,
					O.offer_name,
					C.payout as price,
					C.currency as ot_currency,
					C.aff_click_id,
					C.country,
					C.os,
					C.browser as ot_browser,
					C.aff_sub1 as sub1,					
					C.created_at as ot_updated_at,
					D.device_name,
					C.event as event_name,
					M.name as model_name
				FROM
					conversions C 
						LEFT JOIN offers O ON C.offer_id = O.id
						LEFT JOIN ad_models M ON O.ad_model = M.id
						LEFT JOIN users U ON C.affiliate_id = U.id
						LEFT JOIN devices D ON C.device_id = D.id
				WHERE
					C.offer_id = " . $offer_id . "
					" . $str;


		$data = DB::select($sql);
		return $data;

	}//getPayoutsForOffer



	function getPayoutOffersAfterSearch($oid, $str, $aid = '')
	{
		// Convert OT. references in $str to C.
		$str = str_replace('OT.', 'C.', $str);

		$strAid = "";

		if ($aid > 0) {
			$strAid = "AND C.affiliate_id = " . $aid;

		}//if

		$query = "
				SELECT 
					C.affiliate_id as aff_id,
					U.name as affiliate_name,
					O.id as offer_id,
					O.offer_name,
					C.payout as price,
					C.currency as ot_currency,
					C.aff_click_id,
					C.country,
					C.os,
					C.browser as ot_browser,
					C.aff_sub1 as sub1,
					C.created_at as ot_updated_at,
					D.device_name,
					C.event as event_name,
					M.name as model_name
				FROM
					conversions C 
						LEFT JOIN offers O ON C.offer_id = O.id
						LEFT JOIN ad_models M ON O.ad_model = M.id
						LEFT JOIN users U ON C.affiliate_id = U.id
						LEFT JOIN devices D ON C.device_id = D.id
				WHERE 
					C.offer_id = " . $oid . "
					" . $str . "
					" . $strAid;


		$data = DB::select($query);
		return $data;


	}//getPayoutOffersAfterSearch

}//CommonOfferFunctions

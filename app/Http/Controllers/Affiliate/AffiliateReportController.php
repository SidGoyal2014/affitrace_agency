<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

use DB, Auth;

use App\Models\User;
use App\Models\AdModel;
use App\Models\Device;
use App\Models\Category;
use App\Models\AffiliateUserDetail;
use App\Models\AdvertiserUserDetail;
use App\Models\OfferTracking;
use App\Models\AffiliatePostbackColumn;

use App\Http\Traits\CommonFunctions;
use App\Http\Traits\CommonOfferFunctions;

class AffiliateReportController extends Controller{

	use CommonFunctions, CommonOfferFunctions;

	/*
	function summary(){

		$sql ="
			SELECT 
                offers.id, 
				offers.offer_name,
				COUNT(DISTINCT(ot.tid)) as total_clicks,
				(
					SELECT 
						COUNT(aff_click_id) 
					FROM
						offer_trackings
					WHERE
						aff_click_id != '' 
							AND
						offer_id = offers.id
							AND
						aff_id = ".Auth::id()." 

				) as total_conversions
			FROM 
				offers 
			LEFT JOIN 
				offer_trackings as ot 
			ON 
				ot.offer_id = offers.id 
			INNER JOIN
				offer_affiliates
			ON
				offer_affiliates.offer_id  = offers.id 
			WHERE
				ot.aff_id = ".Auth::id()." 
			GROUP BY
				offers.id
			Order By 
				total_clicks DESC
		";

		$data = DB::select($sql);

		return view('affiliate.reports.summary')
			->with('data', $data)
			->with('module_name', "Reports Summary");
	
	}//summary



	function offer_clicks($offer_id, $get_conversions=""){


		$data = DB::table('offer_trackings')
				->select(
					'*','offer_trackings.id as otid','offer_trackings.created_at as link_clicked_at',
					'affis.id as aff_id','affis.name as aff_name', 'adverts.id as adv_id', 'adverts.name as advert_name','offer_trackings.country as ot_country', 'offer_trackings.os as ot_os', 'offer_trackings.browser as ot_browser'
				)
				->join('offers', 'offer_trackings.offer_id','=', 'offers.id')
				->join('categories', 'offers.category_id','=', 'categories.id')
				->join('devices', 'offer_trackings.device_id','=', 'devices.id')				
				->join('users as affis', 'offer_trackings.aff_id','=', 'affis.id')
				->join('users as adverts', 'offer_trackings.adv_id','=', 'adverts.id')
				->where('offers.id', $offer_id)
				->where('offer_trackings.aff_id', Auth::id());



		if($get_conversions == "Y"){
			$data = $data->where('offer_trackings.aff_click_id','!=' ,'');

		}//if

		$data = $data->orderBy('offer_trackings.id', 'desc')
				->get();



		$x= "";
		$record_exists="";
		$advert_name = "";
		if(isset($data[0]->offer_name)){
			$x= "Click details for Offer: ".$data[0]->offer_name;
			$record_exists="Y";
			$advert_name=$data[0]->advert_name;
		}


	

		return view('affiliate.reports.offer_clicks')
			->with('data', $data)
			->with('offer_id', $offer_id)
			->with('aid', '')
			->with('oid', '')
			->with('search_url', 'affiliate/reports/search_offer_clicks')			
			->with('record_exists', $record_exists)
			->with('show_advrt_name', 'Y')
			->with('arrDevices', Device::all())
			->with('arrCategories', Category::all())
			->with('arrCountry',  $this->getCountry())			
			->with('advrt_name', "Advertiser Name: ".$advert_name)
			->with('module_name', $x);



	
	}//offer_clicks
	*/


	function summary(){

		$sql_1 ="
			SELECT 
                offers.id, 
				offers.offer_name,
				COUNT(DISTINCT(ot.tid)) as total_clicks
			FROM 
				offers 
			LEFT JOIN 
				offer_trackings as ot 
			ON 
				ot.offer_id = offers.id 
			INNER JOIN
				offer_affiliates
			ON
				offer_affiliates.offer_id  = offers.id 
			WHERE
				ot.aff_id = ".Auth::id()." 
			GROUP BY
				offers.id
			Order By 
				total_clicks DESC
		";

		$sql_2 ="

			SELECT 
				offer_id,
				COUNT(aff_click_id) as total_conversions
			FROM
				offer_trackings
			WHERE
				aff_click_id != '' 
					AND
				aff_id = ".Auth::id()." 

			group by
				offer_id;
		";

		$data_1 = DB::select($sql_1);
		$data_2 = DB::select($sql_2);

		//pr($data_1);
		//pr($data_2);

		$tempArr = array();

	    foreach($data_2 as $obj) {

	    	$tempArr[$obj->offer_id] = $obj->total_conversions;
        
	    }//foreach

		//pr($tempArr);

		$data = [];

		foreach ($data_1 as $item1) {

	        if (isset($tempArr[$item1->id]) && $tempArr[$item1->id] > 0) {

	            $combined = (object) [
	                'id' => $item1->id,
	                'offer_name' => $item1->offer_name,
	                'total_clicks' => $item1->total_clicks,
	                'total_conversions' => $tempArr[$item1->id]
	            ];

	        }else{

	            $combined = (object) [
	                'id' => $item1->id,
	                'offer_name' => $item1->offer_name,
	                'total_clicks' => $item1->total_clicks,
	                'total_conversions' => 0
	            ];
	        }

            $data[] = $combined;
	            

		}//foreach

		//pr($data);

		return view('affiliate.reports.summary')
			->with('data', $data)
			->with('module_name', "Reports Summary");
	
	}//summary


	function offer_clicks(Request $request, $offer_id, $get_conversions=""){


	    if ($request->ajax()) {


			$data = DB::table('offer_trackings')
					->select(
						'*','offer_trackings.id as otid','offer_trackings.created_at as link_clicked_at',
						'affis.id as aff_id','affis.name as aff_name', 'adverts.id as adv_id', 'adverts.name as advert_name','offer_trackings.country as ot_country', 'offer_trackings.os as ot_os', 'offer_trackings.browser as ot_browser'
					)
					->join('offers', 'offer_trackings.offer_id','=', 'offers.id')
					->join('categories', 'offers.category_id','=', 'categories.id')
					->join('devices', 'offer_trackings.device_id','=', 'devices.id')				
					->join('users as affis', 'offer_trackings.aff_id','=', 'affis.id')
					->join('users as adverts', 'offer_trackings.adv_id','=', 'adverts.id')
					->where('offers.id', $offer_id)
					->where('offer_trackings.created_at', date('Y-m-d'))
					->where('offer_trackings.aff_id', Auth::id());


			if($get_conversions == "Y"){
				$data = $data->where('offer_trackings.aff_click_id','!=' ,'');

			}//if

	        return DataTables::of($data)
	            ->addColumn('click_info', function ($row) {
	                return "<strong>Click Id:</strong><br />{$row->tid}<br /><br /><strong>Aff Click Id:</strong><br />{$row->aff_click_id}";
	            })
	            ->editColumn('link_clicked_at', function ($row) {
	                return date("d-m-Y H:i:s", strtotime($row->link_clicked_at));
	            })
	            ->rawColumns(['click_info'])
	            ->make(true);


	    }//if


	    $offer_data = DB::table('offers')->where('id', $offer_id)->first();
		$x= "Click details for Offer: ".$offer_data->offer_name;
		$record_exists="Y";

	

		return view('affiliate.reports.offer_clicks')
			//->with('data', $data)
			->with('offer_id', $offer_id)
			->with('aid', '')
			->with('oid', '')
			->with('search_url', 'affiliate/reports/search_offer_clicks')			
			->with('record_exists', $record_exists)
			->with('show_advrt_name', 'Y')
			->with('arrDevices', Device::all())
			->with('arrCategories', Category::all())
			->with('arrCountry',  $this->getCountry())			
			->with('advrt_name', "")//Advertiser Name: .$advert_name)
			->with('module_name', $x);



	
	}//offer_clicks


	function search_offer_clicks(Request $request){
//DB::enableQueryLog();

//pr($request->all());


		$data = DB::table('offer_trackings')
				->select(
					'*','offer_trackings.id as otid','offer_trackings.created_at as link_clicked_at',
					'affis.id as aff_id','affis.name as aff_name', 'adverts.id as adv_id', 'adverts.name as advert_name','offer_trackings.country as ot_country', 'offer_trackings.os as ot_os', 'offer_trackings.browser as ot_browser'
				)
				->join('offers', 'offer_trackings.offer_id','=', 'offers.id')
				->join('categories', 'offers.category_id','=', 'categories.id')
				->join('devices', 'offer_trackings.device_id','=', 'devices.id')
				->join('users as affis', 'offer_trackings.aff_id','=', 'affis.id')
				->join('users as adverts', 'offer_trackings.adv_id','=', 'adverts.id')
				->where('offers.id', $request->offer_id)
				->where('offer_trackings.aff_id', Auth::id());

		if($request->from != "" && $request->to != "" && $request->from == $request->to){
			$data = $data->where('offer_trackings.created_at', $request->from);

		}elseif($request->from != "" && $request->to != ""){
			$data = $data->whereBetween('offer_trackings.created_at', [$request->from, $request->to]);		

		}//if-else


		$arrC = $this->getCountry();

		if(isset($request->country) && $request->country != ""){
			$data = $data->where('offer_trackings.country', $arrC[$request->country]);

		}

		if($request->os != ""){
			$data = $data->where('offer_trackings.os', 'LIKE', "%{$request->os}%");

		}


		if($request->browser != ""){
			$data = $data->where('offer_trackings.browser', $request->browser);

		}


		if($request->device != ""){
			$data = $data->where('offer_trackings.device_id', $request->device);

		}


		if($request->category != ""){
			$data = $data->where('offers.category_id', $request->category);

		}

		$data = $data->orderBy('offer_trackings.id', 'desc')
				->get();

		$x= "";
		$record_exists="";
		$advert_name = "";
		if(isset($data[0]->offer_name)){
			$x= "Click details for Offer: ".$data[0]->offer_name;
			$record_exists="Y";
			$advert_name=$data[0]->advert_name;
		}


//DB::getQueryLog();

		return view('affiliate.reports.offer_clicks_search')
			->with('data', $data)
			->with('offer_id', $request->offer_id)
			->with('from', $request->from)
			->with('to', $request->to)
			->with('aid', '')
			->with('oid', '')
			->with('search_url', 'affiliate/reports/search_offer_clicks')				
			->with('record_exists', $record_exists)
			->with('show_advrt_name', 'Y')
			->with('arrDevices', Device::all())
			->with('arrCategories', Category::all())
			->with('arrCountry',  $this->getCountry())			
			->with('advrt_name', "Advertiser Name: ".$advert_name)
			->with('module_name', $x);



	
	}//search_offer_clicks



	/*

		public function transfer_data_OLD(){

			$users = User::select('id')->where('user_type', 'affiliate')->get()->toArray();
			foreach($users as $key=>$val){
				//echo "===========>".$val['id'];
				$this->get_json($val['id'], "Y");

			}//foreach

		}//transfer_data_OLD


		public function get_json($aid="", $ft=""){

			//ft == for transfer

			if($aid == ""){
				$aid = Auth::id();
			}//if


			try{

				$cols = AffiliatePostbackColumn::where('uid', $aid)->get()->toArray();

				$trackings = "";//OfferTracking::where('aff_id', $aid)->get()->toArray();

				if($ft == "Y"){
					// when we are transferring data then select which are not transferred
					$trackings = OfferTracking::where('aff_id', $aid)->where('is_transferred', 'no')->get()->toArray();
				}else{

					$trackings = OfferTracking::where('aff_id', $aid)->get()->toArray();
				}

				//pr($trackings);
				//die;

				$cols_new = $this->get_required_cols($cols);

				$finalArr = $this->get_final_array($trackings, $cols_new);

				$this->send_data("jSON", $finalArr, $cols[0]['url']);

				if($ft == "Y"){

					$str = "";

					foreach($trackings as $key2=>$arr2){
						$str .= $arr2['id'].",";

					}//foreach

					$str = substr($str, 0, -1);
					$sql = "UPDATE offer_trackings SET is_transferred = 'yes' WHERE id IN(".$str.")";

					DB::select($sql);


				}//if


	        }catch( \Exception $e ){
	            //return response()->json(['error'=>$e->getMessage()], 500);
	        }



		}//get_json

		private function get_final_array($trackings, $cols_new){

			// Traverse each trackings data array and insert values as per required columns to get final array

			$finalArr = array();

			$ctr = 0;

			foreach($trackings as $key2=>$arr2){

				foreach(array_flip($cols_new) as $kay=>$val){

					$finalArr[$ctr][$kay] = $arr2[$val];

				}//foreach

				$ctr++;

			}//foreach

			return $finalArr;

		}//get_final_array


		private function get_required_cols($cols){

			$cols_new = array();

			// To get array with required cols
			foreach($cols as $key1=>$arr1){

				foreach($arr1 as $k=>$v){

					if($k == 'id' || $k == 'oid' || $k == 'eid' || $k == 'uid' || $k == 'url' || $k == 'deep_link' || $k == 'created_at' || $k == 'updated_at'){
						continue;
					}

					if($v != ""){
						$cols_new[$k] = $v;
					}

				}//foreach

			}//foreach

			return $cols_new;

		}//requiredCols

	*/


	public function offer_payouts($oid){//, $aid
	
		//$data = DB::select("select OT.aff_id, U.name as affiliate_name, O.id as offer_id, O.offer_name, O.aff_pricing as price, OT.event_id, OT.aff_click_id, OT.ip, OT.updated_at from offers O LEFT JOIN offer_trackings OT ON O.id = OT.offer_id LEFT JOIN users U ON OT.aff_id = U.id WHERE OT.aff_click_id = OT.tid AND OT.offer_id = ".$oid);


		$data = $this->getPayoutsForOffer($oid, Auth::id());
	
		$show_advrt_name = 'N';
		$of_name = '';

		if(count($data) > 0){

			$dataOffer = DB::select("select offer_name from offers where id =".$oid);

			if(strlen($dataOffer[0]->offer_name) > 0){
				$of_name = "Offer Name: ".$dataOffer[0]->offer_name;
				$show_advrt_name = 'Y';

			}//if

		}//if

		return view('affiliate.reports.payouts_offer')
			->with('data', (count($data) > 0)?$data:array())
			->with('oid', $oid)
			->with('arrDevices', Device::all())
			->with('arrCategories', Category::all())
			->with('arrCountry',  $this->getCountry())				
			->with('search_url', 'affiliate/reports/search_offer_payouts')			
			->with('show_advrt_name', $show_advrt_name)
			->with('advrt_name', $of_name)
			->with('module_name',  "Offer Payouts");



	}//offer_payouts


	public function search_offer_payouts(Request $request){
	
		$oid = $request->oid;
		$aid = Auth::id();

		$str = "";
		if($request->from != "" && $request->to != ""){
			 $str = " AND OT.updated_at BETWEEN '".$request->from."' AND '".$request->to."'";
		}

		$arrC = $this->getCountry();

		if(isset($request->country) && $request->country != ""){
			$str .= " AND OT.country = '".$arrC[$request->country]."'";

		}

		if(isset($request->browser) && $request->browser != ""){
			$str .= " AND OT.browser = '".$request->browser."'";

		}

		if(isset($request->os) && $request->os != ""){
			$str .= " AND OT.os = '".$request->os."'";

		}


		if(isset($request->device) && $request->device != ""){
			$str .= " AND OT.device_id = ".$request->device;

		}

		$data = $this->getPayoutOffersAfterSearch($oid, $str, $aid);


		$show_advrt_name = 'N';
		$of_name = '';

		if(count($data) > 0){

			$dataOffer = DB::select("select offer_name from offers where id =".$oid);
			
			if(strlen($dataOffer[0]->offer_name) > 0){
				$of_name = "Offer Name: <a href='/admin/reports/offer_payouts/".$oid."'>".$dataOffer[0]->offer_name."</a>";
				$show_advrt_name = 'Y';

			}//if

		}//if


		return view('affiliate.reports.payouts_offer')
			->with('data', (count($data) > 0)?$data:array())
			->with('oid', $oid)
			->with('arrDevices', Device::all())
			->with('arrCategories', Category::all())
			->with('arrCountry',  $this->getCountry())
			->with('search_url', 'affiliate/reports/search_offer_payouts')
			->with('from', $request->from)
			->with('to', $request->to)
			->with('show_advrt_name', $show_advrt_name)
			->with('advrt_name', $of_name)
			->with('module_name', "Offer Payouts");


	}//search_offer_payouts
		/**/

	public function payouts(){//, $oid


		$aid = Auth::id();

		$data = $this->getAffiliatePayouts($aid);


		$show_advrt_name = 'N';
		$af_name = '';

		if(count($data) > 0){

			$dataOffer = DB::select("select name from users where id =".$aid);
			
			if(strlen($dataOffer[0]->name) > 0){
				$af_name = "Affiliate Name: ".$dataOffer[0]->name;
				$show_advrt_name = 'Y';

			}//if

		}//if


		return view('affiliate.reports.payouts_affiliate')
			->with('data', $data)
			->with('aid', $aid)
			->with('arrDevices', Device::all())
			->with('arrCategories', Category::all())
			->with('arrCountry',  $this->getCountry())
			->with('search_url', 'affiliate/reports/search_affiliate_payouts')			
			->with('show_advrt_name', $show_advrt_name)
			->with('advrt_name', $af_name)
			->with('module_name', "Affiliate Payouts");

	}//affiliate_payouts


	public function search_affiliate_payouts(Request $request){

		//$aid = $request->aid;
		$aid = Auth::id();

		$str = "";
		if($request->from != "" && $request->to != ""){
			 $str = " AND OT.updated_at BETWEEN '".$request->from."' AND '".$request->to."'";
		}


		$arrC = $this->getCountry();
		
		if(isset($request->country) && $request->country != ""){
			$str .= " AND OT.country = '".$arrC[$request->country]."'";

		}

		if(isset($request->browser) && $request->browser != ""){
			$str .= " AND OT.browser = '".$request->browser."'";

		}

		if(isset($request->os) && $request->os != ""){
			$str .= " AND OT.os = '".$request->os."'";

		}


		if(isset($request->device) && $request->device != ""){
			$str .= " AND OT.device_id = ".$request->device;

		}


		$data = $this->getAffiliatePayoutsAfterSearch($aid, $str);


		$show_advrt_name = 'N';
		$af_name = '';

		if(count($data) > 0){

			$dataOffer = DB::select("select name from users where id =".$aid);
			
			if(strlen($dataOffer[0]->name) > 0){
				$af_name = "Affiliate Name: <a href='/affiliate/reports/payouts'>".$dataOffer[0]->name."</a>";
				$show_advrt_name = 'Y';

			}//if

		}//if



		return view('affiliate.reports.payouts_affiliate')
			->with('data', $data)
			->with('aid', $aid)
			->with('arrDevices', Device::all())
			->with('arrCategories', Category::all())
			->with('arrCountry',  $this->getCountry())				
			->with('from', $request->from)
			->with('to', $request->to)
			->with('show_advrt_name', $show_advrt_name)
			->with('advrt_name', $af_name)
			->with('search_url', 'affiliate/reports/search_affiliate_payouts')
			->with('module_name', "Affiliate Payouts");

	}//search_affiliate_payouts



    
}//AffiliateReportController

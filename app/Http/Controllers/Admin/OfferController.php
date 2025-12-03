<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth, DB, Session;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Offer;
use App\Models\AdModel;
use App\Models\Device;
use App\Models\Category;
use App\Models\OfferAffiliate;
use App\Models\OfferAssignRequest;
use App\Models\OfferToken;
use App\Models\OfferTracking;
use App\Models\OfferEvent;
use App\Models\PayoutRule;

use App\Http\Traits\CommonFunctions;
use App\Http\Traits\SystemSettings;

class OfferController extends Controller{


	use CommonFunctions, SystemSettings;

    /**
     * Display a listing of the resource.
     */
    public function index(){



    }//index


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request){


		$request->session()->forget('is_new_offer_created');
		$request->session()->forget('offer_in_edit_mode');

		$logged_in_user_type = '';

		if(Auth::check()){
			$arrOA = array();
			$arrEvents = array();
			$advertisers = User::all()->where('user_type', 'advertiser');
			$affiliates = User::all()->where('user_type', 'affiliate');
			$admodels = AdModel::all();
			$devices = Device::all();
			$categories = Category::all();
			$offer = new Offer();
			$offerTokens = OfferToken::all();

			$logged_in_user_type = $request->session()->get('user_type');

			if($logged_in_user_type == 'admin'){

				return view('admin.offers.main')
					->with('module_name', 'Create Offer')
					->with('action_url', 'admin/offer/save')
					->with('advertisers', $advertisers)
					->with('admodels', $admodels)
					->with('offer', $offer)
					->with('offerTokens', $offerTokens)
					->with('arrCur',  $this->getCurrency())
					->with('arrCountry',  $this->getCountry())
					->with('arrDevices',  $devices)
					->with('arrAffiliates',  $affiliates)
					->with('arrEvents',  $arrEvents)
					->with('arrOA',  $arrOA)
					->with('tracking_url',  '')
					->with('arrCategories',  $categories)
					->with('logged_in_user_type', $logged_in_user_type);
			
			}


		}

		return redirect('login');

    }//create

    /**
     * Store a newly created resource in storage.
     */
    public function save(Request $request){

		$validator = Validator::make(request()->all(), [
			'offer_name' => 'required|string',
			'advertiser_id' => 'required|integer',
			'category_id' => 'required|integer',
			'offer_url' => 'required|string',
			'ad_model' => 'required|string',
			'currency' => 'required|string',
			'aff_model' => 'required|string'
		]);

		if ($validator->fails()) {
			return redirect('admin/offer/create')->withErrors($validator->messages())->withInput();
			//return response()->json(['status'=> 0, 'error' => $validator->errors()->toArray()]);

		}

		if($this->get_total_offers() >= $this->get_settings("num_offers")){
			return redirect('admin/offer/create')->with('error', "you already reached to the number of offers allowed in your plan. Please upgrade for higher limit.");

		}


		$offer = new Offer();

		$offer->offer_name = $request->offer_name;
		$offer->advertiser_id = $request->advertiser_id;
		$offer->category_id = $request->category_id;
		$offer->app_id = $request->app_id;
		$offer->external_offer_id = $request->external_offer_id;
		$offer->preview_url = $request->preview_url;
		$offer->offer_url = $request->offer_url;
		
		$offer->currency = $request->currency;

		$offer->ad_model = $request->ad_model;		
		$offer->aff_model = $request->aff_model;

		$offer->ad_pricing = $request->ad_pricing;
		$offer->revenue_usd = $request->ad_pricing*$this->getNewCurrencyValue($request->currency, "USD");

		$offer->aff_pricing = $request->aff_pricing;
		$offer->payout_usd = $request->aff_pricing*$this->getNewCurrencyValue($request->currency, "USD");

		$offer->campaign_start_date = $request->campaign_start_date;
		$offer->campaign_end_date = $request->campaign_end_date;
		$offer->daily_start_time = $request->daily_start_time;
		$offer->daily_stop_time = $request->daily_stop_time;
		$offer->offer_visibility = $request->offer_visibility;
		$offer->offer_status = $request->offer_status;
		$offer->offer_desc = $request->offer_desc;
		$offer->private_note = $request->private_note;
		$offer->offer_terms = $request->offer_terms;


		$offer->save();

		$request->session()->put('newly_created_offer_id', $offer->id);
		$request->session()->put('is_new_offer_created', "Yes");

		return redirect('admin/offer/edit/'.$offer->id)->with('success','A new offer has been created');

    }//save


    public function delete_offer(string $id){

        Offer::find($id)->delete();

        return redirect('admin/offer/offers_approved')->with('success','A new offer has been deleted');

    }//delete_offer



    public function restore_offer(string $id){

        Offer::withTrashed()->find($id)->restore();

        return redirect('admin/offer/offers_approved')->with('success','Offer has been restored');

    }//restore_offer


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id){

		$logged_in_user_type = '';


		if(Auth::check()){

			$offer = Offer::find($id);

			if(!is_null($offer)){
			
				$arrOA = OfferAffiliate::select('*')
								->where('offer_id','=', $id)
								->get()
								->toArray();
				
				$arrEvents = OfferEvent::all()->where('offer_id','=', $id)->toArray();

				
				$advertisers = User::all()->where('user_type', 'advertiser');
				$affiliates = User::all()->where('user_type', 'affiliate');
				$admodels = AdModel::all();
				$devices = Device::all();

				$categories = Category::all();
				$offerTokens = OfferToken::all();

				$logged_in_user_type = $request->session()->get('user_type');


				$request->session()->forget('is_new_offer_created');
				$request->session()->put('offer_in_edit_mode', "Yes");

				//$tracking_url = $offer->offer_url."?o=".$offer->id."&cid={replace_it}";

				$eventsForAffiliates = PayoutRule:: select('affiliate_id', 'event_id')->where('offer_id', $id)->get()->toArray();


				$tracking_url = url("offer/gourl")."/".$offer->id;

				if($logged_in_user_type == 'admin'){
				
					return view('admin.offers.main')
							->with('module_name', 'Edit Offer')
							->with('action_url', 'admin/offer/update_offer')
							->with('offer', $offer)
							->with('offerTokens', $offerTokens)
							->with('advertisers', $advertisers)
							->with('admodels', $admodels)
							->with('arrCur',  $this->getCurrency())
							->with('arrCountry',  $this->getCountry())
							->with('arrDevices',  $devices)
							->with('arrAffiliates',  $affiliates)
							->with('arrEvents',  $arrEvents)
							->with('arrOA',  $arrOA)
							->with('eventsForAffiliates', $eventsForAffiliates)
							->with('tracking_url',  $tracking_url)
							->with('arrCategories',  $categories)
							->with('logged_in_user_type', $logged_in_user_type);
				
				}//if

			}else{
			
				return redirect('dashboard');
			
			}

		}

		return redirect('login');

    }//edit


	public function getEventTurlForAffiliate($aff_id){
	
		$arrTurlForEvents = array();

		foreach($eventsForAffiliates as $key=>$arr){

			if(str_contains($arr['affiliate_id'], $obj->id)){

				if(strlen(trim($arrAffIdTurls[$obj->id])) > 0){

					if(str_contains($arrAffIdTurls[$obj->id], '?')){
						$arrTurlForEvents[][$obj->id] = $arrAffIdTurls[$obj->id]."&event_id=".$arr['event_id'];

					}else{
						$arrTurlForEvents[][$obj->id] = $arrAffIdTurls[$obj->id]."?&event_id=".$arr['event_id'];

					}//if-else

				}//if
			
			}//if

		}//foreach



	}//getEventTurlForAffiliate
    /**
     * Update the specified resource in storage.
     */


    public function update_offer(Request $request){


        try {

			$validator = Validator::make(request()->all(), [
				'offer_name' => 'required|string',
				'advertiser_id' => 'required|integer',
				'category_id' => 'required|integer',
				'offer_url' => 'required|string',
				'ad_model' => 'required|string',
				'currency' => 'required|string',
				'aff_model' => 'required|string'
			]);

			if ($validator->fails()) {
				return redirect('admin/offer/edit/'.$request->offer_id)->withErrors($validator->messages())->withInput();

			}


            $offer = Offer::find($request->offer_id);
			
			$offer->offer_name = $request->offer_name;
			$offer->advertiser_id = $request->advertiser_id;
			$offer->category_id = $request->category_id;
			$offer->app_id = $request->app_id;
			$offer->external_offer_id = $request->external_offer_id;
			$offer->preview_url = $request->preview_url;
			$offer->offer_url = $request->offer_url;
			$offer->attribution_link = $request->attribution_link;


			$offer->currency = $request->currency;

			$offer->ad_model = $request->ad_model;

			$offer->ad_pricing = $request->ad_pricing;
			$offer->revenue_usd = $request->ad_pricing*$this->getNewCurrencyValue($request->currency, "USD");

			$offer->aff_model = $request->aff_model;

			$offer->aff_pricing = $request->aff_pricing;
			$offer->payout_usd = $request->aff_pricing*$this->getNewCurrencyValue($request->currency, "USD");

			$offer->campaign_start_date = $request->campaign_start_date;
			$offer->campaign_end_date = $request->campaign_end_date;
			$offer->daily_start_time = $request->daily_start_time;
			$offer->daily_stop_time = $request->daily_stop_time;
			$offer->offer_visibility = $request->offer_visibility;
			$offer->offer_status = $request->offer_status;
			$offer->offer_desc = $request->offer_desc;
			$offer->private_note = $request->private_note;
			$offer->offer_terms = $request->offer_terms;


            $offer->save();

			return redirect('admin/offer/edit/'.$request->offer_id)->with('success', 'Offer updated successfully');


        }catch( \Exception $e ){

			return redirect('admin/offer/edit/'.$request->offer_id)->withErrors($e->getMessage());
        }



    }//update_offer



    public function update_targetting(Request $request){

//pr($request->all());die;
        try {

            $offer = Offer::find($request->offer_id);

            if(strlen(trim($request->target_country)) > 0){

				$offer->country_permission = $request->country_permission;
				$offer->target_country = $request->target_country;

			}


            if(isset($request->target_state) && strlen(trim($request->target_state)) > 0){

				$offer->state_permission = $request->state_permission;
				$offer->target_state = $request->target_state;

			}


            if(isset($request->target_city) && strlen(trim($request->target_city)) > 0){

				$offer->city_permission = $request->city_permission;
				$offer->target_city = $request->target_city;

			}

            if(isset($request->browser) && strlen(trim($request->browser)) > 0){

				$offer->browser_permission = $request->browser_permission;
				$offer->browser = $request->browser;

			}

            //if(strlen(trim($request->target_device)) > 0){

				$offer->device_permission = $request->device_permission;
				$offer->target_device = $request->target_device;

			//}

            $offer->save();

			return redirect('admin/offer/edit/'.$request->offer_id)->with('success', 'Offer updated successfully');


        }catch( \Exception $e ){

			return redirect('admin/offer/edit/'.$request->offer_id)->withErrors($e->getMessage());
        }



    }//update_targetting


	public function offers_approved(){

        try {



/*
		$offer->campaign_start_date = $request->campaign_start_date;
		$offer->campaign_end_date = $request->campaign_end_date;
*/
			$offers = DB::table('offers')
				->join('ad_models', 'offers.ad_model','=','ad_models.id')
				->select('offers.*', 'ad_models.name' )
				->where('offer_status', 'approve')
				->where('deleted_at', null)				
				->where('campaign_end_date', '>=', NOW())
				->orWhere('campaign_end_date', null)
				->get();

			return view('admin.offers.list_offers')
				->with('module_name', 'Offers Approved')
				->with('offers', $offers);

        }catch( \Exception $e ){

			return redirect('login')->withErrors($e->getMessage());
        }

	}//offers_approved


	public function offers_pending(){

        try {

			$offers = DB::table('offers')
				->join('ad_models', 'offers.ad_model','=','ad_models.id')
				->select('offers.*', 'ad_models.name' )
				->where('offer_status', 'pending')
				->where('deleted_at', null)
				->get();

			return view('admin.offers.list_offers')
				->with('module_name', 'Offers Pending')
				->with('offers', $offers);

        }catch( \Exception $e ){

			return redirect('login')->withErrors($e->getMessage());
        }

	}//offers_pending

	public function offers_ended(){

        try {

			$offers = DB::table('offers')
				->join('ad_models', 'offers.ad_model','=','ad_models.id')
				->select('offers.*', 'ad_models.name' )
				->where('offer_status', 'ended')
				->orWhere('campaign_end_date', '<', NOW())
				->orWhere('deleted_at', '!=', null)
				->get();

			return view('admin.offers.list_offers')
				->with('module_name', 'Offers Ended')
				->with('offers', $offers);

        }catch( \Exception $e ){

			return redirect('login')->withErrors($e->getMessage());
        }

	}//offers_ended


	public function offers_deleted(){

        try {

			$offers = DB::table('offers')
				->join('ad_models', 'offers.ad_model','=','ad_models.id')
				->select('offers.*', 'ad_models.name' )
				->where('deleted_at', '!=', null)
				->get();

			return view('admin.offers.list_offers')
				->with('module_name', 'Offers Deleted')
				->with('offers', $offers);

        }catch( \Exception $e ){

			return redirect('login')->withErrors($e->getMessage());
        }

	}//offers_deleted



	public function save_payout_rules(Request $request){
	
		try{
			$arr = array();
			$arrW = array();
			$currency = $request->all()['currency'];

/*echo $currency;
pr($request->all());
die;*/


			foreach($request->all() as $k=>$v){

				if($k == '_token'){
					continue;
				
				}elseif($k == 'event_id' || $k == 'offer_id'){
					$arrW[$k] = $v;

				}elseif($k == 'affiliate_id' || $k == 'target_country' || $k == 'target_device'){
					$arr[$k] = implode(',', $v);

				}elseif($k == 'revenue_pricing'){
					$arr['revenue_pricing'] = $v;
					$arr['revenue_usd'] = $v*$this->getNewCurrencyValue($currency, "USD");

				}elseif($k == 'payout_pricing'){
					$arr['payout_pricing'] = $v;
					$arr['payout_usd'] = $v*$this->getNewCurrencyValue($currency, "USD");

				}else{
					$arr[$k] = $v;

				}

			}//foreach

			
			DB::table('payout_rules')
				->updateOrInsert($arrW, $arr);

			return redirect('admin/offer/edit/'.$request->offer_id)->with('success', 'Payout rules updated successfully');


        }catch( \Exception $e ){

			return redirect('admin/offer/edit/'.$request->offer_id)->with('error', 'Payout rules could not be updated');
        }
	
	}//save_payout_rules

	public function updateTrackingURL(Request $request){

		//OfferTracking::where('offer_id')

		//print_r($request->all());
	
	
	}//updateTrackingURL



	public function stu(Request $request) {
	
		//stu = Save Tracking Url

        try {

			/*
			$purl=parse_url($request->tracking_url);
			parse_str($purl['query'], $arrParsed);
			*/

			$arrParsed['tracking_url'] = $request->tracking_url;

			DB::table('offer_affiliates')->where('offer_id', $request->offer_id)
				->where('affiliate_id', $request->aff_id)
				->update($arrParsed);


			return redirect('admin/offer/edit/'.$request->offer_id)->with('success', 'Offer updated successfully');


        }catch( \Exception $e ){

			return redirect('login')->withErrors($e->getMessage());
        }

	}//stu



	public function show_events($id){

		$arrEvents = OfferEvent::all()->where('offer_id','=', $id)->toArray();

		$strEvents = "";

		foreach($arrEvents as $obj){
			$strEvents .= "<tr>";
				$strEvents .= "<td>".$obj['event_name']."</td>";
				$strEvents .= "<td>".$obj['event_token']."</td>";
				$strEvents .= "<td>";
					$strEvents .= "<a href='#' class='btn btn-primary' data-toggle='modal' data-target='#idEventModal' onClick='transferEventDataInModal(".$obj['offer_id'].",".$obj['id'].")'>Edit</a>";

					$strEvents .= "<a href='#' class='btn btn-warning'  data-toggle='modal' data-target='#idPayoutsModal' onClick='transferEventPayoutRulesDataInModal(".$obj['offer_id'].",".$obj['id'].")'>Payouts</a>";


					$strEvents .= "<a href='javascript:void(0)' class='btn btn-info btn-payout'  data-toggle='modal' data-target='#idEventPostbackModal' onClick='transferEventPostbackDataInModal(".$obj['offer_id'].", ".$obj['id'].",".$obj['event_name'].")'>Postback</a>";

				$strEvents .= "</td>";
			$strEvents .= "</tr>";
		}//foreach

		return $strEvents;

	}//show_events


    public function assign($oid, $aid) {

        try {

			$turl = url('')."/offer/gourl/".$oid."/".$aid."/?&tid={tid}";

			$query = OfferAffiliate::where('offer_id', $oid)
			                     ->where('affiliate_id', $aid);

			if ($query->exists()) {

				$query->update(['request_status' => 'Accepted', 'tracking_url' => $turl]);

			}else{


			    $oa = new OfferAffiliate();

				$oa->offer_id = $oid;
			    $oa->affiliate_id = $aid;
			    $oa->tracking_url = $turl;
			    $oa->request_made = 'N';
			    $oa->request_status = 'Accepted';

			    $oa->save();

			}//if-else

			return redirect('admin/offer/edit/'.$oid)->with('success', 'Offer updated successfully');


        }catch( \Exception $e ){

			return redirect('admin/offer/edit/'.$oid)->withErrors($e->getMessage());
        }


    }//assign






    public function reject($oid, $aid) {


		try{

			OfferAffiliate::where("offer_id",$oid)
							->where("affiliate_id",$aid)
							->update(['request_status'=> 'Rejected']);



			return redirect('admin/offer/edit/'.$oid)->with('success', 'Offer updated successfully');


		 }catch( \Exception $e ){
			return redirect('admin/offer/edit/'.$oid)->withErrors($e->getMessage());
		
		}



    }//reject


    public function unassign($oid, $aid) {


		try{

			OfferAffiliate::where("offer_id",$oid)
							->where("affiliate_id",$aid)
							->update(['request_status'=> 'Pending', 'tracking_url'=> Null]);



			return redirect('admin/offer/edit/'.$oid)->with('success', 'Offer updated successfully');


		 }catch( \Exception $e ){
			return redirect('admin/offer/edit/'.$oid)->withErrors($e->getMessage());
		
		}



    }//unassign


	public function save_event(Request $request){
	
		$rules = array();
		$rules['event_name'] = 'required';
		$rules['event_token'] = 'required';

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()){
			return response()->json(['status'=> 0, 'error' => $validator->errors()->toArray()]);

		}


		$oe = new OfferEvent();
		$oe->offer_id = $request->offer_id;
		$oe->event_name = $request->event_name;
		$oe->event_token = $request->event_token;
		$oe->multi_conv = $request->multi_conv;
		$oe->multi_conv_ip = $request->multi_conv_ip;
		$oe->approval = $request->approval;
		$oe->save();

		if($oe->id > 0){
			return response()->json(['status'=> 1, 'msg' => 'A new event has been created']);

		}
	
	}//save_event


	function get_event($eid){
	
		$event = OfferEvent::find($eid)->toArray();
		return response()->json($event);
	
	}//get_event


	function get_rule($oid, $eid){

		$rule = PayoutRule::where('event_id',$eid)->where('offer_id', $oid)->get()->toArray();
		if(count($rule) > 0){
			return response()->json($rule[0]);
		}else{
			return response()->json(array());
		}

	}//get_rule

	function update_event(Request $request){

		try{

			$rules = array();
			$rules['event_name'] = 'required';
			$rules['event_token'] = 'required';

			$validator = Validator::make($request->all(), $rules);

			if ($validator->fails()) {
				return response()->json(['status'=> 0, 'error' => $validator->errors()->toArray()]);

			}


			$affectedRows = OfferEvent::where('id', $request->event_id)
				->update([
						'event_name' => $request->event_name,
						'event_token' => $request->event_token,
						'multi_conv' => $request->multi_conv,
						'multi_conv_ip' => $request->multi_conv_ip,
						'approval' => $request->approval
				]);


			if($affectedRows > 0){
				return response()->json(['status'=> 1, 'msg' => 'Event updated successfully']);

			}else{
				return response()->json(['status'=> 0, 'error' => "Event could not be updated"]);
			
			}

        }catch( \Exception $e ){

			return response()->json(['status'=> 0, 'error' => $e->getMessage()]);
        }
	
	}//update_event


	function update_event_postback(Request $request){
	
		try{

			$rules = array();
			$rules['postback'] = 'required';
			$rules['event_id'] = 'required';

			$validator = Validator::make($request->all(), $rules);

			if ($validator->fails()) {
				return response()->json(['status'=> 0, 'error' => $validator->errors()->toArray()]);

			}

			$affectedRows = OfferEvent::where('id', $request->event_id)
				->update([
					'postback' => $request->postback
				]);

			if($affectedRows > 0){

				return redirect('admin/offer/edit/'.$request->offer_id)->with('success', 'Event updated successfully');

			}else{

				return redirect('admin/offer/edit/'.$request->offer_id)->with('error', 'Event could not be updated');
			
			}

        }catch( \Exception $e ){

			return response()->json(['status'=> 0, 'error' => $e->getMessage()]);
        }

	
	}//update_event_postback



	function remove_event($oid, $eid){

		try{

			$event = OfferEvent::find($eid);
			$event->delete();

			return redirect('admin/offer/edit/'.$oid)->with('success', 'Offer removed successfully');


        }catch( \Exception $e ){

			return redirect('admin/offer/edit/'.$oid)->withErrors($e->getMessage());
        }
	
	}//remove_event


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

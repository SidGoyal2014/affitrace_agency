<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use DB, Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\User;
use App\Models\AffiliateUserDetail;
use App\Models\OfferToken;
use App\Models\AffiliatePostbackColumn;

use App\Http\Traits\CommonFunctions;
use App\Http\Traits\CommonProfileFunctions;

class AffiliateController extends Controller {

    use CommonFunctions, CommonProfileFunctions;

	public function profile(Request $request){

		if(Auth::check()){

			$logged_in_user_type = $request->session()->get('user_type');

			$aud = AffiliateUserDetail::where('user_id', Auth::id())->first();

			return view('affiliate.profile')
				->with('user', Auth::user())
				->with('aud', $aud)
				->with('uid',  Auth::id())
			    ->with('arrTimeZone',  $this->getTimeZone())
			    ->with('pmtArray',  $this->getPaymentMethod())
		    	->with('arrCur',  $this->getCurrency())
			    ->with('arrCountry',  $this->getCountry())
				->with('title', ucfirst($logged_in_user_type).' Profile')
				->with('module_name', 'Profile')
				->with('form_action_url', url('affiliate/update_profile'))
				->with('logged_in_user_type', $logged_in_user_type);

		}

		return redirect('login');

	}//profile



	function update_profile(Request $request){

		return $this->update_affiliate_profile($request);

	}//update_profile


	public function a_postback_cols(Request $request){

		$cols = array();
        $cols['sub1'] = '';
        $cols['oid'] = '';
        $cols['eid'] = '';
        $cols['uid'] = '';
        $cols['url'] = '';
        $cols['tid'] = '';
        $cols['aff_id'] = Auth::id();
        $cols['event_id'] = '';
        $cols['sub_aff_id'] = '';
        $cols['aff_sub1'] = '';
        $cols['aff_sub2'] = '';
        $cols['aff_sub3'] = '';
        $cols['aff_sub4'] = '';
        $cols['aff_sub5'] = '';
        $cols['source'] = '';
        $cols['android_id'] = '';
        $cols['device_id'] = '';
        $cols['google_aid'] = '';
        $cols['iosidfa'] = '';
        $cols['offer_id'] = '';
        $cols['offer_name'] = '';
        $cols['ip'] = '';
        $cols['country'] = '';
        $cols['os'] = '';
        $cols['os_ver'] = '';
        $cols['raw_useragent'] = '';
        $cols['adv_id'] = '';
        $cols['timestamp'] = '';
        $cols['useragent'] = '';
        $cols['deep_link'] = '';

		if(Auth::check()){

			$logged_in_user_type = $request->session()->get('user_type');

			//$tokens = OfferToken::all()->toArray();
			$tokens = OfferToken::where('token_micro', '!=', 'sub1')->get()->toArray();
			$pCols = AffiliatePostbackColumn::where('uid', Auth::id())->first();//->toArray();

			if(!is_null($pCols)){
				$aCols = $pCols->toArray();

				if(count($aCols) > 0){
					$cols = $aCols;
				}

			}//if




			return view('affiliate.postback_cols') 
				->with('user', Auth::user())
				->with('tokens', $tokens)
				->with('cols', $cols)
				->with('uid',  Auth::id())
				->with('title', ucfirst($logged_in_user_type).' Profile')
				->with('module_name', 'Postback Columns')
				->with('form_action_url', url('affiliate/update_a_postback_cols'))
				->with('logged_in_user_type', $logged_in_user_type);

		}//if

		return redirect('login');

	}//a_postback_cols


	public function update_a_postback_cols(Request $request){

		//echo  $request->all()['uid'];
		//pr($request->all());die;

		try{

			if(Auth::check()){

				$arrCondition = array();
				$arr = array();
				
				foreach($request->all() as $k=>$v){

					if($k == '_token'){
						continue;

					}elseif($k == 'uid'){
						$arrCondition[$k] =  Auth::id();

					}elseif($k == 'oid'){
						$arrCondition[$k] =  '';
					}else{
						$arr[$k] = $v;

					}

				}//foreach



				DB::table('affiliate_postback_columns')
					->updateOrInsert($arrCondition, $arr);



			}//if


			return redirect('affiliate/a_postback_cols')->with('success', 'Postback columns updated successfully');


        }catch( \Exception $e ){

			return redirect('affiliate/a_postback_cols')->with('error', 'Postback columns could not be updated');
        }

		//return redirect('login');

	}//update_a_postback_cols

	/*
	public function postback_cols($oid, Request $request){

		$cols = array();
        $cols['oid'] = '';
        $cols['eid'] = '';
        $cols['uid'] = '';
        $cols['url'] = '';
        $cols['tid'] = '';
        $cols['aff_id'] = '';
        $cols['event_id'] = '';
        $cols['sub_aff_id'] = '';
        $cols['aff_sub1'] = '';
        $cols['aff_sub2'] = '';
        $cols['aff_sub3'] = '';
        $cols['aff_sub4'] = '';
        $cols['aff_sub5'] = '';
        $cols['source'] = '';
        $cols['android_id'] = '';
        $cols['device_id'] = '';
        $cols['google_aid'] = '';
        $cols['iosidfa'] = '';
        $cols['offer_id'] = '';
        $cols['offer_name'] = '';
        $cols['ip'] = '';
        $cols['country'] = '';
        $cols['os'] = '';
        $cols['os_ver'] = '';
        $cols['raw_useragent'] = '';
        $cols['adv_id'] = '';
        $cols['timestamp'] = '';
        $cols['useragent'] = '';
        $cols['deep_link'] = '';

		if(Auth::check()){

			$logged_in_user_type = $request->session()->get('user_type');

			$tokens = OfferToken::all()->toArray();
			$pCols = AffiliatePostbackColumn::where('uid', Auth::id())->where('oid', $oid)->first();//->toArray();

			if(!is_null($pCols)){
				$aCols = $pCols->toArray();

				if(count($aCols) > 0){
					$cols = $aCols;
				}

			}//if

			$cols['oid'] = $oid;


			return view('affiliate.postback_cols') 
				->with('user', Auth::user())
				->with('tokens', $tokens)
				->with('cols', $cols)
				->with('uid',  Auth::id())
				->with('title', ucfirst($logged_in_user_type).' Profile')
				->with('module_name', 'Postback Columns')
				->with('form_action_url', url('affiliate/update_postback_cols'))
				->with('logged_in_user_type', $logged_in_user_type);

		}//if

		return redirect('login');

	}//postback_cols


	public function update_postback_cols(Request $request){

		//echo  $request->all()['uid'];
		//pr($request->all());die;

		$oid = '';
		try{

			if(Auth::check()){

				$arrCondition = array();
				$arr = array();
				
				foreach($request->all() as $k=>$v){

					if($k == '_token'){
						continue;

					}elseif($k == 'uid'){
						$arrCondition[$k] =  Auth::id();

					}elseif($k == 'oid'){
						$arrCondition[$k] =  $v;
						$oid = $v;

					}else{
						$arr[$k] = $v;

					}

				}//foreach


				try{

					DB::table('affiliate_postback_columns')
						->updateOrInsert($arrCondition, $arr);

				}catch(\Exception $e){


				}



			}//if


			return redirect('affiliate/postback_cols/'.$oid)->with('success', 'Postback columns updated successfully');


        }catch( \Exception $e ){

			return redirect('affiliate/postback_cols/'.$oid)->with('error', 'Postback columns could not be updated');
        }

		//return redirect('login');

	}//update_postback_cols
	*/



}//End of class AffiliateController

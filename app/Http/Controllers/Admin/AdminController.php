<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use DB, Auth, Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\User;
use App\Models\AffiliateUserDetail;
use App\Models\AdvertiserUserDetail;
use App\Http\Traits\CommonFunctions;
use App\Http\Traits\CommonProfileFunctions;

use App\Http\Traits\SystemSettings;

class AdminController extends Controller {

	use CommonFunctions, CommonProfileFunctions, SystemSettings;

	public function list_advertisers(){

		$data = DB::table('users')
				->select('*', 'users.id as uid')
				->leftJoin('advertiser_user_details', 'users.id','=', 'advertiser_user_details.user_id')
				->where('user_type', 'advertiser')
				->get();


		return view('admin.list_adv')
			->with('data', $data)
			->with('module_name', "List Advertisers")
			;

	}//list_advertisers


	public function list_affiliates(){

		$data = DB::table('users')
				->select('*', 'users.id as uid')
				->leftJoin('affiliate_user_details', 'users.id','=', 'affiliate_user_details.user_id')
				->where('user_type', 'affiliate')
				->get();

		return view('admin.list_aff')
			->with('data', $data)
			->with('module_name', "List Affiliates")
			;

	}//list_advertisers


	public function profile(Request $request){

		if(Auth::check()){

			$logged_in_user_type = $request->session()->get('user_type');

			return view('admin.profile')
				->with('user', Auth::user())
				->with('title', ucfirst($logged_in_user_type).' Profile')
				->with('module_name', 'Profile')
				->with('logged_in_user_type', $logged_in_user_type);

		}

		return redirect('login');
	
	
	}//profile



	function update_profile(Request $request){

        try {

            $validator = Validator::make(request()->all(), [
                'name' => 'required|string|min:5',
				'email' => 'unique:users,email,'.Auth::id(),
                'password' => 'nullable|string|min:5'
            ]);

            if ($validator->fails()) {
				return redirect('admin/profile')->withErrors($validator->messages())->withInput();

            }

            $user = Auth::user();

            $user->name = $request->name;
            $user->email = $request->email;
			$user->phone = $request->phone;


			if(strlen($request->password) > 0){
				$user->password = Hash::make($request->password);

			}

            $user->save();

			return redirect('admin/profile')->with('success', 'Profile Updated');


        }catch( \Exception $e ){

			return redirect('admin/profile')->withErrors($e->getMessage());
        }


	}//update_profile


	public function edit_affiliate($id){

		$user = User::find($id);

		$user_type = $user->user_type;

		$aud = AffiliateUserDetail::where('user_id', $id)->first();

		if(!isset($aud)){
			$aud = new AffiliateUserDetail();

			$aud->additional_tracking_url_tokens = '';
			$aud->time_zone = '';
			$aud->payment_method = '';
			$aud->payment_details = '';
			$aud->billing_terms = '';
			$aud->add_tax_basic_name = '';
			$aud->add_tax_type = '';
			$aud->add_tax_number = '';
			$aud->add_tax = '';
		}

		return view('admin.affiliate.profile')
			->with('user', $user)
			->with('aud', $aud)
			->with('uid',  $id)
			->with('arrTimeZone',  $this->getTimeZone())
			->with('pmtArray',  $this->getPaymentMethod())
			->with('arrCur',  $this->getCurrency())
			->with('arrCountry',  $this->getCountry())
			->with('title', ucfirst($user_type).' Profile')
			->with('module_name', 'Profile')
			->with('form_action_url', url('admin/update_affiliate'))
			->with('logged_in_user_type', $user_type)
		;
	
	
	}//edit_affiliate


	public function save_affiliate(Request $request){

        try {

            $validator = Validator::make(request()->all(), [
                'name' => 'required|string|min:5',
				'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|string|min:5',
                'company' => 'required|string|min:5',
				'phone' => 'required|digits_between:10,15',
            ]);

            if ($validator->fails()) {
				return redirect('admin/create_affiliate')->withErrors($validator->messages())->withInput();

            }//if



			if($this->get_total_affiliates() >= $this->get_settings("num_affiliates")){
				return redirect('admin/list_affiliates')->with('error', "We are not accepting new affiliates as of now.");

			}//if


			$code = bin2hex(random_bytes(5));
            $user = new User();
            $user->code = $code;
            $user->agency_id = session('agency_id');
			$user->name = $request->name;
			$user->username = str_replace(' ','', strtolower($request->name));
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->user_type = $request->user_type;

			$user->company = $request->company;
			$user->country = $request->country;
			$user->phone = $request->phone;

            $user->save();


			$aud = new AffiliateUserDetail();
			$aud->user_id = $user->id;
			$aud->save();


			return redirect('admin/list_affiliates');





        }catch( \Exception $e ){

        	return redirect('admin/create_affiliate')->withErrors($e->getMessage());

        }

	}//save_affiliate

	public function create_affiliate(){

		return view('admin/affiliate/create_affiliate')
			->with('title', 'Create Affiliate')
			->with('module_name', 'Create Affiliate')
			;

	}//create_affiliate


	public function create_advertiser(){

		return view('admin/advertiser/create_advertiser')
			->with('title', 'Create Advertiser')
			->with('form_action_url', url('doRegister'))
			->with('module_name', 'Create Advertiser')
			;

	}//create_advertiser


	public function save_advertiser(Request $request){

        try {

            $validator = Validator::make(request()->all(), [
                'name' => 'required|string|min:5',
				'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|string|min:5',
                'company' => 'required|string|min:5',
				'phone' => 'required|digits_between:10,15',
            ]);

            if ($validator->fails()) {
            	if($request->user_type == 'advertiser'){
					return redirect('admin/list_advertisers')->withErrors($validator->messages())->withInput();
					
				}

				return redirect('admin/create_advertiser')->withErrors($validator->messages())->withInput();

            }//if


			$code = bin2hex(random_bytes(5));
            $user = new User();
            $user->code = $code;
            $user->agency_id = session('agency_id');
			$user->name = $request->name;
			$user->username = str_replace(' ','', strtolower($request->name));
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->user_type = $request->user_type;

			$user->company = $request->company;
			$user->country = $request->country;
			$user->phone = $request->phone;

            $user->save();

			$adud = new AdvertiserUserDetail();
			$adud->user_id = $user->id;
			$adud->save();

            return redirect('admin/list_advertisers');


        }catch( \Exception $e ){

        	return redirect('admin/create_advertiser')->withErrors($e->getMessage());

        }





	}//save_advertiser






	public function edit_advertiser($id){

		$user = User::find($id);

		$user_type = $user->user_type;

		$adud = AdvertiserUserDetail::where('user_id', $id)->first();
		
		if(!isset($adud)){
			$adud = new AdvertiserUserDetail();
			$adud->currency = '';
			$adud->postback_ip_whitelist = '';
			$adud->time_zone = '';
			$adud->affiliate_block = '';
			$adud->affiliate_whitelist = '';
			$adud->private_note = '';
			$adud->additional_info = '';
			$adud->tax_details = '';
		}

		return view('admin.advertiser.profile')
			->with('user', $user)
			->with('adud', $adud)
			->with('uid',  $id)
			->with('arrTimeZone',  $this->getTimeZone())
			->with('arrCur',  $this->getCurrency())
			->with('arrCountry',  $this->getCountry())
			->with('title', ucfirst($user_type).' Profile')
			->with('module_name', 'Profile')
			->with('form_action_url', url('admin/update_advertiser'))
			->with('logged_in_user_type', $user_type);
	
	
	}//edit_advertiser


	function update_advertiser(Request $request){

		return $this->update_advertiser_profile($request);

	}//update_advertiser



	function update_affiliate(Request $request){

		return $this->update_affiliate_profile($request);

	}//update_affiliate


}//End of class AdminController

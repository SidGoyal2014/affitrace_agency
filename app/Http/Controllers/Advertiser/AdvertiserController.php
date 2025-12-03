<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use DB, Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\User;
use App\Models\AdvertiserUserDetail;

use App\Http\Traits\CommonFunctions;
use App\Http\Traits\CommonProfileFunctions;

class AdvertiserController extends Controller {

    use CommonFunctions, CommonProfileFunctions;

	public function profile(Request $request){

		if(Auth::check()){

			$logged_in_user_type = $request->session()->get('user_type');

			$adud = AdvertiserUserDetail::where('user_id', Auth::id())->first();
			

			return view('advertiser.profile')
				->with('user', Auth::user())
				->with('adud', $adud)
				->with('uid',  Auth::id())				
    			->with('arrTimeZone',  $this->getTimeZone())
    			->with('arrCur',  $this->getCurrency())
    			->with('arrCountry',  $this->getCountry())				
				->with('title', ucfirst($logged_in_user_type).' Profile')
				->with('module_name', 'Profile')
				->with('form_action_url', url('advertiser/update_profile'))				
				->with('logged_in_user_type', $logged_in_user_type);

		}

		return redirect('login');
	
	
	}//profile



	function update_profile(Request $request){

		return $this->update_advertiser_profile($request);

	}//update_profile



}//End of class AdvertiserController

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

class AdvertiserController extends Controller
{
	use CommonFunctions, CommonProfileFunctions;

	public function profile(Request $request)
	{
		$logged_in_user_type = $request->user()->user_type;

		$aud = AdvertiserUserDetail::where('user_id', Auth::id())->first();

		return response()->json([
			'module_name' => 'Profile',
			'user' => Auth::user(),
			'aud' => $aud,
			'uid' => Auth::id(),
			'arrTimeZone' => $this->getTimeZone(),
			'pmtArray' => $this->getPaymentMethod(),
			'arrCur' => $this->getCurrency(),
			'arrCountry' => $this->getCountry(),
			'logged_in_user_type' => $logged_in_user_type
		]);
	}

	function update_profile(Request $request)
	{

		return $this->update_advertiser_profile($request);

	}//update_profile

}//End of class AdvertiserController

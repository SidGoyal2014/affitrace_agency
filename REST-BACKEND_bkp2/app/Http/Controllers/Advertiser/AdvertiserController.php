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
	function update_profile(Request $request)
	{

		return $this->update_advertiser_profile($request);

	}//update_profile

}//End of class AdvertiserController

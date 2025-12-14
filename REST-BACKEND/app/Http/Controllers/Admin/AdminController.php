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

class AdminController extends Controller
{

	use CommonFunctions, CommonProfileFunctions, SystemSettings;

	public function list_advertisers()
	{

		$data = DB::table('users')
			->select('*', 'users.id as uid')
			->leftJoin('advertiser_user_details', 'users.id', '=', 'advertiser_user_details.user_id')
			->where('user_type', 'advertiser')
			->get();


		return response()->json([
			'module_name' => "List Advertisers",
			'data' => $data
		]);

	}//list_advertisers


	public function list_affiliates()
	{

		$data = DB::table('users')
			->select('*', 'users.id as uid')
			->leftJoin('affiliate_user_details', 'users.id', '=', 'affiliate_user_details.user_id')
			->where('user_type', 'affiliate')
			->get();

		return response()->json([
			'module_name' => "List Affiliates",
			'data' => $data
		]);

	}//list_affiliates


	public function profile(Request $request)
	{

		//if(Auth::check()){ // handled by middleware

		$user = $request->user();
		$logged_in_user_type = $user->user_type;

		return response()->json([
			'module_name' => 'Profile',
			'user' => $user,
			'title' => ucfirst($logged_in_user_type) . ' Profile',
			'logged_in_user_type' => $logged_in_user_type
		]);

		//}

		//return response()->json(['error' => 'Unauthorized'], 401);


	}//profile


	function update_profile(Request $request)
	{

		try {

			$validator = Validator::make(request()->all(), [
				'name' => 'required|string|min:5',
				'email' => 'unique:users,email,' . Auth::id(),
				'password' => 'nullable|string|min:5'
			]);

			if ($validator->fails()) {
				return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()], 422);

			}

			$user = User::find(Auth::id());

			$user->name = $request->name;
			$user->email = $request->email;
			$user->phone = $request->phone;


			if (strlen($request->password) > 0) {
				$user->password = Hash::make($request->password);

			}

			$user->save();

			return response()->json(['status' => 1, 'msg' => 'Profile Updated']);


		} catch (\Exception $e) {
			return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
		}


	}//update_profile


	public function edit_affiliate($id)
	{

		$user = User::find($id);

		if (!$user) {
			return response()->json(['error' => 'User not found'], 404);
		}

		$user_type = $user->user_type;

		$aud = AffiliateUserDetail::where('user_id', $id)->first();

		if (!isset($aud)) {
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

		$responseData = [
			'module_name' => 'Profile',
			'title' => ucfirst($user_type) . ' Profile',
			'user' => $user->toArray(),
			'aud' => $aud->toArray(),
			'uid' => $id,
			'arrTimeZone' => $this->getTimeZone(),
			'pmtArray' => $this->getPaymentMethod(),
			'arrCur' => $this->getCurrency(),
			'arrCountry' => $this->getCountry(),
			'logged_in_user_type' => $user_type,
			'form_action_url' => 'admin/update_affiliate'
		];

		// Sanitize Response Data ensuring UTF-8 integrity
		$responseData = $this->sanitize_utf8_recursive($responseData);

		return response()->json($responseData);

	}//edit_affiliate


	public function save_affiliate(Request $request)
	{

		try {

			$validator = Validator::make(request()->all(), [
				'name' => 'required|string|min:5',
				'email' => 'required|email|unique:users,email',
				'password' => 'required|confirmed|string|min:5',
				'company' => 'required|string|min:5',
				'phone' => 'required|digits_between:10,15',
			]);

			if ($validator->fails()) {
				return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()], 422);

			}//if



			/* if ($this->get_total_affiliates() >= $this->get_settings("num_affiliates")) {
				return response()->json(['status' => 0, 'error' => "We are not accepting new affiliates as of now."], 403);

			}//if */


			$code = bin2hex(random_bytes(5));
			$user = new User();
			$user->code = $code;
			/* $user->agency_id = $request->header('X-Agency-ID'); 
			if (session('agency_id')) {
				$user->agency_id = session('agency_id');
			} */

			$user->name = $request->name;
			$user->username = str_replace(' ', '', strtolower($request->name));
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


			return response()->json(['status' => 1, 'msg' => 'Affiliate created successfully', 'user_id' => $user->id]);


		} catch (\Exception $e) {
			return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);

		}

	}//save_affiliate

	public function create_affiliate()
	{

		return response()->json([
			'module_name' => 'Create Affiliate',
			'title' => 'Create Affiliate'
		]);

	}//create_affiliate


	public function create_advertiser()
	{

		return response()->json([
			'module_name' => 'Create Advertiser',
			'title' => 'Create Advertiser',
			'form_action_url' => 'doRegister' // Or appropriate API endpoint
		]);

	}//create_advertiser


	public function save_advertiser(Request $request)
	{

		try {

			$validator = Validator::make(request()->all(), [
				'name' => 'required|string|min:5',
				'email' => 'required|email|unique:users,email',
				'password' => 'required|confirmed|string|min:5',
				'company' => 'required|string|min:5',
				'phone' => 'required|digits_between:10,15',
			]);

			if ($validator->fails()) {
				return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()], 422);

			}//if


			$code = bin2hex(random_bytes(5));
			$user = new User();
			$user->code = $code;
			/* if (session('agency_id')) {
				$user->agency_id = session('agency_id');
			} */
			$user->name = $request->name;
			$user->username = str_replace(' ', '', strtolower($request->name));
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

			return response()->json(['status' => 1, 'msg' => 'Advertiser created successfully', 'user_id' => $user->id]);


		} catch (\Exception $e) {
			return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);

		}

	}//save_advertiser


	public function edit_advertiser($id)
	{

		$user = User::find($id);

		if (!$user) {
			return response()->json(['error' => 'User not found'], 404);
		}

		$user_type = $user->user_type;

		$adud = AdvertiserUserDetail::where('user_id', $id)->first();

		if (!isset($adud)) {
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

		$responseData = [
			'module_name' => 'Profile',
			'title' => ucfirst($user_type) . ' Profile',
			'user' => $user->toArray(),
			'adud' => $adud->toArray(),
			'uid' => $id,
			'arrTimeZone' => $this->getTimeZone(),
			'arrCur' => $this->getCurrency(),
			'arrCountry' => $this->getCountry(),
			'logged_in_user_type' => $user_type,
			'form_action_url' => 'admin/update_advertiser'
		];

		// Sanitize Response Data ensuring UTF-8 integrity
		$responseData = $this->sanitize_utf8_recursive($responseData);

		return response()->json($responseData);

	}//edit_advertiser

	private function sanitize_utf8_recursive($data)
	{
		if (is_string($data)) {
			return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
		} elseif (is_array($data)) {
			$ret = [];
			foreach ($data as $i => $d)
				$ret[$i] = $this->sanitize_utf8_recursive($d);
			return $ret;
		} elseif (is_object($data)) {
			foreach ($data as $i => $d)
				$data->$i = $this->sanitize_utf8_recursive($d);
			return $data;
		}
		return $data;
	}


	function update_advertiser(Request $request)
	{

		return $this->update_advertiser_profile($request);

	}//update_advertiser



	function update_affiliate(Request $request)
	{

		return $this->update_affiliate_profile($request);

	}//update_affiliate


}//End of class AdminController

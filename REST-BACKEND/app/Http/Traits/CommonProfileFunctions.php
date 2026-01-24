<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

use DB, Auth, Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\User;
use App\Models\AffiliateUserDetail;
use App\Models\AdvertiserUserDetail;

trait CommonProfileFunctions
{


	function update_advertiser_profile($request)
	{

		try {

			$validator = Validator::make(request()->all(), [
				//'name' => 'required|string|min:5',
				//'phone' => 'required|string',
				//'company' => 'required|string',
				//'email' => 'unique:users,email,'.$request->uid,
				//'instant_messenger_name' => 'required|string',
				//'instant_messenger_value' => 'required|string'
			]);

			// Validation logic should ideally be here or in FormRequest. 
			// For now assuming validation happens or relying on basic checks.
			// But original code had validation. Let's keep it but return JSON on fail.

			$validator = Validator::make(request()->all(), [
				'name' => 'required|string|min:5',
				'phone' => 'required|string',
				'company' => 'required|string',
				'email' => 'unique:users,email,' . $request->uid,
				'instant_messenger_name' => 'required|string',
				'instant_messenger_value' => 'required|string'
			]);

			if ($validator->fails()) {
				//return redirect($prevURL)->withErrors($validator->messages())->withInput();
				return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()], 422);

			}

			$user = User::findOrFail($request->uid);

			$user->name = $request->name;
			$user->email = $request->email;
			$user->phone = $request->phone;

			if (strlen(trim($request->password)) > 0) {
				$user->password = Hash::make($request->password);

			}

			$user->company = $request->company;
			$user->instant_messenger_name = $request->instant_messenger_name;
			$user->instant_messenger_value = $request->instant_messenger_value;
			$user->address = $request->address;
			$user->city = $request->city;
			$user->state = $request->state;
			$user->country = $request->country;
			$user->zipcode = $request->zipcode;

			$user->save();



			$adud = AdvertiserUserDetail::where('user_id', $request->uid)->first();

			$adud->currency = $request->currency;
			$adud->postback_ip_whitelist = $request->postback_ip_whitelist;
			$adud->time_zone = $request->time_zone;
			$adud->affiliate_block = $request->affiliate_block;
			$adud->affiliate_whitelist = $request->affiliate_whitelist;
			$adud->private_note = $request->private_note;
			$adud->additional_info = $request->additional_info;
			$adud->tax_details = $request->tax_details;

			$adud->save();

			//return redirect($prevURL)->with('success', 'Profile Updated');
			return response()->json(['status' => 1, 'msg' => 'Profile Updated']);


		} catch (\Exception $e) {
			//return redirect($prevURL)->withErrors($e->getMessage());
			return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
		}


	}//update_advertiser_profile




	function update_affiliate_profile(Request $request)
	{

		try {

			$validator = Validator::make(request()->all(), [
				'name' => 'required|string|min:5',
				'phone' => 'required|string',
				'company' => 'required|string',
				'email' => 'unique:users,email,' . $request->uid,
				'job_title' => 'required|string',
				'instant_messenger_name' => 'required|string',
				'instant_messenger_value' => 'required|string'

			]);

			if ($validator->fails()) {

				//return redirect($prevURL)->withErrors($validator->messages())->withInput();
				return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()], 422);


			}

			$user = User::findOrFail($request->uid);


			$user->name = $request->name;
			$user->email = $request->email;
			$user->phone = $request->phone;

			if (strlen(trim($request->password)) > 0) {
				$user->password = Hash::make($request->password);

			}

			$user->company = $request->company;
			$user->job_title = $request->job_title;
			$user->instant_messenger_name = $request->instant_messenger_name;
			$user->instant_messenger_value = $request->instant_messenger_value;
			$user->address = $request->address;
			$user->city = $request->city;
			$user->state = $request->state;
			$user->country = $request->country;
			$user->zipcode = $request->zipcode;

			// Allow Admin to update status
			if ($request->user()->user_type === \App\Enums\UserType::Admin->value && $request->has('status')) {
				$user->status = \App\Enums\UserStatus::tryFrom($request->status) ?? $user->status;
			}

			$user->save();

			//$aud = AffiliateUserDetail::where('user_id', Auth::id())->first();

			$aud = AffiliateUserDetail::where('user_id', $request->uid)->first();


			$aud->currency = $request->currency;
			$aud->additional_tracking_url_tokens = $request->additional_tracking_url_tokens;
			$aud->time_zone = $request->time_zone;
			$aud->payment_method = $request->payment_method;
			$aud->payment_details = $request->payment_details;
			$aud->billing_terms = $request->billing_terms;
			$aud->add_tax_basic_name = $request->add_tax_basic_name;
			$aud->add_tax_type = $request->add_tax_type;
			$aud->add_tax_number = $request->add_tax_number;
			$aud->add_tax = $request->add_tax;

			$aud->save();

			//return redirect($prevURL)->with('success', 'Profile Updated');
			return response()->json(['status' => 1, 'msg' => 'Profile Updated']);


		} catch (\Exception $e) {

			//return redirect($prevURL)->withErrors($e->getMessage());
			return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);

		}


	}//update_affiliate_profile



}//CommonProfileFunctions

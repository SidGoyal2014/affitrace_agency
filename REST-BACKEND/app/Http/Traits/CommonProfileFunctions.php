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

            if ($request->has('role_id')) {
                $user->role_id = $request->filled('role_id') ? $request->role_id : null;
            }

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





            $uid = $request->user()->id;

            $validator = Validator::make(request()->all(), [
                'name' => 'required|string|min:5',
                'phone' => 'nullable|string',
                'company' => 'nullable|string',
                'email' => 'unique:users,email,' . $uid,
                'job_title' => 'nullable|string',
                'instant_messenger_name' => 'nullable|string',
                'instant_messenger_value' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                \Illuminate\Support\Facades\Log::error('Affiliate Profile Update Validation Failed for User ' . $uid, [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $request->except(['password'])
                ]);
                return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()], 422);
            }

            $user = User::findOrFail($uid);

            if ($request->has('name'))
                $user->name = $request->name;
            if ($request->has('email'))
                $user->email = $request->email;
            if ($request->has('phone'))
                $user->phone = $request->phone;

            if ($request->has('password') && strlen(trim($request->password)) > 0) {
                $user->password = Hash::make($request->password);
            }

            if ($request->has('company'))
                $user->company = $request->company;
            if ($request->has('job_title'))
                $user->job_title = $request->job_title;
            if ($request->has('instant_messenger_name'))
                $user->instant_messenger_name = $request->instant_messenger_name;
            if ($request->has('instant_messenger_value'))
                $user->instant_messenger_value = $request->instant_messenger_value;
            if ($request->has('address'))
                $user->address = $request->address;
            if ($request->has('city'))
                $user->city = $request->city;
            if ($request->has('state'))
                $user->state = $request->state;
            if ($request->has('country'))
                $user->country = $request->country;
            if ($request->has('zipcode'))
                $user->zipcode = $request->zipcode;

            // Allow Admin to update status
            if ($request->user()->user_type === \App\Enums\UserType::Admin->value && $request->has('status')) {
                $user->status = \App\Enums\UserStatus::tryFrom($request->status) ?? $user->status;
            }

            if ($request->has('role_id')) {
                $user->role_id = $request->filled('role_id') ? $request->role_id : null;
            }

            $user->save();

            $aud = AffiliateUserDetail::firstOrNew(['user_id' => $uid]);

            if ($request->has('currency'))
                $aud->currency = $request->currency;
            if ($request->has('additional_tracking_url_tokens'))
                $aud->additional_tracking_url_tokens = $request->additional_tracking_url_tokens;
            if ($request->has('time_zone'))
                $aud->time_zone = $request->time_zone;
            if ($request->has('payment_method'))
                $aud->payment_method = $request->payment_method;
            if ($request->has('payment_details'))
                $aud->payment_details = $request->payment_details;
            if ($request->has('billing_terms'))
                $aud->billing_terms = $request->billing_terms;
            if ($request->has('add_tax_basic_name'))
                $aud->add_tax_basic_name = $request->add_tax_basic_name;
            if ($request->has('add_tax_type'))
                $aud->add_tax_type = $request->add_tax_type;
            if ($request->has('add_tax_number'))
                $aud->add_tax_number = $request->add_tax_number;
            if ($request->has('add_tax'))
                $aud->add_tax = $request->add_tax;

            $aud->save();

            return response()->json(['status' => 1, 'msg' => 'Profile Updated']);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Affiliate Profile Update Exception for User ' . ($uid ?? 'unknown'), [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }


        function update_employee_profile(Request $request)
        {
            try {
                $validator = Validator::make(request()->all(), [
                    'name' => 'required|string|min:5',
                    'phone' => 'nullable|string',
                    'email' => 'unique:users,email,' . $request->uid,
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()], 422);
                }

                $user = User::findOrFail($request->uid);

                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;

                if ($request->has('role_id')) {
                    $user->role_id = $request->filled('role_id') ? $request->role_id : null;
                }
                if ($request->has('designation_id')) {
                    $user->designation_id = $request->filled('designation_id') ? $request->designation_id : null;
                }

                if (strlen(trim($request->password)) > 0) {
                    $user->password = Hash::make($request->password);
                }

                if ($request->user()->user_type === \App\Enums\UserType::Admin->value && $request->has('status')) {
                    $user->status = \App\Enums\UserStatus::tryFrom($request->status) ?? $user->status;
                }

                $user->save();
                return response()->json(['status' => 1, 'msg' => 'Employee Profile Updated']);
            } catch (\Exception $e) {
                return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
            }
        }


    }

} // CommonProfileFunctions

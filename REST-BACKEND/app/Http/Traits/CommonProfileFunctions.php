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


    function update_advertiser_profile(Request $request)
    {
        try {
        
            $uid = $request->filled('uid') ? (int) $request->uid : $request->user()->id;

            $validator = Validator::make(request()->all(), [
                'name' => 'required|string|min:5',
                'phone' => 'nullable|string',
                'company' => 'nullable|string',
                'email' => 'unique:users,email,' . $uid,
                'instant_messenger_name' => 'nullable|string',
                'instant_messenger_value' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                \Illuminate\Support\Facades\Log::error('Advertiser Profile Update Validation Failed for User ' . $uid, [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $request->except(['password'])
                ]);
                return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()], 422);
            }

            $user = User::findOrFail($uid);

            if ($request->has('name')) $user->name = $request->name;
            if ($request->has('email')) $user->email = $request->email;
            if ($request->has('phone')) $user->phone = $request->phone;

            if ($request->has('password') && strlen(trim($request->password)) > 0) {
                $user->password = Hash::make($request->password);
            }

            if ($request->has('company')) $user->company = $request->company;
            if ($request->has('instant_messenger_name')) $user->instant_messenger_name = $request->instant_messenger_name;
            if ($request->has('instant_messenger_value')) $user->instant_messenger_value = $request->instant_messenger_value;
            if ($request->has('address')) $user->address = $request->address;
            if ($request->has('city')) $user->city = $request->city;
            if ($request->has('state')) $user->state = $request->state;
            if ($request->has('country')) $user->country = $request->country;
            if ($request->has('zipcode')) $user->zipcode = $request->zipcode;


            if ($request->has('timezone')) $user->timezone = $request->timezone;
            elseif ($request->has('time_zone')) $user->timezone = $request->time_zone;

            $user->save();

            $adud = AdvertiserUserDetail::firstOrNew(['user_id' => $uid]);

            if ($request->has('currency')) $adud->currency = $request->currency;
            if ($request->has('postback_ip_whitelist')) $adud->postback_ip_whitelist = $request->postback_ip_whitelist;
            if ($request->has('time_zone')) $adud->time_zone = $request->time_zone;
            if ($request->has('affiliate_block')) $adud->affiliate_block = $request->affiliate_block;
            if ($request->has('affiliate_whitelist')) $adud->affiliate_whitelist = $request->affiliate_whitelist;
            if ($request->has('private_note')) $adud->private_note = $request->private_note;
            if ($request->has('additional_info')) $adud->additional_info = $request->additional_info;
            if ($request->has('tax_details')) $adud->tax_details = $request->tax_details;

            $adud->save();

            return response()->json(['status' => 1, 'msg' => 'Profile Updated']);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Advertiser Profile Update Exception for User ' . ($uid ?? 'unknown'), [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }

    }//update_advertiser_profile




    function update_affiliate_profile(Request $request)
    {
        try {

            $uid = $request->filled('uid') ? (int) $request->uid : $request->user()->id;

            $validator = Validator::make(request()->all(), [
                'name' => 'required|string|min:5',
                'phone' => 'nullable|string',
                'company' => 'nullable|string',
                'email' => 'unique:users,email,'.$uid,
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

            // Allow Admin & Employee to update status & manager
            $currentUserType = strtolower($request->user()->user_type);
            if ($currentUserType === 'admin' || $currentUserType === 'employee') {
                if ($request->has('status')) {
                    $user->status = \App\Enums\UserStatus::tryFrom($request->status) ?? $user->status;
                }
                if ($request->has('employee_id')) {
                    $user->employee_id = $request->filled('employee_id') ? (string)$request->employee_id : null;
                }
            }

            if ($request->has('timezone')) $user->timezone = $request->timezone;
            elseif ($request->has('time_zone')) $user->timezone = $request->time_zone;

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

            // Save new fields
            if ($request->has('website'))
                $aud->website = $request->website;
            if ($request->has('traffic_source'))
                $aud->traffic_source = $request->traffic_source;
            if ($request->has('global_clicks_cap'))
                $aud->global_clicks_cap = $request->filled('global_clicks_cap') ? (int)$request->global_clicks_cap : null;
            if ($request->has('global_conversions_cap'))
                $aud->global_conversions_cap = $request->filled('global_conversions_cap') ? (int)$request->global_conversions_cap : null;
            if ($request->has('tags'))
                $aud->tags = $request->tags;
            if ($request->has('referred_by'))
                $aud->referred_by = $request->filled('referred_by') ? (int)$request->referred_by : null;
            if ($request->has('private_note'))
                $aud->private_note = $request->private_note;

            $aud->save();

            // Save postback configuration if provided
            if ($request->has('pb_url')) {
                \Illuminate\Support\Facades\DB::table('affiliate_postback_columns')->updateOrInsert(
                    ['uid' => $uid],
                    [
                        'url' => $request->pb_url,
                        'tid' => $request->pb_tid,
                        'aff_id' => $request->pb_aff_id,
                        'event_id' => $request->pb_event_id,
                        'sub_aff_id' => $request->pb_sub_aff_id,
                        'aff_sub1' => $request->pb_aff_sub1,
                        'aff_sub2' => $request->pb_aff_sub2,
                        'aff_sub3' => $request->pb_aff_sub3,
                        'aff_sub4' => $request->pb_aff_sub4,
                        'aff_sub5' => $request->pb_aff_sub5,
                        'updated_at' => now()
                    ]
                );
            }

            return response()->json(['status' => 1, 'msg' => 'Profile Updated']);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Affiliate Profile Update Exception for User ' . ($uid ?? 'unknown'), [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    } // End update_affiliate_profile

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

                if ($request->has('timezone')) $user->timezone = $request->timezone;
                elseif ($request->has('time_zone')) $user->timezone = $request->time_zone;

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

} // CommonProfileFunctions

<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\AffiliateUserDetail;
use App\Models\AdvertiserUserDetail;
use App\Http\Traits\CommonFunctions;
use App\Http\Traits\CommonProfileFunctions;
use App\Http\Traits\SystemSettings;

class AdminService
{
    use CommonFunctions, CommonProfileFunctions, SystemSettings;

    public function listAdvertisers()
    {
        $data = DB::table('users')
            ->select('*', 'users.id as uid')
            ->leftJoin('advertiser_user_details', 'users.id', '=', 'advertiser_user_details.user_id')
            ->where('user_type', \App\Enums\UserType::Advertiser->value)
            ->get();

        return [
            'module_name' => "List Advertisers",
            'data' => $data
        ];
    }

    public function listAffiliates()
    {
        $data = DB::table('users')
            ->select('*', 'users.id as uid')
            ->leftJoin('affiliate_user_details', 'users.id', '=', 'affiliate_user_details.user_id')
            ->where('user_type', \App\Enums\UserType::Affiliate->value)
            ->get();

        return [
            'module_name' => "List Affiliates",
            'data' => $data
        ];
    }

    public function listEmployees()
    {
        $data = DB::table('users')
            ->select('users.*', 'roles.name as role_name', 'designations.name as designation_name')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('designations', 'users.designation_id', '=', 'designations.id')
            ->where('users.user_type', \App\Enums\UserType::Employee->value)
            ->get();

        return [
            'module_name' => "List Employees",
            'data' => $data
        ];
    }

    public function searchUsers(string $q, int $limit = 20)
    {
        $limit = min($limit, 50);
        $like  = '%' . $q . '%';

        $data = DB::table('users')
            ->select('id', 'name', 'email', 'user_type')
            ->where('user_type', '!=', \App\Enums\UserType::Admin->value)
            ->where(function ($query) use ($like) {
                $query->where('name', 'LIKE', $like)
                    ->orWhere('email', 'LIKE', $like);
            })
            ->limit($limit)
            ->get();

        return ['data' => $data];
    }

    public function getProfileData($user)
    {
        $logged_in_user_type = $user->user_type;

        return [
            'module_name' => 'Profile',
            'user' => $user,
            'title' => ucfirst($logged_in_user_type) . ' Profile',
            'logged_in_user_type' => $logged_in_user_type
        ];
    }

    public function updateProfile($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:5',
            'email' => 'unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:5'
        ]);

        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->toArray(), 'code' => 422];
        }

        $user = User::find(Auth::id());
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        
        if ($request->has('timezone')) {
            $user->timezone = $request->timezone;
        }

        if (strlen($request->password) > 0) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return ['status' => 1, 'msg' => 'Profile Updated', 'code' => 200];
    }

    public function editAffiliate($id)
    {
        $user = User::find($id);

        if (!$user) {
            return ['error' => 'User not found', 'code' => 404];
        }

        $user_type = $user->user_type;
        $aud = AffiliateUserDetail::where('user_id', $id)->first();

        if (!isset($aud)) {
            $aud = new AffiliateUserDetail();
            // ... (populate default values as in controller)
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

        return $this->sanitize_utf8_recursive($responseData);
    }

    public function saveAffiliate($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:5',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|string|min:5',
            'company' => 'required|string|min:5',
            'phone' => 'required|digits_between:10,15',
        ]);

        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->toArray(), 'code' => 422];
        }

        $code = bin2hex(random_bytes(5));
        $user = new User();
        $user->code = $code;
        $user->name = $request->name;
        $user->username = str_replace(' ', '', strtolower($request->name));
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->user_type = $request->user_type;
        $user->company = $request->company;
        $user->country = $request->country;
        $user->phone = $request->phone;
        $user->status = \App\Enums\UserStatus::Active;
        $user->role_id = $request->filled('role_id') ? $request->role_id : null;
        $user->save();

        $aud = new AffiliateUserDetail();
        $aud->user_id = $user->id;
        $aud->save();

        return ['status' => 1, 'msg' => 'Affiliate created successfully', 'user_id' => $user->id, 'code' => 200];
    }

    public function saveAdvertiser($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:5',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|string|min:5',
            'company' => 'required|string|min:5',
            'phone' => 'required|digits_between:10,15',
        ]);

        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->toArray(), 'code' => 422];
        }

        $code = bin2hex(random_bytes(5));
        $user = new User();
        $user->code = $code;

        $user->name = $request->name;
        $user->username = str_replace(' ', '', strtolower($request->name));
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->user_type = 'advertiser';

        $user->company = $request->company;
        $user->country = $request->country;
        $user->phone = $request->phone;
        //$user->role_id = $request->filled('role_id') ? $request->role_id : null;

        $user->save();

        $adud = new AdvertiserUserDetail();
        $adud->user_id = $user->id;
        $adud->save();

        return ['status' => 1, 'msg' => 'Advertiser created successfully', 'user_id' => $user->id, 'code' => 200];
    }

    public function editAdvertiser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return ['error' => 'User not found', 'code' => 404];
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

        return $this->sanitize_utf8_recursive($responseData);
    }

    public function editEmployee($id)
    {
        $user = User::find($id);

        if (!$user) {
            return ['error' => 'User not found', 'code' => 404];
        }

        $responseData = [
            'module_name' => 'Profile',
            'title' => 'Employee Profile',
            'user' => $user->toArray(),
            'uid' => $id,
            'logged_in_user_type' => 'employee',
            'form_action_url' => 'admin/update_employee'
        ];

        return $this->sanitize_utf8_recursive($responseData);
    }

    public function saveEmployee($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:5',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|string|min:5',
            'phone' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->toArray(), 'code' => 422];
        }

        $code = bin2hex(random_bytes(5));
        $user = new User();
        $user->code = $code;
        $user->name = $request->name;
        $user->username = str_replace(' ', '', strtolower($request->name));
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->user_type = \App\Enums\UserType::Employee->value;
        $user->phone = $request->phone;
        $user->role_id = $request->filled('role_id') ? $request->role_id : null;
        $user->designation_id = $request->filled('designation_id') ? $request->designation_id : null;
        $user->status = \App\Enums\UserStatus::Active;
        $user->save();

        return ['status' => 1, 'msg' => 'Employee created successfully', 'user_id' => $user->id, 'code' => 200];
    }

    public function updateAdvertiserProfile($request)
    {
        return $this->update_advertiser_profile($request);
    }

    public function updateEmployeeProfile($request)
    {
        return $this->update_employee_profile($request);
    }

    public function updateAffiliateProfile($request)
    {
        return $this->update_affiliate_profile($request);
    }

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
}

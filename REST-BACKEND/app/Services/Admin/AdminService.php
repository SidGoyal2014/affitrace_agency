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
            ->select(
                'users.*',
                'users.id as uid',
                'advertiser_user_details.*',
                'managers.name as manager_name'
            )
            ->leftJoin('advertiser_user_details', 'users.id', '=', 'advertiser_user_details.user_id')
            ->leftJoin('users as managers', DB::raw("CAST(NULLIF(users.employee_id, '') AS BIGINT)"), '=', 'managers.id')
            ->where('users.user_type', \App\Enums\UserType::Advertiser->value)
            ->get();

        return [
            'module_name' => "List Advertisers",
            'data' => $data
        ];
    }

    public function listAffiliates($request = null)
    {
        $query = DB::table('users')
            ->select(
                'users.name',
                'users.email',
                'users.phone',
                'users.company',
                'users.country',
                'users.status',
                'users.created_at',
                'users.id as uid',
                'users.employee_id',
                'affiliate_user_details.website',
                'affiliate_user_details.traffic_source',
                'affiliate_user_details.global_clicks_cap',
                'affiliate_user_details.global_conversions_cap',
                'affiliate_user_details.tags',
                'affiliate_user_details.referred_by',
                'affiliate_user_details.status_reason',
                'affiliate_user_details.private_note',
                'managers.name as manager_name',
                'managers.id as manager_id'
            )
            ->leftJoin('affiliate_user_details', 'users.id', '=', 'affiliate_user_details.user_id')
            ->leftJoin('users as managers', DB::raw("CAST(NULLIF(users.employee_id, '') AS BIGINT)"), '=', 'managers.id')
            ->where('users.user_type', \App\Enums\UserType::Affiliate->value);

        if ($request) {
            if ($request->filled('status')) {
                $query->where('users.status', $request->status);
            }
            if ($request->filled('manager_id')) {
                $query->where('users.employee_id', (string) $request->manager_id);
            }
            if ($request->filled('country')) {
                $query->where('users.country', $request->country);
            }
            if ($request->filled('traffic_source')) {
                $query->where('affiliate_user_details.traffic_source', 'LIKE', '%' . $request->traffic_source . '%');
            }
            if ($request->filled('signup_start') && $request->filled('signup_end')) {
                $query->whereBetween('users.created_at', [
                    \Carbon\Carbon::parse($request->signup_start)->startOfDay(),
                    \Carbon\Carbon::parse($request->signup_end)->endOfDay()
                ]);
            }
        }

        $data = $query->orderBy('users.id', 'desc')->get();

        return [
            'module_name' => "List Affiliates",
            'data' => $data
        ];
    }

    public function listEmployees()
    {
        $data = DB::table('users')
            ->select('users.id', 'users.name', 'users.email', 'users.phone', 'users.status', 'users.created_at', 'users.role_id', 'users.designation_id', 'roles.name as role_name', 'designations.name as designation_name')
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
        $like = '%' . $q . '%';

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

        $payoutOverrides = DB::table('payout_rules')
            ->where('event_id', 0)
            ->where(function ($q) use ($id) {
                $q->where('affiliate_id', (string) $id)
                    ->orWhere('affiliate_id', 'LIKE', $id . ',%')
                    ->orWhere('affiliate_id', 'LIKE', '%,' . $id . ',%')
                    ->orWhere('affiliate_id', 'LIKE', '%,' . $id);
            })
            ->get();

        $postbackCols = DB::table('affiliate_postback_columns')
            ->where('uid', $id)
            ->first();

        $responseData = [
            'module_name' => 'Profile',
            'title' => ucfirst($user_type) . ' Profile',
            'user' => $user->toArray(),
            'aud' => $aud->toArray(),
            'payout_overrides' => $payoutOverrides->toArray(),
            'postback_cols' => $postbackCols ? (array) $postbackCols : null,
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
            $adud->tags = '';
            $adud->status_reason = '';
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

    public function updateAffiliateStatus($id, $status, $reason = null)
    {
        $user = User::findOrFail($id);
        $user->status = \App\Enums\UserStatus::tryFrom($status) ?? $user->status;
        $user->save();

        if ($reason !== null) {
            $aud = AffiliateUserDetail::where('user_id', $id)->first();
            if (!$aud) {
                $aud = new AffiliateUserDetail();
                $aud->user_id = $id;
            }
            $aud->status_reason = $reason;
            $aud->save();
        }

        return ['success' => true, 'msg' => 'Affiliate status updated successfully'];
    }

    public function bulkAffiliatesStatus($ids, $status, $reason = null)
    {
        foreach ($ids as $id) {
            $this->updateAffiliateStatus($id, $status, $reason);
        }
        return ['success' => true, 'msg' => 'Affiliates status updated successfully'];
    }

    public function deleteAffiliate($id)
    {
        User::where('id', $id)->delete();
        AffiliateUserDetail::where('user_id', $id)->delete();
        return ['success' => true, 'msg' => 'Affiliate account deleted successfully'];
    }

    public function assignAffiliateManager($id, $employee_id = null)
    {
        $user = User::findOrFail($id);
        $user->employee_id = $employee_id ? (string) $employee_id : null;
        $user->save();
        return ['success' => true, 'msg' => 'Affiliate manager assigned successfully'];
    }

    public function getAffiliateStats($id)
    {
        $totalClicks = DB::table('offer_trackings')->where('aff_id', $id)->count();

        $conversionsQuery = DB::table('conversions')->where('affiliate_id', $id);

        $stats = [
            'total_clicks' => $totalClicks,
            'approved_conversions' => (clone $conversionsQuery)->where('status', 'confirmed')->count(),
            'pending_conversions' => (clone $conversionsQuery)->where('status', 'pending')->count(),
            'rejected_conversions' => (clone $conversionsQuery)->where('status', 'declined')->count(),
            'total_revenue' => (clone $conversionsQuery)->where('status', 'confirmed')->sum('revenue'),
            'total_payout' => (clone $conversionsQuery)->where('status', 'confirmed')->sum('payout'),
        ];

        $stats['total_profit'] = $stats['total_revenue'] - $stats['total_payout'];
        $stats['cr'] = $totalClicks > 0 ? round(($stats['approved_conversions'] / $totalClicks) * 100, 2) : 0;
        $stats['epc'] = $totalClicks > 0 ? round($stats['total_payout'] / $totalClicks, 2) : 0;

        return ['success' => true, 'stats' => $stats];
    }

    public function savePayoutOverride($request)
    {
        \App\Models\PayoutRule::updateOrCreate(
            [
                'offer_id' => $request->offer_id,
                'event_id' => 0, // 0 indicates the default offer event
                'affiliate_id' => (string) $request->affiliate_id
            ],
            [
                'rule_name' => 'Override for Affiliate #' . $request->affiliate_id,
                'payout_pricing' => $request->payout,
                'revenue_pricing' => $request->revenue,
                'payout_model' => 'cpa',
                'revenue_model' => 'cpa',
                'currency' => 'USD',
                'payout_usd' => $this->getNewCurrencyValue($request->payout, 'USD'),
                'revenue_usd' => $this->getNewCurrencyValue($request->revenue, 'USD')
            ]
        );

        return ['success' => true, 'msg' => 'Payout override saved successfully'];
    }

    public function getAffiliateReferrals($id)
    {
        $referrals = DB::table('users')
            ->join('affiliate_user_details', 'users.id', '=', 'affiliate_user_details.user_id')
            ->select('users.id', 'users.name', 'users.email', 'users.company', 'users.created_at')
            ->where('affiliate_user_details.referred_by', $id)
            ->get();

        return ['success' => true, 'referrals' => $referrals];
    }

    public function exportAffiliates($request)
    {
        $res = $this->listAffiliates($request);
        $data = $res['data'];

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=affiliates_export_" . date('Ymd_His') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Name', 'Email', 'Company', 'Phone', 'Country', 'Status', 'Website', 'Traffic Source', 'Manager', 'Signup Date'];

        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $aff) {
                fputcsv($file, [
                    $aff->uid,
                    $aff->name,
                    $aff->email,
                    $aff->company ?? '-',
                    $aff->phone ?? '-',
                    $aff->country ?? '-',
                    $aff->status,
                    $aff->website ?? '-',
                    $aff->traffic_source ?? '-',
                    $aff->manager_name ?? '-',
                    $aff->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function uploadAffiliateDoc($request)
    {
        $uid = $request->uid;
        $file = $request->file('doc');

        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/affiliate_docs/' . $uid, $fileName);

        $aud = AffiliateUserDetail::where('user_id', $uid)->first();
        if (!$aud) {
            $aud = new AffiliateUserDetail();
            $aud->user_id = $uid;
        }
        $existingDocs = json_decode($aud->docs, true) ?: [];
        $existingDocs[] = $fileName;
        $aud->docs = json_encode($existingDocs);
        $aud->save();

        return ['success' => true, 'msg' => 'Document uploaded successfully', 'file_name' => $fileName];
    }

    public function updateAdvertiserStatus($id, $status, $reason = null)
    {
        $user = User::findOrFail($id);
        $user->status = \App\Enums\UserStatus::tryFrom($status) ?? $user->status;
        $user->save();

        if ($reason !== null) {
            $adud = AdvertiserUserDetail::where('user_id', $id)->first();
            if (!$adud) {
                $adud = new AdvertiserUserDetail();
                $adud->user_id = $id;
            }
            $adud->status_reason = $reason;
            $adud->save();
        }

        return ['success' => true, 'msg' => 'Advertiser status updated successfully'];
    }

    public function updateAdvertiserNotesTags($id, $notes, $tags)
    {
        $adud = AdvertiserUserDetail::where('user_id', $id)->first();
        if (!$adud) {
            $adud = new AdvertiserUserDetail();
            $adud->user_id = $id;
        }
        $adud->private_note = $notes;
        $adud->tags = $tags;
        $adud->save();

        return ['success' => true, 'msg' => 'Advertiser notes and tags updated successfully'];
    }

    public function getAdvertiserOffers($id)
    {
        $offers = DB::table('offers')
            ->select('id', 'offer_name', 'ad_model', 'ad_pricing', 'aff_model', 'aff_pricing', 'offer_status', 'offer_visibility')
            ->where('advertiser_id', (string) $id)
            ->whereNull('deleted_at')
            ->get();

        return ['success' => true, 'offers' => $offers];
    }

    public function getAdvertiserStats($id)
    {
        $totalClicks = DB::table('offer_trackings')->where('adv_id', $id)->count();
        $conversionsQuery = DB::table('conversions')->where('advertiser_id', $id);

        $stats = [
            'total_clicks' => $totalClicks,
            'approved_conversions' => (clone $conversionsQuery)->where('status', 'confirmed')->count(),
            'pending_conversions' => (clone $conversionsQuery)->where('status', 'pending')->count(),
            'rejected_conversions' => (clone $conversionsQuery)->where('status', 'declined')->count(),
            'total_spend' => (clone $conversionsQuery)->where('status', 'confirmed')->sum('revenue'), // Spend for advertiser is revenue for agency
            'total_payout' => (clone $conversionsQuery)->where('status', 'confirmed')->sum('payout'),
        ];

        $stats['total_profit'] = $stats['total_spend'] - $stats['total_payout'];
        $stats['cr'] = $totalClicks > 0 ? round(($stats['approved_conversions'] / $totalClicks) * 100, 2) : 0;
        $stats['epc'] = $totalClicks > 0 ? round($stats['total_spend'] / $totalClicks, 2) : 0;

        return ['success' => true, 'stats' => $stats];
    }

    public function getAdvertiserInvoices($id)
    {
        $invoices = DB::table('invoices')
            ->select('id', 'invoice_number', 'title', 'status', 'invoice_date', 'due_date', 'total_amount')
            ->where('advertiser_id', $id)
            ->whereNull('deleted_at')
            ->get();

        return ['success' => true, 'invoices' => $invoices];
    }

    public function exportAdvertisers($request = null)
    {
        $res = $this->listAdvertisers();
        $data = $res['data'];

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=advertisers_export_" . date('Ymd_His') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Name', 'Email', 'Company', 'Phone', 'Country', 'Status', 'Private Note', 'Tags', 'Manager', 'Signup Date'];

        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $adv) {
                fputcsv($file, [
                    $adv->uid,
                    $adv->name,
                    $adv->email,
                    $adv->company ?? '-',
                    $adv->phone ?? '-',
                    $adv->country ?? '-',
                    $adv->status,
                    $adv->private_note ?? '-',
                    $adv->tags ?? '-',
                    $adv->manager_name ?? '-',
                    $adv->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

<?php

namespace App\Services\Affiliate;

use App\Models\User;
use App\Models\AffiliateUserDetail;
use App\Models\OfferToken;
use App\Models\AffiliatePostbackColumn;
use App\Http\Traits\CommonFunctions;
use App\Http\Traits\CommonProfileFunctions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AffiliateService
{
    use CommonFunctions, CommonProfileFunctions;

    /**
     * Get profile data for the authenticated affiliate.
     */
    public function getProfileData($user)
    {
        $aud = AffiliateUserDetail::where('user_id', $user->id)->first();

        return [
            'module_name' => 'Profile',
            'user' => $user,
            'aud' => $aud,
            'uid' => $user->id,
            'arrTimeZone' => $this->getTimeZone(),
            'pmtArray' => $this->getPaymentMethod(),
            'arrCur' => $this->getCurrency(),
            'arrCountry' => $this->getCountry(),
            'logged_in_user_type' => $user->user_type
        ];
    }

    /**
     * Update the affiliate profile.
     */
    public function updateProfile(Request $request)
    {
        return $this->update_affiliate_profile($request);
    }

    /**
     * Get postback columns data.
     */
    public function getPostbackColumns($uid, $userType)
    {
        $cols = array();
        $cols['sub1'] = '';
        $cols['oid'] = '';
        $cols['eid'] = '';
        $cols['uid'] = '';
        $cols['url'] = '';
        $cols['tid'] = '';
        $cols['aff_id'] = $uid;
        $cols['event_id'] = '';
        $cols['sub_aff_id'] = '';
        $cols['aff_sub1'] = '';
        $cols['aff_sub2'] = '';
        $cols['aff_sub3'] = '';
        $cols['aff_sub4'] = '';
        $cols['aff_sub5'] = '';
        $cols['source'] = '';
        $cols['android_id'] = '';
        $cols['device_id'] = '';
        $cols['google_aid'] = '';
        $cols['iosidfa'] = '';
        $cols['offer_id'] = '';
        $cols['offer_name'] = '';
        $cols['ip'] = '';
        $cols['country'] = '';
        $cols['os'] = '';
        $cols['os_ver'] = '';
        $cols['raw_useragent'] = '';
        $cols['adv_id'] = '';
        $cols['timestamp'] = '';
        $cols['useragent'] = '';
        $cols['deep_link'] = '';

        $tokens = OfferToken::where('token_micro', '!=', 'sub1')->get()->toArray();
        $pCols = AffiliatePostbackColumn::where('uid', $uid)
            ->whereNull('oid')
            ->first();

        if (!is_null($pCols)) {
            $aCols = $pCols->toArray();

            if (count($aCols) > 0) {
                $cols = $aCols;
            }
        }

        return [
            'module_name' => 'Postback Columns',
            'user' => Auth::user(),
            'tokens' => $tokens,
            'cols' => $cols,
            'uid' => $uid,
            'logged_in_user_type' => $userType
        ];
    }

    /**
     * Update or insert postback columns.
     */
    public function updatePostbackColumns($data, $uid)
    {
        // Always use Auth::id() for the condition
        $arrCondition = [
            'uid' => $uid,
            'oid' => null
        ];

        $arr = [];

        foreach ($data as $k => $v) {
            if ($k == '_token' || $k == 'uid' || $k == 'oid') {
                continue;
            }
            $arr[$k] = $v;
        }

        DB::table('affiliate_postback_columns')
            ->updateOrInsert($arrCondition, $arr);

        return ['message' => 'Postback columns updated successfully'];
    }
}

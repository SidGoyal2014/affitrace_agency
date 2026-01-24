<?php

namespace App\Services\Advertiser;

use App\Models\User;
use App\Models\AdvertiserUserDetail;
use App\Http\Traits\CommonFunctions;
use App\Http\Traits\CommonProfileFunctions;

class AdvertiserService
{
    use CommonFunctions, CommonProfileFunctions;

    public function getProfileData($user)
    {
        $logged_in_user_type = $user->user_type;
        $aud = AdvertiserUserDetail::where('user_id', $user->id)->first();

        return [
            'module_name' => 'Profile',
            'user' => $user,
            'aud' => $aud,
            'uid' => $user->id,
            'arrTimeZone' => $this->getTimeZone(),
            'pmtArray' => $this->getPaymentMethod(),
            'arrCur' => $this->getCurrency(),
            'arrCountry' => $this->getCountry(),
            'logged_in_user_type' => $logged_in_user_type
        ];
    }

    public function updateProfile($request)
    {
        return $this->update_advertiser_profile($request);
    }
}

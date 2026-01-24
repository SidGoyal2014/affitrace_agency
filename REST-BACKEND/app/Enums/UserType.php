<?php

namespace App\Enums;

enum UserType: string
{
    case Admin = 'admin';
    case Affiliate = 'affiliate';
    case Advertiser = 'advertiser';
}

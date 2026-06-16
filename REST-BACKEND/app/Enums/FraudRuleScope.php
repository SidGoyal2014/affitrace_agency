<?php

namespace App\Enums;

enum FraudRuleScope: string
{
    case Global       = 'global';
    case PerAdvertiser = 'per_advertiser';
    case PerAffiliate  = 'per_affiliate';
    case PerOffer      = 'per_offer';

    public function label(): string
    {
        return match($this) {
            self::Global        => 'Global',
            self::PerAdvertiser => 'Per Advertiser',
            self::PerAffiliate  => 'Per Affiliate',
            self::PerOffer      => 'Per Offer',
        };
    }
}
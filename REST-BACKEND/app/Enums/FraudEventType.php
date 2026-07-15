<?php

namespace App\Enums;

enum FraudEventType: string
{
    case ClickFlagged        = 'click_flagged';
    case ClickBlocked        = 'click_blocked';
    case ConversionRejected  = 'conversion_rejected';
    case IpBlacklisted       = 'ip_blacklisted';
    case DeviceBlacklisted   = 'device_blacklisted';
    case RuleTriggered       = 'rule_triggered';
}
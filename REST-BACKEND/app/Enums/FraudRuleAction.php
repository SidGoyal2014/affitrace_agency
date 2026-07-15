<?php

namespace App\Enums;

enum FraudRuleAction: string
{
    case Block      = 'block';
    case Flag       = 'flag';
    case AutoReject = 'auto_reject';

    public function label(): string
    {
        return match($this) {
            self::Block      => 'Block',
            self::Flag       => 'Flag',
            self::AutoReject => 'Auto-Reject Conversion',
        };
    }
}
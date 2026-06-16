<?php

namespace App\Enums;

enum FraudRuleType: string
{
    case CTIT        = 'ctit';
    case DuplicateIP = 'duplicate_ip';
    case VpnProxy    = 'vpn_proxy';
    case Bot         = 'bot';
    case GeoMismatch = 'geo_mismatch';
    case Datacenter  = 'datacenter';

    public function label(): string
    {
        return match($this) {
            self::CTIT        => 'CTIT (Click-to-Install Time)',
            self::DuplicateIP => 'Duplicate IP',
            self::VpnProxy    => 'VPN / Proxy',
            self::Bot         => 'Bot Traffic',
            self::GeoMismatch => 'Geo Mismatch',
            self::Datacenter  => 'Datacenter IP',
        };
    }

    public function scoreDeduction(): int
    {
        return match($this) {
            self::Bot         => 50,
            self::VpnProxy    => 40,
            self::Datacenter  => 30,
            self::CTIT        => 30,
            self::GeoMismatch => 20,
            self::DuplicateIP => 20,
        };
    }
}
<?php

namespace App\Services\Admin;

use App\Models\FraudRule;
use App\Models\FraudIpBlacklist;
use App\Models\FraudIpWhitelist;
use App\Models\FraudDeviceBlacklist;
use App\Models\FraudClickScore;
use App\Models\FraudEvent;
use App\Models\FraudThirdPartyConfig;
use App\Enums\FraudRuleType;
use App\Enums\FraudRuleAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Core fraud detection logic — called from the tracking pipeline.
 * analyzeClick()      → call after saveClick() in TrackingService::processClick()
 * analyzeConversion() → call before $conv->save() in TrackingService::processPostback()
 */
class FraudDetectionService
{
    /**
     * Analyse a click for fraud.
     *
     * @param  string  $tid
     * @param  array   $clickData  Keys: ip, offer_id, affiliate_id, country, device_id, ua
     * @return array   ['status' => 'clean|flagged|blocked', 'score' => int, 'flags' => array]
     */
    public function analyzeClick(string $tid, array $clickData): array
    {
        $ip          = $clickData['ip']           ?? null;
        $offerId     = $clickData['offer_id']     ?? null;
        $affiliateId = $clickData['affiliate_id'] ?? null;
        $country     = $clickData['country']      ?? null;
        $deviceId    = $clickData['device_id']    ?? null;

        $violations = [];
        $score      = 100;

        // ── 1. Whitelist bypass ──────────────────────────────────────────
        if ($ip && FraudIpWhitelist::where('ip_address', $ip)->exists()) {
            return $this->saveAndReturn($tid, $offerId, $affiliateId, $ip, 100, [], 'clean');
        }

        // ── 2. IP blacklist ──────────────────────────────────────────────
        if ($ip && FraudIpBlacklist::where('ip_address', $ip)->exists()) {
            $violations[] = 'ip_blacklisted';
            $score -= 60;
        }

        // ── 3. Device blacklist ──────────────────────────────────────────
        if ($deviceId && FraudDeviceBlacklist::where('device_id', $deviceId)->exists()) {
            $violations[] = 'device_blacklisted';
            $score -= 50;
        }

        // ── 4. Load applicable active rules ─────────────────────────────
        $rules = FraudRule::where('is_active', true)
            ->where(function ($q) use ($offerId, $affiliateId) {
                $q->where('scope', 'global')
                  ->orWhere(function ($q2) use ($offerId) {
                      $q2->where('scope', 'per_offer')->where('scope_id', $offerId);
                  })
                  ->orWhere(function ($q2) use ($affiliateId) {
                      $q2->where('scope', 'per_affiliate')->where('scope_id', $affiliateId);
                  });
            })
            ->get();

        // ── 5. Evaluate each rule ────────────────────────────────────────
        foreach ($rules as $rule) {
            $triggered = false;

            switch ($rule->rule_type->value) {
                case 'duplicate_ip':
                    $triggered = $this->checkDuplicateIp($ip, $offerId, $rule->conditions);
                    break;
                case 'vpn_proxy':
                    $triggered = $this->checkVpnProxy($ip);
                    break;
                case 'bot':
                    $triggered = $this->checkBot($clickData, $rule->conditions);
                    break;
                case 'geo_mismatch':
                    $triggered = $this->checkGeoMismatch($country, $offerId);
                    break;
                case 'datacenter':
                    $triggered = $this->checkDatacenter($ip);
                    break;
            }

            if ($triggered) {
                $violations[] = $rule->rule_type->value;
                $score -= $rule->rule_type->scoreDeduction();

                $eventType = ($rule->rule_action->value === 'block') ? 'click_blocked' : 'click_flagged';
                FraudEvent::create([
                    'event_type'   => $eventType,
                    'entity_type'  => 'click',
                    'entity_id'    => $tid,
                    'rule_id'      => $rule->id,
                    'offer_id'     => $offerId,
                    'affiliate_id' => $affiliateId,
                    'details'      => ['ip' => $ip, 'rule' => $rule->name, 'action' => $rule->rule_action->value],
                    'created_at'   => now(),
                ]);
            }
        }

        $score  = max(0, $score);
        $status = $this->scoreToStatus($score);

        return $this->saveAndReturn($tid, $offerId, $affiliateId, $ip, $score, array_unique($violations), $status);
    }

    /**
     * Analyse a conversion for CTIT fraud.
     *
     * @param  int   $conversionId  Pass 0 if not yet saved; update entity_id after save if needed.
     * @param  array $convData      Keys: tid, ip, offer_id, affiliate_id, click_time, conversion_time
     * @return array ['should_reject' => bool, 'reason' => string|null]
     */
    public function analyzeConversion(int $conversionId, array $convData): array
    {
        $offerId        = $convData['offer_id']        ?? null;
        $affiliateId    = $convData['affiliate_id']    ?? null;
        $clickTime      = $convData['click_time']      ?? null;
        $conversionTime = $convData['conversion_time'] ?? now()->toDateTimeString();

        $ctitRules = FraudRule::where('is_active', true)
            ->where('rule_type', 'ctit')
            ->where(function ($q) use ($offerId, $affiliateId) {
                $q->where('scope', 'global')
                  ->orWhere(fn($q2) => $q2->where('scope', 'per_offer')->where('scope_id', $offerId))
                  ->orWhere(fn($q2) => $q2->where('scope', 'per_affiliate')->where('scope_id', $affiliateId));
            })->get();

        foreach ($ctitRules as $rule) {
            if (!$clickTime) continue;

            $diffSecs = strtotime($conversionTime) - strtotime($clickTime);
            $conds    = $rule->conditions ?? [];
            $minSecs  = $conds['min_seconds'] ?? 0;
            $maxSecs  = $conds['max_seconds'] ?? PHP_INT_MAX;

            if ($diffSecs < $minSecs || $diffSecs > $maxSecs) {
                FraudEvent::create([
                    'event_type'   => 'conversion_rejected',
                    'entity_type'  => 'conversion',
                    'entity_id'    => (string) $conversionId,
                    'rule_id'      => $rule->id,
                    'offer_id'     => $offerId,
                    'affiliate_id' => $affiliateId,
                    'details'      => ['ctit_seconds' => $diffSecs, 'min' => $minSecs, 'max' => $maxSecs],
                    'created_at'   => now(),
                ]);

                if ($rule->rule_action->value === 'auto_reject') {
                    return ['should_reject' => true, 'reason' => "CTIT violation: {$diffSecs}s"];
                }
            }
        }

        return ['should_reject' => false, 'reason' => null];
    }

    // ─── Rule Check Helpers ──────────────────────────────────────────────

    private function checkDuplicateIp(?string $ip, ?int $offerId, ?array $conditions): bool
    {
        if (!$ip) return false;
        $maxCount   = $conditions['max_count']      ?? 5;
        $windowMins = $conditions['window_minutes'] ?? 60;

        $count = DB::table('offer_trackings')
            ->where('ip', $ip)
            ->where('offer_id', $offerId)
            ->where('created_at', '>=', now()->subMinutes($windowMins))
            ->count();

        return $count >= $maxCount;
    }

    private function checkVpnProxy(?string $ip): bool
    {
        if (!$ip) return false;
        $config = FraudThirdPartyConfig::where('provider', 'ipqs')->where('is_active', true)->first();
        if (!$config || !$config->api_key) return false;

        try {
            $response = Http::timeout(5)->get(
                "https://www.ipqualityscore.com/api/json/ip/{$config->api_key}/{$ip}"
            );
            $data = $response->json();
            return ($data['vpn'] ?? false) || ($data['proxy'] ?? false) || ($data['tor'] ?? false);
        } catch (\Exception $e) {
            Log::warning('FraudDetectionService: IPQS VPN check failed — ' . $e->getMessage());
            return false;
        }
    }

    private function checkBot(array $clickData, ?array $conditions): bool
    {
        $ua          = $clickData['ua'] ?? '';
        $botPatterns = $conditions['ua_patterns'] ?? ['bot', 'crawler', 'spider', 'scraper', 'headless', 'python-requests'];
        foreach ($botPatterns as $pattern) {
            if (stripos($ua, $pattern) !== false) return true;
        }
        return false;
    }

    private function checkGeoMismatch(?string $clickCountry, ?int $offerId): bool
    {
        if (!$offerId || !$clickCountry) return false;
        $targetCountries = DB::table('offers')->where('id', $offerId)->value('countries');
        if (!$targetCountries) return false;
        $allowed = is_array($targetCountries)
            ? $targetCountries
            : (json_decode($targetCountries, true) ?? []);
        if (empty($allowed)) return false;
        return !in_array(strtoupper($clickCountry), array_map('strtoupper', $allowed));
    }

    private function checkDatacenter(?string $ip): bool
    {
        if (!$ip) return false;
        $config = FraudThirdPartyConfig::where('provider', 'ipqs')->where('is_active', true)->first();
        if (!$config || !$config->api_key) return false;

        try {
            $response = Http::timeout(5)->get(
                "https://www.ipqualityscore.com/api/json/ip/{$config->api_key}/{$ip}"
            );
            $data = $response->json();
            return ($data['is_crawler'] ?? false)
                || (($data['connection_type'] ?? '') === 'Data Center');
        } catch (\Exception $e) {
            Log::warning('FraudDetectionService: IPQS datacenter check failed — ' . $e->getMessage());
            return false;
        }
    }

    // ─── Helpers ─────────────────────────────────────────────────────────

    private function scoreToStatus(int $score): string
    {
        if ($score >= 70) return 'clean';
        if ($score >= 40) return 'flagged';
        return 'blocked';
    }

    private function saveAndReturn(
        string $tid, ?int $offerId, ?int $affiliateId,
        ?string $ip, int $score, array $flags, string $status
    ): array {
        FraudClickScore::updateOrCreate(
            ['tid' => $tid],
            [
                'offer_id'     => $offerId,
                'affiliate_id' => $affiliateId,
                'ip_address'   => $ip,
                'score'        => $score,
                'flags'        => $flags,
                'status'       => $status,
            ]
        );

        return ['status' => $status, 'score' => $score, 'flags' => $flags];
    }
}

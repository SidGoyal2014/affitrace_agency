<?php

namespace App\Services\Admin;

use App\Models\FraudRule;
use App\Models\FraudIpBlacklist;
use App\Models\FraudIpWhitelist;
use App\Models\FraudDeviceBlacklist;
use App\Models\FraudThirdPartyConfig;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class FraudService
{
    // ─── Rules ────────────────────────────────────────────────────────────

    public function listRules(): array
    {
        try {
            $rules = FraudRule::with('creator:id,name')
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($r) {
                    return [
                        'id'              => $r->id,
                        'name'            => $r->name,
                        'description'     => $r->description,
                        'scope'           => $r->scope->value,
                        'scope_label'     => $r->scope->label(),
                        'scope_id'        => $r->scope_id,
                        'rule_type'       => $r->rule_type->value,
                        'type_label'      => $r->rule_type->label(),
                        'rule_action'     => $r->rule_action->value,
                        'action_label'    => $r->rule_action->label(),
                        'conditions'      => $r->conditions,
                        'is_active'       => $r->is_active,
                        'created_by_name' => $r->creator?->name ?? '-',
                        'created_at'      => $r->created_at?->format('Y-m-d H:i'),
                    ];
                });

            return ['status' => 1, 'data' => $rules, 'code' => 200];
        } catch (\Exception $e) {
            Log::error('FraudService::listRules - ' . $e->getMessage());
            return ['status' => 0, 'error' => 'Failed to load rules', 'code' => 500];
        }
    }

    public function getRule(int $id): array
    {
        $rule = FraudRule::with('creator:id,name')->find($id);
        if (!$rule) {
            return ['status' => 0, 'error' => 'Rule not found', 'code' => 404];
        }
        return [
            'status' => 1,
            'data'   => [
                'id'          => $rule->id,
                'name'        => $rule->name,
                'description' => $rule->description,
                'scope'       => $rule->scope->value,
                'scope_id'    => $rule->scope_id,
                'rule_type'   => $rule->rule_type->value,
                'rule_action' => $rule->rule_action->value,
                'conditions'  => $rule->conditions,
                'is_active'   => $rule->is_active,
            ],
            'code' => 200,
        ];
    }

    public function createRule(array $data, int $userId): array
    {
        $validator = Validator::make($data, [
            'name'        => 'required|string|max:255',
            'scope'       => 'required|in:global,per_advertiser,per_affiliate,per_offer',
            'scope_id'    => 'nullable|integer',
            'rule_type'   => 'required|in:ctit,duplicate_ip,vpn_proxy,bot,geo_mismatch,datacenter',
            'rule_action' => 'required|in:block,flag,auto_reject',
            'conditions'  => 'nullable|array',
            'is_active'   => 'boolean',
        ]);

        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->first(), 'code' => 422];
        }

        try {
            $rule = FraudRule::create([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'scope'       => $data['scope'],
                'scope_id'    => $data['scope_id'] ?? null,
                'rule_type'   => $data['rule_type'],
                'rule_action' => $data['rule_action'],
                'conditions'  => $data['conditions'] ?? null,
                'is_active'   => $data['is_active'] ?? true,
                'created_by'  => $userId,
            ]);

            return ['status' => 1, 'message' => 'Rule created successfully', 'data' => $rule, 'code' => 201];
        } catch (\Exception $e) {
            Log::error('FraudService::createRule - ' . $e->getMessage());
            return ['status' => 0, 'error' => 'Failed to create rule', 'code' => 500];
        }
    }

    public function updateRule(int $id, array $data): array
    {
        $rule = FraudRule::find($id);
        if (!$rule) {
            return ['status' => 0, 'error' => 'Rule not found', 'code' => 404];
        }

        $validator = Validator::make($data, [
            'name'        => 'sometimes|string|max:255',
            'scope'       => 'sometimes|in:global,per_advertiser,per_affiliate,per_offer',
            'scope_id'    => 'nullable|integer',
            'rule_type'   => 'sometimes|in:ctit,duplicate_ip,vpn_proxy,bot,geo_mismatch,datacenter',
            'rule_action' => 'sometimes|in:block,flag,auto_reject',
            'conditions'  => 'nullable|array',
            'is_active'   => 'boolean',
        ]);

        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->first(), 'code' => 422];
        }

        try {
            $rule->fill(array_intersect_key($data, array_flip([
                'name', 'description', 'scope', 'scope_id',
                'rule_type', 'rule_action', 'conditions', 'is_active',
            ])));
            $rule->save();

            return ['status' => 1, 'message' => 'Rule updated successfully', 'data' => $rule, 'code' => 200];
        } catch (\Exception $e) {
            Log::error('FraudService::updateRule - ' . $e->getMessage());
            return ['status' => 0, 'error' => 'Failed to update rule', 'code' => 500];
        }
    }

    public function deleteRule(int $id): array
    {
        $rule = FraudRule::find($id);
        if (!$rule) {
            return ['status' => 0, 'error' => 'Rule not found', 'code' => 404];
        }
        $rule->delete();
        return ['status' => 1, 'message' => 'Rule deleted', 'code' => 200];
    }

    public function toggleRule(int $id): array
    {
        $rule = FraudRule::find($id);
        if (!$rule) {
            return ['status' => 0, 'error' => 'Rule not found', 'code' => 404];
        }
        $rule->is_active = !$rule->is_active;
        $rule->save();
        return ['status' => 1, 'message' => 'Rule status toggled', 'is_active' => $rule->is_active, 'code' => 200];
    }

    // ─── IP Blacklist ───────────────────────────────────────────────────

    public function listIpBlacklist(): array
    {
        $list = FraudIpBlacklist::orderByDesc('created_at')->get();
        return ['status' => 1, 'data' => $list, 'code' => 200];
    }

    public function addIpBlacklist(array $data, int $userId): array
    {
        $validator = Validator::make($data, [
            'ip_address' => 'required|ip',
            'reason'     => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->first(), 'code' => 422];
        }

        // Whitelisted IPs cannot be blacklisted
        if (FraudIpWhitelist::where('ip_address', $data['ip_address'])->exists()) {
            return ['status' => 0, 'error' => 'IP is on the whitelist. Remove it from whitelist first.', 'code' => 422];
        }

        try {
            $entry = FraudIpBlacklist::firstOrCreate(
                ['ip_address' => $data['ip_address']],
                ['reason' => $data['reason'] ?? null, 'imported_from' => 'manual', 'added_by' => $userId]
            );
            return ['status' => 1, 'message' => 'IP added to blacklist', 'data' => $entry, 'code' => 201];
        } catch (\Exception $e) {
            Log::error('FraudService::addIpBlacklist - ' . $e->getMessage());
            return ['status' => 0, 'error' => 'Failed to add IP', 'code' => 500];
        }
    }

    public function importIpBlacklist(array $ips, int $userId): array
    {
        $added   = 0;
        $skipped = 0;
        foreach ($ips as $ip) {
            $ip = trim($ip);
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $skipped++;
                continue;
            }
            $exists = FraudIpBlacklist::where('ip_address', $ip)->exists();
            if (!$exists) {
                FraudIpBlacklist::create([
                    'ip_address'    => $ip,
                    'imported_from' => 'csv',
                    'added_by'      => $userId,
                ]);
                $added++;
            } else {
                $skipped++;
            }
        }
        return ['status' => 1, 'message' => "Imported {$added} IPs. Skipped {$skipped} (invalid or duplicate).", 'code' => 200];
    }

    public function removeIpBlacklist(int $id): array
    {
        $entry = FraudIpBlacklist::find($id);
        if (!$entry) {
            return ['status' => 0, 'error' => 'Entry not found', 'code' => 404];
        }
        $entry->delete();
        return ['status' => 1, 'message' => 'IP removed from blacklist', 'code' => 200];
    }

    // ─── IP Whitelist ───────────────────────────────────────────────────

    public function listIpWhitelist(): array
    {
        $list = FraudIpWhitelist::orderByDesc('created_at')->get();
        return ['status' => 1, 'data' => $list, 'code' => 200];
    }

    public function addIpWhitelist(array $data, int $userId): array
    {
        $validator = Validator::make($data, [
            'ip_address' => 'required|ip',
            'reason'     => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->first(), 'code' => 422];
        }

        try {
            $entry = FraudIpWhitelist::firstOrCreate(
                ['ip_address' => $data['ip_address']],
                ['reason' => $data['reason'] ?? null, 'added_by' => $userId]
            );
            return ['status' => 1, 'message' => 'IP added to whitelist', 'data' => $entry, 'code' => 201];
        } catch (\Exception $e) {
            return ['status' => 0, 'error' => 'Failed to add IP', 'code' => 500];
        }
    }

    public function removeIpWhitelist(int $id): array
    {
        $entry = FraudIpWhitelist::find($id);
        if (!$entry) {
            return ['status' => 0, 'error' => 'Entry not found', 'code' => 404];
        }
        $entry->delete();
        return ['status' => 1, 'message' => 'IP removed from whitelist', 'code' => 200];
    }

    // ─── Device Blacklist ───────────────────────────────────────────────

    public function listDeviceBlacklist(): array
    {
        $list = FraudDeviceBlacklist::orderByDesc('created_at')->get();
        return ['status' => 1, 'data' => $list, 'code' => 200];
    }

    public function addDeviceBlacklist(array $data, int $userId): array
    {
        $validator = Validator::make($data, [
            'device_id'   => 'required|string|max:255',
            'device_type' => 'nullable|in:android_id,ios_idfa,google_aid',
            'reason'      => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->first(), 'code' => 422];
        }

        try {
            $entry = FraudDeviceBlacklist::firstOrCreate(
                ['device_id' => $data['device_id']],
                ['device_type' => $data['device_type'] ?? null, 'reason' => $data['reason'] ?? null, 'added_by' => $userId]
            );
            return ['status' => 1, 'message' => 'Device ID blacklisted', 'data' => $entry, 'code' => 201];
        } catch (\Exception $e) {
            return ['status' => 0, 'error' => 'Failed to add device', 'code' => 500];
        }
    }

    public function removeDeviceBlacklist(int $id): array
    {
        $entry = FraudDeviceBlacklist::find($id);
        if (!$entry) {
            return ['status' => 0, 'error' => 'Entry not found', 'code' => 404];
        }
        $entry->delete();
        return ['status' => 1, 'message' => 'Device ID removed', 'code' => 200];
    }

    // ─── Third-Party Config ─────────────────────────────────────────────

    public function getThirdPartyConfig(): array
    {
        $providers = ['ipqs', 'anura', '24metrics'];
        $result    = [];
        foreach ($providers as $p) {
            $cfg       = FraudThirdPartyConfig::where('provider', $p)->first();
            $result[$p] = $cfg ? [
                'provider'   => $cfg->provider,
                'api_key'    => $cfg->api_key ? ('***' . substr($cfg->api_key, -4)) : null,
                'api_url'    => $cfg->api_url,
                'settings'   => $cfg->settings,
                'is_active'  => $cfg->is_active,
                'updated_at' => $cfg->updated_at?->format('Y-m-d H:i'),
            ] : [
                'provider'  => $p,
                'api_key'   => null,
                'api_url'   => null,
                'settings'  => null,
                'is_active' => false,
                'updated_at'=> null,
            ];
        }
        return ['status' => 1, 'data' => $result, 'code' => 200];
    }

    public function updateThirdPartyConfig(array $data): array
    {
        $validator = Validator::make($data, [
            'provider'  => 'required|in:ipqs,anura,24metrics',
            'api_key'   => 'nullable|string|max:500',
            'api_url'   => 'nullable|url|max:500',
            'settings'  => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        if ($validator->fails()) {
            return ['status' => 0, 'error' => $validator->errors()->first(), 'code' => 422];
        }

        try {
            $cfg = FraudThirdPartyConfig::firstOrNew(['provider' => $data['provider']]);
            // Only overwrite api_key if a real (non-masked) value was submitted
            if (!empty($data['api_key']) && !str_starts_with($data['api_key'], '***')) {
                $cfg->api_key = $data['api_key'];
            }
            if (array_key_exists('api_url', $data))  $cfg->api_url  = $data['api_url'];
            if (array_key_exists('settings', $data))  $cfg->settings = $data['settings'];
            if (array_key_exists('is_active', $data)) $cfg->is_active = (bool) $data['is_active'];
            $cfg->save();
            return ['status' => 1, 'message' => 'Config saved', 'code' => 200];
        } catch (\Exception $e) {
            Log::error('FraudService::updateThirdPartyConfig - ' . $e->getMessage());
            return ['status' => 0, 'error' => 'Failed to save config', 'code' => 500];
        }
    }
}

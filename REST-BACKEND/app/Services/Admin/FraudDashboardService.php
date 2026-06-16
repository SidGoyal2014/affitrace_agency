<?php

namespace App\Services\Admin;

use App\Models\FraudClickScore;
use App\Models\FraudEvent;
use App\Models\FraudIpBlacklist;
use App\Models\FraudDeviceBlacklist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FraudDashboardService
{
    public function getDashboardStats(): array
    {
        try {
            $totalFlagged  = FraudClickScore::where('status', 'flagged')->count();
            $totalBlocked  = FraudClickScore::where('status', 'blocked')->count();
            $totalClean    = FraudClickScore::where('status', 'clean')->count();

            $rejectedConversions = FraudEvent::where('event_type', 'conversion_rejected')
                ->whereDate('created_at', today())
                ->count();

            // Top triggered rule types in last 7 days
            $topPatterns = DB::table('fraud_events')
                ->join('fraud_rules', 'fraud_events.rule_id', '=', 'fraud_rules.id')
                ->selectRaw('fraud_rules.rule_type, COUNT(*) as count')
                ->where('fraud_events.created_at', '>=', now()->subDays(7))
                ->groupBy('fraud_rules.rule_type')
                ->orderByDesc('count')
                ->limit(6)
                ->get();

            // Top fraudulent affiliates (by fraud click count)
            $topFraudAffiliates = DB::table('fraud_click_scores')
                ->join('users', 'fraud_click_scores.affiliate_id', '=', 'users.id')
                ->selectRaw('fraud_click_scores.affiliate_id, users.name, COUNT(*) as fraud_count, AVG(fraud_click_scores.score) as avg_score')
                ->whereIn('fraud_click_scores.status', ['flagged', 'blocked'])
                ->groupBy('fraud_click_scores.affiliate_id', 'users.name')
                ->orderByDesc('fraud_count')
                ->limit(5)
                ->get();

            // 5 most recent events
            $recentEvents = FraudEvent::with('rule:id,name,rule_type')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(function ($e) {
                    return [
                        'id'           => $e->id,
                        'event_type'   => $e->event_type,
                        'entity_type'  => $e->entity_type,
                        'entity_id'    => $e->entity_id,
                        'offer_id'     => $e->offer_id,
                        'affiliate_id' => $e->affiliate_id,
                        'details'      => $e->details,
                        'rule'         => $e->rule ? ['name' => $e->rule->name] : null,
                        'created_at'   => $e->created_at?->format('Y-m-d H:i:s'),
                    ];
                });

            return [
                'status' => 1,
                'data'   => [
                    'total_flagged'               => $totalFlagged,
                    'total_blocked'               => $totalBlocked,
                    'total_clean'                 => $totalClean,
                    'rejected_conversions_today'  => $rejectedConversions,
                    'top_patterns'                => $topPatterns,
                    'top_fraud_affiliates'        => $topFraudAffiliates,
                    'recent_events'               => $recentEvents,
                ],
                'code' => 200,
            ];
        } catch (\Exception $e) {
            Log::error('FraudDashboardService::getDashboardStats - ' . $e->getMessage());
            return ['status' => 0, 'error' => 'Failed to load dashboard', 'code' => 500];
        }
    }

    public function getAlerts(int $limit = 50, int $page = 1): array
    {
        try {
            $alerts = FraudEvent::with('rule:id,name,rule_type')
                ->orderByDesc('created_at')
                ->paginate($limit, ['*'], 'page', $page);

            $items = $alerts->map(function ($e) {
                return [
                    'id'           => $e->id,
                    'event_type'   => $e->event_type,
                    'entity_type'  => $e->entity_type,
                    'entity_id'    => $e->entity_id,
                    'offer_id'     => $e->offer_id,
                    'affiliate_id' => $e->affiliate_id,
                    'details'      => $e->details,
                    'rule'         => $e->rule ? ['name' => $e->rule->name, 'rule_type' => $e->rule->rule_type] : null,
                    'created_at'   => $e->created_at?->format('Y-m-d H:i:s'),
                ];
            });

            return ['status' => 1, 'data' => $items, 'total' => $alerts->total(), 'code' => 200];
        } catch (\Exception $e) {
            Log::error('FraudDashboardService::getAlerts - ' . $e->getMessage());
            return ['status' => 0, 'error' => 'Failed to load alerts', 'code' => 500];
        }
    }

    public function getClickScores(array $filters = []): array
    {
        try {
            $query = DB::table('fraud_click_scores')
                ->leftJoin('offers', 'fraud_click_scores.offer_id', '=', 'offers.id')
                ->leftJoin('users', 'fraud_click_scores.affiliate_id', '=', 'users.id')
                ->select(
                    'fraud_click_scores.id',
                    'fraud_click_scores.tid',
                    'fraud_click_scores.ip_address',
                    'fraud_click_scores.score',
                    'fraud_click_scores.flags',
                    'fraud_click_scores.status',
                    'fraud_click_scores.created_at',
                    'offers.offer_name',
                    'users.name as affiliate_name'
                )
                ->orderByDesc('fraud_click_scores.created_at');

            if (!empty($filters['status'])) {
                $query->where('fraud_click_scores.status', $filters['status']);
            }
            if (!empty($filters['affiliate_id'])) {
                $query->where('fraud_click_scores.affiliate_id', $filters['affiliate_id']);
            }
            if (!empty($filters['offer_id'])) {
                $query->where('fraud_click_scores.offer_id', $filters['offer_id']);
            }
            if (!empty($filters['date_from'])) {
                $query->whereDate('fraud_click_scores.created_at', '>=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $query->whereDate('fraud_click_scores.created_at', '<=', $filters['date_to']);
            }

            $limit = (int) ($filters['limit'] ?? 200);
            $data  = $query->limit($limit)->get()->map(function ($row) {
                return [
                    'id'             => $row->id,
                    'tid'            => $row->tid,
                    'offer_name'     => $row->offer_name    ?? '-',
                    'affiliate_name' => $row->affiliate_name ?? '-',
                    'ip_address'     => $row->ip_address,
                    'score'          => (int) $row->score,
                    'flags'          => is_string($row->flags) ? json_decode($row->flags, true) : ($row->flags ?? []),
                    'status'         => $row->status,
                    'created_at'     => $row->created_at,
                ];
            });

            return ['status' => 1, 'data' => $data, 'code' => 200];
        } catch (\Exception $e) {
            Log::error('FraudDashboardService::getClickScores - ' . $e->getMessage());
            return ['status' => 0, 'error' => 'Failed to load click scores', 'code' => 500];
        }
    }

    /**
     * Bulk-block IPs or Device IDs sourced from the fraud report.
     */
    public function bulkBlock(string $type, array $values, int $userId): array
    {
        $added = 0;
        try {
            if ($type === 'ip') {
                foreach ($values as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        FraudIpBlacklist::firstOrCreate(
                            ['ip_address' => $ip],
                            ['reason' => 'Bulk block from fraud report', 'imported_from' => 'bulk', 'added_by' => $userId]
                        );
                        $added++;
                    }
                }
            } elseif ($type === 'device') {
                foreach ($values as $deviceId) {
                    $deviceId = trim($deviceId);
                    if ($deviceId) {
                        FraudDeviceBlacklist::firstOrCreate(
                            ['device_id' => $deviceId],
                            ['reason' => 'Bulk block from fraud report', 'added_by' => $userId]
                        );
                        $added++;
                    }
                }
            } else {
                return ['status' => 0, 'error' => 'Invalid type. Use ip or device.', 'code' => 422];
            }

            return ['status' => 1, 'message' => "Blocked {$added} {$type}(s).", 'code' => 200];
        } catch (\Exception $e) {
            Log::error('FraudDashboardService::bulkBlock - ' . $e->getMessage());
            return ['status' => 0, 'error' => 'Bulk block failed', 'code' => 500];
        }
    }
}

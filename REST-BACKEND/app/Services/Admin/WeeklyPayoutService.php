<?php

namespace App\Services\Admin;

use App\Models\Transaction;
use App\Models\AffiliateWeeklyPayout;
use App\Services\Reports\BaseReportService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class WeeklyPayoutService extends BaseReportService
{
    public function listMonthData(array $filters = []): array
    {
        $timezone = $this->sanitizeTimezone($this->getUserTimezone());
        $monthKey = $filters['month'] ?? Carbon::now($timezone)->format('Y-m');
        $affiliateId = isset($filters['affiliate_id']) ? (int) $filters['affiliate_id'] : null;
        $sync = !array_key_exists('sync', $filters) || filter_var($filters['sync'], FILTER_VALIDATE_BOOLEAN);

        if (!$this->weeklyPayoutTableExists()) {
            return [
                'success' => true,
                'module_name' => 'Weekly Payouts',
                'message' => 'Weekly payouts table is not available yet. Run migrations to enable data sync.',
                'month' => $monthKey,
                'synced' => false,
                'data' => [],
            ];
        }

        try {
            $data = $sync
                ? $this->syncMonth($monthKey, $affiliateId)
                : AffiliateWeeklyPayout::query()
                    ->where('month_key', $monthKey)
                    ->when($affiliateId, fn ($query) => $query->where('affiliate_id', $affiliateId))
                    ->orderBy('week_start')
                    ->orderBy('affiliate_id')
                    ->get();
        } catch (Throwable $e) {
            if (!$this->isMissingWeeklyPayoutTableException($e)) {
                throw $e;
            }

            return [
                'success' => true,
                'module_name' => 'Weekly Payouts',
                'message' => 'Weekly payouts table is not available yet. Run migrations to enable data sync.',
                'month' => $monthKey,
                'synced' => false,
                'data' => [],
            ];
        }

        return [
            'success' => true,
            'module_name' => 'Weekly Payouts',
            'message' => 'Weekly payouts loaded successfully.',
            'month' => $monthKey,
            'synced' => $sync,
            'data' => $data,
        ];
    }

    public function monthStats(array $filters = []): array
    {
        $timezone = $this->sanitizeTimezone($this->getUserTimezone());
        $monthKey = $filters['month'] ?? Carbon::now($timezone)->format('Y-m');
        $affiliateId = isset($filters['affiliate_id']) ? (int) $filters['affiliate_id'] : null;

        if (!$this->weeklyPayoutTableExists()) {
            return [
                'success' => true,
                'module_name' => 'Weekly Payout Stats',
                'message' => 'Weekly payouts table is not available yet. Run migrations to enable stats.',
                'month' => $monthKey,
                'stats' => [
                    'pending_count' => 0,
                    'paid_count' => 0,
                    'rejected_count' => 0,
                    'pending_amount' => 0,
                    'paid_amount' => 0,
                    'rejected_amount' => 0,
                ],
            ];
        }

        try {
            $query = AffiliateWeeklyPayout::query()
                ->where('month_key', $monthKey)
                ->when($affiliateId, fn ($q) => $q->where('affiliate_id', $affiliateId));

            $stats = [
                'pending_count' => (clone $query)->where('status', 'PENDING')->count(),
                'paid_count' => (clone $query)->where('status', 'PAID')->count(),
                'rejected_count' => (clone $query)->where('status', 'REJECTED')->count(),
                'pending_amount' => round((float) (clone $query)->where('status', 'PENDING')->sum('total_amount'), 2),
                'paid_amount' => round((float) (clone $query)->where('status', 'PAID')->sum('total_amount'), 2),
                'rejected_amount' => round((float) (clone $query)->where('status', 'REJECTED')->sum('total_amount'), 2),
            ];
        } catch (Throwable $e) {
            if (!$this->isMissingWeeklyPayoutTableException($e)) {
                throw $e;
            }

            return [
                'success' => true,
                'module_name' => 'Weekly Payout Stats',
                'message' => 'Weekly payouts table is not available yet. Run migrations to enable stats.',
                'month' => $monthKey,
                'stats' => [
                    'pending_count' => 0,
                    'paid_count' => 0,
                    'rejected_count' => 0,
                    'pending_amount' => 0,
                    'paid_amount' => 0,
                    'rejected_amount' => 0,
                ],
            ];
        }

        return [
            'success' => true,
            'module_name' => 'Weekly Payout Stats',
            'message' => 'Weekly payout stats loaded successfully.',
            'month' => $monthKey,
            'stats' => $stats,
        ];
    }

    public function payPayouts(array $payload, int $actorId): array
    {
        if (!$this->weeklyPayoutTableExists()) {
            return ['success' => false, 'code' => 'SCHEMA_MISSING', 'message' => 'Weekly payouts table is not available.'];
        }

        if ($actorId <= 0) {
            return ['success' => false, 'code' => 'UNAUTHORIZED', 'message' => 'Authenticated user is required.'];
        }

        $ids = array_values(array_unique(array_map('intval', $payload['payout_ids'] ?? [])));
        if (empty($ids)) {
            return ['success' => false, 'code' => 'INVALID_INPUT', 'message' => 'No payout rows selected.'];
        }

        return DB::transaction(function () use ($ids, $payload, $actorId) {
            $payouts = AffiliateWeeklyPayout::query()
                ->whereIn('id', $ids)
                ->lockForUpdate()
                ->get();

            if ($payouts->count() !== count($ids)) {
                return ['success' => false, 'code' => 'NOT_FOUND', 'message' => 'One or more payout rows were not found.'];
            }

            $invalid = $payouts->first(function ($payout) {
                return $this->statusValue($payout->status) !== 'PENDING';
            });
            if ($invalid) {
                return ['success' => false, 'code' => 'INVALID_STATE', 'message' => 'Only pending payout rows can be paid.'];
            }

            $transactionIds = [];
            $manualInvoiceNoRaw = $payload['manual_invoice_no'] ?? null;
            $sharedTxAmount = isset($payload['transaction_amount']) ? (float) $payload['transaction_amount'] : null;

            foreach ($payouts as $index => $payout) {
                $amountForRow = $payouts->count() === 1 && $sharedTxAmount !== null
                    ? $sharedTxAmount
                    : (float) $payout->total_amount;

                $tx = Transaction::create([
                    'payment_type' => 'Payout',
                    'payment_mode' => $payload['payment_mode'],
                    'payment_from' => $payload['payment_from'],
                    'payment_to' => $payload['payment_to'],
                    'transaction_amount' => $amountForRow,
                    'activity_date' => $payload['activity_date'] ?? now(),
                    'transaction_code' => $this->intOrNull($payload['transaction_code'] ?? null),
                    'manual_invoice_no' => $this->intOrNull($manualInvoiceNoRaw),
                    'transaction_details' => $payload['transaction_details'] ?? '',
                    'subscription_id' => 0,
                    'logged_in_user' => $actorId,
                    'advertiser_id' => null,
                    'affiliate_id' => $payout->affiliate_id,
                    'invoice_id' => null,
                    'weekly_payout_id' => $payout->id,
                ]);

                $transactionIds[] = $tx->id;

                $payout->update([
                    'status' => 'PAID',
                    'paid_at' => now(),
                    'paid_by' => $actorId,
                    'invoice_code' => $manualInvoiceNoRaw ?: $payout->invoice_code,
                    'notes' => $index === 0 ? ($payload['transaction_details'] ?? $payout->notes) : $payout->notes,
                ]);
            }

            return [
                'success' => true,
                'code' => 'OK',
                'message' => 'Weekly payout payment recorded successfully.',
                'count' => $payouts->count(),
                'transaction_ids' => $transactionIds,
            ];
        });
    }

    public function rejectPayout(array $payload, int $actorId): array
    {
        if (!$this->weeklyPayoutTableExists()) {
            return ['success' => false, 'code' => 'SCHEMA_MISSING', 'message' => 'Weekly payouts table is not available.'];
        }

        if ($actorId <= 0) {
            return ['success' => false, 'code' => 'UNAUTHORIZED', 'message' => 'Authenticated user is required.'];
        }

        $payoutId = (int) ($payload['payout_id'] ?? 0);
        if ($payoutId <= 0) {
            return ['success' => false, 'code' => 'INVALID_INPUT', 'message' => 'Invalid payout row id.'];
        }

        return DB::transaction(function () use ($payoutId, $payload, $actorId) {
            $payout = AffiliateWeeklyPayout::query()
                ->where('id', $payoutId)
                ->lockForUpdate()
                ->first();

            if (!$payout) {
                return ['success' => false, 'code' => 'NOT_FOUND', 'message' => 'Payout row not found.'];
            }

            if ($this->statusValue($payout->status) !== 'PENDING') {
                return ['success' => false, 'code' => 'INVALID_STATE', 'message' => 'Only pending payout rows can be rejected.'];
            }

            $payout->update([
                'status' => 'REJECTED',
                'rejected_reason' => $payload['rejected_reason'] ?? null,
                'rejected_at' => now(),
                'rejected_by' => $actorId,
            ]);

            return [
                'success' => true,
                'code' => 'OK',
                'message' => 'Weekly payout row rejected successfully.',
                'payout_id' => $payout->id,
            ];
        });
    }

    public function createManualPayout(array $payload, int $actorId): array
    {
        if (!$this->weeklyPayoutTableExists()) {
            return ['success' => false, 'code' => 'SCHEMA_MISSING', 'message' => 'Weekly payouts table is not available.'];
        }

        if ($actorId <= 0) {
            return ['success' => false, 'code' => 'UNAUTHORIZED', 'message' => 'Authenticated user is required.'];
        }

        $weekStart = Carbon::parse($payload['week_start'])->startOfDay();
        $weekEnd = Carbon::parse($payload['week_end'])->startOfDay();

        $row = AffiliateWeeklyPayout::create([
            'affiliate_id' => (int) $payload['affiliate_id'],
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'month_key' => $weekStart->format('Y-m'),
            'year' => (int) $weekStart->format('Y'),
            'week_number' => (int) $weekStart->format('W'),
            'entry_type' => 'Manual',
            'generated_key' => null,
            'work_basis' => 'Manual',
            'work_units' => isset($payload['work_units']) ? (float) $payload['work_units'] : null,
            'total_amount' => (float) $payload['total_amount'],
            'currency' => strtoupper((string) ($payload['currency'] ?? 'USD')),
            'status' => 'PENDING',
            'invoice_code' => $payload['invoice_code'] ?? null,
            'notes' => $payload['notes'] ?? null,
            'rejected_reason' => null,
            'breakdown' => [[
                'source' => 'manual',
                'created_by' => $actorId,
            ]],
            'created_by' => $actorId,
        ]);

        return [
            'success' => true,
            'code' => 'OK',
            'message' => 'Manual weekly payout row created successfully.',
            'payout_id' => $row->id,
        ];
    }

    /**
     * Build a non-persistent preview for a given month.
     */
    public function previewMonth(string $monthKey, ?int $affiliateId = null): Collection
    {
        $month = $this->parseMonthKey($monthKey);

        return $this->buildMonthlyRows($month, $affiliateId)->values();
    }

    /**
     * Persist generated weekly payout rows for a month without touching paid/rejected rows.
     */
    public function syncMonth(string $monthKey, ?int $affiliateId = null): Collection
    {
        if (!$this->weeklyPayoutTableExists()) {
            return collect();
        }

        $month = $this->parseMonthKey($monthKey);
        $rows = $this->buildMonthlyRows($month, $affiliateId);

        return DB::transaction(function () use ($rows, $monthKey, $affiliateId) {
            if ($affiliateId) {
                AffiliateWeeklyPayout::query()
                    ->where('month_key', $monthKey)
                    ->where('entry_type', 'Generated')
                    ->where('affiliate_id', $affiliateId)
                    ->where('status', 'PENDING')
                    ->delete();
            }

            foreach ($rows as $row) {
                $existing = AffiliateWeeklyPayout::query()
                    ->where('generated_key', $row['generated_key'])
                    ->first();

                if ($existing && $this->statusValue($existing->status) !== 'PENDING') {
                    continue;
                }

                AffiliateWeeklyPayout::updateOrCreate(
                    ['generated_key' => $row['generated_key']],
                    $row
                );
            }

            return AffiliateWeeklyPayout::query()
                ->where('month_key', $monthKey)
                ->when($affiliateId, fn ($query) => $query->where('affiliate_id', $affiliateId))
                ->orderBy('week_start')
                ->orderBy('affiliate_id')
                ->get();
        });
    }

    /**
     * Build generated rows for all accepted affiliate-offer assignments for a month.
     */
    protected function buildMonthlyRows(Carbon $month, ?int $affiliateId = null): Collection
    {
        $timezone = $this->sanitizeTimezone($this->getUserTimezone());
        $monthKey = $month->format('Y-m');
        $monthStartUtc = $month->copy()->startOfMonth()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $monthEndUtc = $month->copy()->endOfMonth()->setTimezone('UTC')->format('Y-m-d H:i:s');

        $rows = collect();

        foreach ($this->getAcceptedAssignments($affiliateId) as $assignment) {
            $basis = $this->resolveWorkBasis($assignment->ad_model_name ?? null);
            $contributions = match ($basis) {
                'Clicks' => $this->buildClickContributions($assignment, $timezone, $monthStartUtc, $monthEndUtc),
                'Views' => $this->buildViewContributions($assignment, $timezone, $monthStartUtc, $monthEndUtc),
                'Weekly' => $this->buildWeeklyFixedContributions($assignment, $timezone, $monthStartUtc, $monthEndUtc),
                default => $this->buildConversionContributions($assignment, $timezone, $monthStartUtc, $monthEndUtc),
            };

            foreach ($contributions as $contribution) {
                $key = $assignment->affiliate_id . '|' . $monthKey . '|' . $contribution['week_start'];
                $existing = $rows->get($key, [
                    'affiliate_id' => (int) $assignment->affiliate_id,
                    'week_start' => $contribution['week_start'],
                    'week_end' => $contribution['week_end'],
                    'month_key' => $monthKey,
                    'year' => (int) $month->format('Y'),
                    'week_number' => (int) Carbon::parse($contribution['week_start'])->format('W'),
                    'entry_type' => 'Generated',
                    'generated_key' => sprintf('generated:%d:%s:%s', $assignment->affiliate_id, $monthKey, $contribution['week_start']),
                    'work_basis' => $contribution['work_basis'],
                    'work_units' => $contribution['work_units'],
                    'total_amount' => 0,
                    'currency' => 'USD',
                    'status' => 'PENDING',
                    'invoice_code' => null,
                    'notes' => null,
                    'rejected_reason' => null,
                    'breakdown' => [],
                    'paid_at' => null,
                    'rejected_at' => null,
                    'paid_by' => null,
                    'rejected_by' => null,
                    'created_by' => null,
                ]);

                $existing['total_amount'] = round(((float) $existing['total_amount']) + ((float) $contribution['amount_usd']), 2);
                $existing['breakdown'][] = $contribution['breakdown'];

                if ($existing['work_basis'] !== $contribution['work_basis']) {
                    $existing['work_basis'] = 'Mixed';
                    $existing['work_units'] = null;
                } elseif ($existing['work_units'] !== null && $contribution['work_units'] !== null) {
                    $existing['work_units'] = round(((float) $existing['work_units']) + ((float) $contribution['work_units']), 4);
                }

                $rows->put($key, $existing);
            }
        }

        return $rows->filter(fn ($row) => (float) $row['total_amount'] > 0)
            ->sortBy([
                ['week_start', 'asc'],
                ['affiliate_id', 'asc'],
            ]);
    }

    protected function getAcceptedAssignments(?int $affiliateId = null): Collection
    {
        return DB::table('offer_affiliates as oa')
            ->join('offers as o', 'oa.offer_id', '=', 'o.id')
            ->leftJoin('ad_models as am', DB::raw("CAST(NULLIF(o.ad_model, '') AS BIGINT)"), '=', 'am.id')
            ->leftJoin('users as u', 'oa.affiliate_id', '=', 'u.id')
            ->select(
                'oa.offer_id',
                'oa.affiliate_id',
                'u.name as affiliate_name',
                'o.offer_name',
                'o.currency',
                'o.aff_pricing',
                'o.aff_model',
                'o.ad_model',
                'am.name as ad_model_name'
            )
            ->whereRaw("LOWER(TRIM(COALESCE(oa.request_status, ''))) IN (?, ?)", ['accepted', 'pending'])
            ->whereNull('o.deleted_at')
            ->when($affiliateId, fn ($query) => $query->where('oa.affiliate_id', $affiliateId))
            ->get();
    }

    protected function buildClickContributions(object $assignment, string $timezone, string $monthStartUtc, string $monthEndUtc): Collection
    {
        $rateUsd = $this->rateToUsd($assignment->aff_pricing, $assignment->currency);

        return $this->groupOfferTrackingsByWeek($assignment, $timezone, $monthStartUtc, $monthEndUtc)
            ->map(function ($row) use ($assignment, $rateUsd) {
                $workUnits = (float) $row->work_units;

                return $this->makeContribution(
                    $assignment,
                    'Clicks',
                    $workUnits,
                    round($workUnits * $rateUsd, 2),
                    $row->week_start_local,
                    [
                        'source' => 'offer_trackings',
                        'rate_usd' => $rateUsd,
                    ]
                );
            });
    }

    protected function buildViewContributions(object $assignment, string $timezone, string $monthStartUtc, string $monthEndUtc): Collection
    {
        $rateUsd = $this->rateToUsd($assignment->aff_pricing, $assignment->currency);
        $weekExpr = $this->weekStartExpression('c.created_at', $timezone);

        return DB::table('conversions as c')
            ->selectRaw("{$weekExpr} as week_start_local, SUM(COALESCE(c.impressions, 0)) as work_units")
            ->where('c.offer_id', $assignment->offer_id)
            ->where('c.affiliate_id', $assignment->affiliate_id)
            ->whereBetween('c.created_at', [$monthStartUtc, $monthEndUtc])
            ->whereRaw('COALESCE(c.impressions, 0) > 0')
            ->groupBy(DB::raw($weekExpr))
            ->get()
            ->map(function ($row) use ($assignment, $rateUsd) {
                $workUnits = (float) $row->work_units;

                return $this->makeContribution(
                    $assignment,
                    'Views',
                    $workUnits,
                    round($workUnits * $rateUsd, 2),
                    $row->week_start_local,
                    [
                        'source' => 'conversions.impressions',
                        'rate_usd' => $rateUsd,
                    ]
                );
            });
    }

    protected function buildWeeklyFixedContributions(object $assignment, string $timezone, string $monthStartUtc, string $monthEndUtc): Collection
    {
        $rateUsd = $this->rateToUsd($assignment->aff_pricing, $assignment->currency);

        return $this->groupOfferTrackingsByWeek($assignment, $timezone, $monthStartUtc, $monthEndUtc)
            ->map(function ($row) use ($assignment, $rateUsd) {
                return $this->makeContribution(
                    $assignment,
                    'Weekly',
                    1,
                    round($rateUsd, 2),
                    $row->week_start_local,
                    [
                        'source' => 'offer_trackings.activity',
                        'activity_count' => (int) $row->work_units,
                        'rate_usd' => $rateUsd,
                    ]
                );
            });
    }

    protected function buildConversionContributions(object $assignment, string $timezone, string $monthStartUtc, string $monthEndUtc): Collection
    {
        $weekExpr = $this->weekStartExpression('c.created_at', $timezone);

        return DB::table('conversions as c')
            ->selectRaw("{$weekExpr} as week_start_local, COUNT(*) as work_units, SUM(COALESCE(c.payout_usd, 0)) as amount_usd")
            ->where('c.offer_id', $assignment->offer_id)
            ->where('c.affiliate_id', $assignment->affiliate_id)
            ->whereBetween('c.created_at', [$monthStartUtc, $monthEndUtc])
            ->whereIn('c.status', $this->eligibleConversionStatuses())
            ->groupBy(DB::raw($weekExpr))
            ->get()
            ->map(function ($row) use ($assignment) {
                return $this->makeContribution(
                    $assignment,
                    'Conversions',
                    (float) $row->work_units,
                    (float) $row->amount_usd,
                    $row->week_start_local,
                    [
                        'source' => 'conversions.payout_usd',
                        'eligible_statuses' => $this->eligibleConversionStatuses(),
                    ]
                );
            });
    }

    protected function groupOfferTrackingsByWeek(object $assignment, string $timezone, string $monthStartUtc, string $monthEndUtc): Collection
    {
        $weekExpr = $this->weekStartExpression('ot.created_at', $timezone);

        return DB::table('offer_trackings as ot')
            ->selectRaw("{$weekExpr} as week_start_local, COUNT(*) as work_units")
            ->where('ot.offer_id', $assignment->offer_id)
            ->where('ot.aff_id', $assignment->affiliate_id)
            ->whereBetween('ot.created_at', [$monthStartUtc, $monthEndUtc])
            ->groupBy(DB::raw($weekExpr))
            ->get();
    }

    protected function makeContribution(object $assignment, string $basis, ?float $workUnits, float $amountUsd, string $weekStart, array $meta = []): array
    {
        $weekStartDate = Carbon::parse($weekStart)->startOfDay();

        return [
            'week_start' => $weekStartDate->toDateString(),
            'week_end' => $weekStartDate->copy()->endOfWeek(Carbon::SUNDAY)->toDateString(),
            'work_basis' => $basis,
            'work_units' => $workUnits,
            'amount_usd' => round($amountUsd, 2),
            'breakdown' => array_merge([
                'offer_id' => (int) $assignment->offer_id,
                'offer_name' => $assignment->offer_name,
                'affiliate_id' => (int) $assignment->affiliate_id,
                'affiliate_name' => $assignment->affiliate_name,
                'ad_model_name' => $assignment->ad_model_name,
                'currency' => $assignment->currency ?? 'USD',
                'work_basis' => $basis,
                'work_units' => $workUnits,
                'amount_usd' => round($amountUsd, 2),
            ], $meta),
        ];
    }

    protected function rateToUsd($rate, ?string $currency): float
    {
        $rate = (float) ($rate ?? 0);
        $currency = strtoupper((string) ($currency ?? 'USD'));

        return (float) $this->getNewCurrencyValue($rate, $currency);
    }

    protected function resolveWorkBasis(?string $adModelName): string
    {
        $normalized = strtolower(trim((string) $adModelName));

        if (str_contains($normalized, 'click')) {
            return 'Clicks';
        }

        if (str_contains($normalized, 'view') || str_contains($normalized, 'impression')) {
            return 'Views';
        }

        if (str_contains($normalized, 'week')) {
            return 'Weekly';
        }

        return 'Conversions';
    }

    protected function eligibleConversionStatuses(): array
    {
        return ['confirmed'];
    }

    protected function parseMonthKey(string $monthKey): Carbon
    {
        return Carbon::createFromFormat('Y-m', $monthKey, $this->sanitizeTimezone($this->getUserTimezone()))->startOfMonth();
    }

    protected function weekStartExpression(string $column, string $timezone): string
    {
        return "DATE_TRUNC('week', {$column} AT TIME ZONE 'UTC' AT TIME ZONE '{$timezone}')::date";
    }

    protected function sanitizeTimezone(?string $timezone): string
    {
        $timezone = preg_replace('/[^A-Za-z0-9_\/+:-]/', '', (string) $timezone);

        return $timezone ?: 'UTC';
    }

    protected function statusValue($status): string
    {
        if ($status instanceof \BackedEnum) {
            return (string) $status->value;
        }

        if ($status instanceof \UnitEnum) {
            return (string) $status->name;
        }

        return (string) $status;
    }

    protected function intOrNull($value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    protected function weeklyPayoutTableExists(): bool
    {
        try {
            $connection = DB::connection()->getName();

            return Schema::connection($connection)->hasTable('affiliate_weekly_payouts');
        } catch (Throwable $e) {
            return false;
        }
    }

    protected function isMissingWeeklyPayoutTableException(Throwable $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, 'affiliate_weekly_payouts')
            && (str_contains($message, '42p01') || str_contains($message, 'does not exist') || str_contains($message, 'undefined table'));
    }
}
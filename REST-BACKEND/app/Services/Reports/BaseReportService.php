<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\DTOs\ReportFilterDTO;
use App\Http\Traits\CommonFunctions;

class BaseReportService
{
    use CommonFunctions, \App\Http\Traits\TimezoneHelper;

    /**
     * Apply filters to a query builder
     * 
     * @param Builder $query The query builder instance
     * @param ReportFilterDTO $filters The filters to apply
     * @return Builder
     */
    protected function applyFilters(Builder $query, ReportFilterDTO $filters): Builder
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user) {
            if ($user->isAffiliateManager()) {
                $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('offer_trackings.aff_id', count($assignedAffIds) ? $assignedAffIds : [-1]);
            } elseif ($user->isAdvertiserManager()) {
                $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('offers.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }

        // Date range filter
        if ($filters->from && $filters->to) {
            $fromUtc = $this->getUtcStartOfDay($filters->from);
            $toUtc   = $this->getUtcEndOfDay($filters->to);
            $query->whereBetween('offer_trackings.created_at', [$fromUtc, $toUtc]);
        }

        // Offer filter
        if ($filters->offer_id) {
            $query->where('offers.id', $filters->offer_id);
        }

        // Affiliate filter
        if ($filters->affiliate_id) {
            $query->where('offer_trackings.aff_id', $filters->affiliate_id);
        }

        // Advertiser filter
        if ($filters->advertiser_id) {
            $query->where('offers.advertiser_id', $filters->advertiser_id);
        }

        // Country filter
        if ($filters->country) {
            $arrC = $this->getCountry();
            $country = isset($arrC[$filters->country]) ? $arrC[$filters->country] : $filters->country;
            $query->where('offer_trackings.country', $country);
        }

        // OS filter
        if ($filters->os) {
            $query->where('offer_trackings.os', 'LIKE', "%{$filters->os}%");
        }

        // Browser filter
        if ($filters->browser) {
            $query->where('offer_trackings.browser', $filters->browser);
        }

        // Device filter
        if ($filters->device) {
            $query->where('offer_trackings.device_id', $filters->device);
        }

        // Category filter
        if ($filters->category) {
            $query->where('offers.category_id', $filters->category);
        }

        // Conversion status filter
        if ($filters->conversion_status) {
            if ($filters->conversion_status == \App\Enums\ConversionStatus::Converted->value) {
                $query->where('offer_trackings.aff_click_id', '!=', '');
            } elseif ($filters->conversion_status == \App\Enums\ConversionStatus::Pending->value) {
                $query->where('offer_trackings.aff_click_id', '=', '');
            }
        }

        return $query;
    }

    /**
     * Get click and conversion report with filters
     * 
     * @param ReportFilterDTO $filters
     * @param int|null $scopeUserId User ID to scope results to (advertiser or affiliate)
     * @param string|null $scopeType Type of scoping: 'advertiser' or 'affiliate'
     * @return array
     */
    public function getClickConversionReport(ReportFilterDTO $filters, ?int $scopeUserId = null, ?string $scopeType = null): array
    {
        $query = DB::table('offer_trackings')
            ->select(
                'offer_trackings.*',
                'offer_trackings.id as otid',
                'offer_trackings.created_at as link_clicked_at',
                'offer_trackings.country as ot_country',
                'offer_trackings.os as ot_os',
                'offer_trackings.browser as ot_browser',
                'affis.id as aff_id',
                'affis.name as aff_name',
                'adverts.id as adv_id',
                'adverts.name as advert_name',
                'offers.id as offer_id',
                'offers.offer_name',
                'devices.device_name',
                DB::raw("CASE WHEN offer_trackings.aff_click_id != '' THEN 1 ELSE 0 END as is_conversion")
            )
            ->leftJoin('offers', 'offer_trackings.offer_id', '=', 'offers.id')
            ->leftJoin('categories', 'offers.category_id', '=', 'categories.id')
            ->leftJoin('devices', 'offer_trackings.device_id', '=', 'devices.id')
            ->leftJoin('users as affis', 'offer_trackings.aff_id', '=', 'affis.id')
            ->leftJoin('users as adverts', 'offer_trackings.adv_id', '=', 'adverts.id');

        // Apply scoping based on user type
        if ($scopeType === 'advertiser' && $scopeUserId) {
            $query->where('offers.advertiser_id', $scopeUserId);
        } elseif ($scopeType === 'affiliate' && $scopeUserId) {
            $query->where('offer_trackings.aff_id', $scopeUserId);
        }

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        // Paginate results
        $results = $query->orderBy('offer_trackings.id', 'desc')->paginate($filters->limit);
        
        $user = \Illuminate\Support\Facades\Auth::user();

        $results->getCollection()->transform(function ($row) use ($user) {
            return $this->formatClickRow($row, $user);
        });

        return [
            'module_name' => 'Click & Conversion Report',
            'data' => $results,
            'filters' => $filters->toArray()
        ];
    }

    /**
     * Get summary report with filters
     * 
     * @param ReportFilterDTO $filters
     * @param int|null $scopeUserId User ID to scope results to (advertiser or affiliate)
     * @param string|null $scopeType Type of scoping: 'advertiser' or 'affiliate'
     * @return array
     */
    public function getSummaryReport(ReportFilterDTO $filters, ?int $scopeUserId = null, ?string $scopeType = null): array
    {
        $tz = $this->getUserTimezone();
        $tzStr = "AT TIME ZONE 'UTC' AT TIME ZONE '{$tz}'";
        
        $query = DB::table('offer_trackings')
            ->select(
                DB::raw("CAST(offer_trackings.created_at {$tzStr} AS DATE) as date"),
                DB::raw("COUNT(CASE WHEN offer_trackings.event = '" . \App\Enums\TrackingEventType::Click->value . "' THEN 1 END) as clicks"),
                DB::raw("COUNT(CASE WHEN offer_trackings.event != '" . \App\Enums\TrackingEventType::Click->value . "' THEN 1 END) as conversions"),
                DB::raw('SUM(COALESCE(offer_trackings.revenue_usd, 0)) as cost')
            )
            ->leftJoin('offers', 'offer_trackings.offer_id', '=', 'offers.id')
            ->leftJoin('categories', 'offers.category_id', '=', 'categories.id')
            ->leftJoin('devices', 'offer_trackings.device_id', '=', 'devices.id');

        // Apply scoping based on user type
        if ($scopeType === 'advertiser' && $scopeUserId) {
            $query->where('offers.advertiser_id', $scopeUserId);
        } elseif ($scopeType === 'affiliate' && $scopeUserId) {
            $query->where('offer_trackings.aff_id', $scopeUserId);
        }

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        // Group by date and order
        $stats = $query
            ->groupBy(DB::raw("CAST(offer_trackings.created_at {$tzStr} AS DATE)"))
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->isObserver()) {
            foreach ($stats as $row) {
                $row->cost = 0;
            }
        }

        return [
            'module_name' => 'Summary Report',
            'stats' => $stats,
            'filters' => $filters->toArray()
        ];
    }

    /**
     * Get detailed click information by transaction ID (tid)
     * 
     * @param string $tid Transaction ID
     * @param int|null $scopeUserId User ID to scope results to
     * @param string|null $scopeType Type of scoping: 'advertiser' or 'affiliate'
     * @return array
     */
    public function getClickDetail(string $tid, ?int $scopeUserId = null, ?string $scopeType = null): array
    {
        $query = DB::table('offer_trackings')
            ->select(
                'offer_trackings.*',
                'offer_trackings.id as otid',
                'offer_trackings.created_at as link_clicked_at',
                'offer_trackings.country as ot_country',
                'offer_trackings.os as ot_os',
                'offer_trackings.browser as ot_browser',
                'offers.offer_name',
                'affis.name as aff_name',
                'adverts.name as advert_name',
                'devices.device_name'
            )
            ->join('offers', 'offer_trackings.offer_id', '=', 'offers.id')
            ->leftJoin('devices', 'offer_trackings.device_id', '=', 'devices.id')
            ->join('users as affis', 'offer_trackings.aff_id', '=', 'affis.id')
            ->join('users as adverts', 'offer_trackings.adv_id', '=', 'adverts.id')
            ->where('offer_trackings.tid', $tid);

        // Apply scoping based on user type
        if ($scopeType === 'advertiser' && $scopeUserId) {
            $query->where('offers.advertiser_id', $scopeUserId);
        } elseif ($scopeType === 'affiliate' && $scopeUserId) {
            $query->where('offer_trackings.aff_id', $scopeUserId);
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user) {
            if ($user->isAffiliateManager()) {
                $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('offer_trackings.aff_id', count($assignedAffIds) ? $assignedAffIds : [-1]);
            } elseif ($user->isAdvertiserManager()) {
                $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('offers.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }

        $click = $query->first();

        if (!$click) {
            return ['success' => false, 'message' => 'Click record not found'];
        }

        if ($user && $user->isObserver()) {
            if (isset($click->revenue_usd)) $click->revenue_usd = 0;
            if (isset($click->payout_usd)) $click->payout_usd = 0;
            if (isset($click->revenue)) $click->revenue = 0;
            if (isset($click->payout)) $click->payout = 0;
        }

        // Convert timestamps to local timezone if present
        if (isset($click->link_clicked_at)) {
            $click->link_clicked_at = $this->formatUtcToLocal($click->link_clicked_at);
        }
        if (isset($click->created_at)) {
            $click->created_at = $this->formatUtcToLocal($click->created_at);
        }

        if ($click) {
            $this->formatClickRow($click, $user);
        }

        return ['success' => true, 'data' => $click];
    }

    /**
     * Format a conversion row, calculating fallbacks and masking fields based on user role.
     */
    public function formatConversionRow($row, $user)
    {
        // 1. Time Zone conversions
        if (isset($row->conversion_time)) {
            $row->conversion_time = $this->formatUtcToLocal($row->conversion_time);
        }
        if (isset($row->created_at)) {
            $row->created_at = $this->formatUtcToLocal($row->created_at);
        }
        if (isset($row->click_time)) {
            $row->click_time = $this->formatUtcToLocal($row->click_time);
        }
        if (isset($row->status_changed_at)) {
            $row->status_changed_at = $this->formatUtcToLocal($row->status_changed_at);
        }
        if (isset($row->postback_fired_at)) {
            $row->postback_fired_at = $this->formatUtcToLocal($row->postback_fired_at);
        }
        if (isset($row->postback_received_at)) {
            $row->postback_received_at = $this->formatUtcToLocal($row->postback_received_at);
        }
        if (isset($row->impression_time)) {
            $row->impression_time = $this->formatUtcToLocal($row->impression_time);
        }

        // 2. Dynamic Fallback Calculations
        // Fallback for click_to_conv_time
        if (!isset($row->click_to_conv_time) || $row->click_to_conv_time === null) {
            $convTime = $row->conversion_time ?? $row->created_at ?? null;
            $clickTime = $row->click_time ?? $row->ot_click_time ?? null;
            if ($convTime && $clickTime) {
                $row->click_to_conv_time = max(0, strtotime($convTime) - strtotime($clickTime));
            } else {
                $row->click_to_conv_time = null;
            }
        }

        // Fallback for click_to_conv_display
        if (!isset($row->click_to_conv_display) || $row->click_to_conv_display === null || $row->click_to_conv_display === '') {
            $seconds = $row->click_to_conv_time;
            if ($seconds === null) {
                $row->click_to_conv_display = '-';
            } else {
                if ($seconds < 60) {
                    $row->click_to_conv_display = $seconds . 's';
                } else {
                    $minutes = floor($seconds / 60);
                    $remSeconds = $seconds % 60;
                    if ($minutes < 60) {
                        $row->click_to_conv_display = $minutes . 'm ' . $remSeconds . 's';
                    } else {
                        $hours = floor($minutes / 60);
                        $remMinutes = $minutes % 60;
                        if ($hours < 24) {
                            $row->click_to_conv_display = $hours . 'h ' . $remMinutes . 'm';
                        } else {
                            $days = floor($hours / 24);
                            $remHours = $hours % 24;
                            $row->click_to_conv_display = $days . 'd ' . $remHours . 'h';
                        }
                    }
                }
            }
        }

        // Fallback for click_ip and conversion_ip
        if (!isset($row->click_ip) || $row->click_ip === null || $row->click_ip === '') {
            $row->click_ip = $row->ot_click_ip ?? null;
        }
        if (!isset($row->conversion_ip) || $row->conversion_ip === null || $row->conversion_ip === '') {
            $row->conversion_ip = $row->ip ?? null;
        }

        // Fallback for ip_match
        if (!isset($row->ip_match) || $row->ip_match === null) {
            if ($row->click_ip && $row->conversion_ip) {
                $row->ip_match = strtolower(trim($row->click_ip)) === strtolower(trim($row->conversion_ip));
            } else {
                $row->ip_match = false;
            }
        }

        // 3. Role-Based Data Masking
        $userType = $user ? $user->user_type : 'guest';
        $isObserver = $user ? $user->isObserver() : false;

        if ($userType === 'affiliate') {
            $row->advertiser_id = null;
            $row->advertiser_name = 'Advertiser';
            $row->advertiser_price = 0;
            $row->revenue = 0;
            $row->revenue_usd = 0;
            $row->profit = 0;
            $row->margin_pct = 0;
            $row->adv_sub1 = null;
            $row->adv_sub2 = null;
            $row->adv_sub3 = null;
            $row->adv_sub4 = null;
            $row->adv_sub5 = null;
            $row->adv_sub6 = null;
            $row->adv_sub7 = null;
            $row->adv_sub8 = null;
            $row->adv_sub9 = null;
            $row->adv_sub10 = null;
            $row->landing_page_id = null;
            $row->landing_page_url = null;
            $row->tracking_url = null;
            $row->redirect_url = null;
            $row->rejection_reason = null;
        } elseif ($userType === 'advertiser') {
            $row->affiliate_id = null;
            $row->affiliate_name = 'Affiliate';
            $row->affiliate_price = 0;
            $row->payout = 0;
            $row->payout_usd = 0;
            $row->profit = 0;
            $row->margin_pct = 0;
            $row->aff_sub1 = null;
            $row->aff_sub2 = null;
            $row->aff_sub3 = null;
            $row->aff_sub4 = null;
            $row->aff_sub5 = null;
            $row->aff_sub6 = null;
            $row->aff_sub7 = null;
            $row->aff_sub8 = null;
            $row->aff_sub9 = null;
            $row->aff_sub10 = null;
            $row->postback_status = null;
            $row->postback_fired_at = null;
            $row->postback_received_at = null;
            $row->postback_url_fired = null;
            $row->postback_http_code = null;
            $row->postback_attempts = 0;
        }

        // Mask financial details for observers
        if ($isObserver) {
            $row->revenue = 0;
            $row->payout = 0;
            $row->revenue_usd = 0;
            $row->payout_usd = 0;
            $row->profit = 0;
            $row->sale = 0;
            $row->affiliate_price = 0;
            $row->advertiser_price = 0;
        }

        return $row;
    }

    /**
     * Format a click row, calculating fallbacks and masking fields based on user role.
     */
    public function formatClickRow($row, $user)
    {
        if (isset($row->link_clicked_at)) {
            $row->link_clicked_at = $this->formatUtcToLocal($row->link_clicked_at);
        }
        if (isset($row->created_at)) {
            $row->created_at = $this->formatUtcToLocal($row->created_at);
        }
        if (isset($row->transferred_at)) {
            $row->transferred_at = $this->formatUtcToLocal($row->transferred_at);
        }

        $userType = $user ? $user->user_type : 'guest';
        $isObserver = $user ? $user->isObserver() : false;

        if ($userType === 'affiliate') {
            $row->adv_id = null;
            $row->advert_name = 'Advertiser';
            $row->revenue = 0;
            $row->revenue_usd = 0;
        } elseif ($userType === 'advertiser') {
            $row->aff_id = null;
            $row->aff_name = 'Affiliate';
            $row->payout = 0;
            $row->payout_usd = 0;
        }

        if ($isObserver) {
            $row->revenue = 0;
            $row->payout = 0;
            $row->revenue_usd = 0;
            $row->payout_usd = 0;
        }

        return $row;
    }
}

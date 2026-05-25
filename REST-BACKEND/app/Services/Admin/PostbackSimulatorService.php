<?php

namespace App\Services\Admin;

use App\Models\Conversion;
use App\Models\AffiliatePostbackLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostbackSimulatorService
{
    // ─── Advertiser → Network ─────────────────────────────────────────────────

    public function buildAdvertiserPostbackUrl(array $params): string
    {
        $base  = config('app.url') . '/api/offer/sd';
        $query = http_build_query(array_filter($params, fn($v) => $v !== null && $v !== ''));
        return $base . '?' . $query;
    }

    public function fireAdvertiserPostback(array $params): array
    {
        $url    = config('app.url') . '/api/offer/sd';
        $clean  = array_filter($params, fn($v) => $v !== null && $v !== '');
        $builtUrl = $url . '?' . http_build_query($clean);

        $start = microtime(true);
        try {
            $response = Http::timeout(15)->get($url, $clean);
            $duration = round((microtime(true) - $start) * 1000);

            return [
                'success'       => true,
                'request_url'   => $builtUrl,
                'http_status'   => $response->status(),
                'response_body' => $response->body(),
                'response_json' => $response->json(),
                'duration_ms'   => $duration,
            ];
        } catch (\Exception $e) {
            Log::error('PostbackSimulator fireAdvertiserPostback: ' . $e->getMessage());
            return [
                'success'     => false,
                'request_url' => $builtUrl,
                'error'       => $e->getMessage(),
            ];
        }
    }

    // ─── Network → Affiliate ──────────────────────────────────────────────────

    public function getAffiliatePostbackConfig(int $affiliateId): ?array
    {
        $row = DB::table('affiliate_postback_columns')
            ->where('uid', $affiliateId)
            ->first();
        return $row ? (array) $row : null;
    }

    /**
     * Build affiliate postback URL.
     * `affiliate_postback_columns.url`  = base URL
     * Other string columns = the param name the affiliate uses for that value.
     * e.g. tid="click_id" → appends ?click_id={actual_tid}
     */
    public function buildAffiliatePostbackUrl(array $config, array $conv): string
    {
        $base = rtrim($config['url'] ?? '', '?&');
        if (!$base) return '';

        // Map: column_name => actual value from conversion
        $paramMap = [
            'tid'          => $conv['tid']           ?? '',
            'aff_id'       => $conv['affiliate_id']  ?? '',
            'event_id'     => $conv['event_id']      ?? '',
            'sub_aff_id'   => $conv['source']        ?? '',
            'aff_sub1'     => $conv['aff_sub1']      ?? '',
            'aff_sub2'     => $conv['aff_sub2']      ?? '',
            'aff_sub3'     => $conv['aff_sub3']      ?? '',
            'aff_sub4'     => $conv['aff_sub4']      ?? '',
            'aff_sub5'     => $conv['aff_sub5']      ?? '',
            'source'       => $conv['source']        ?? '',
            'android_id'   => $conv['android_id']    ?? '',
            'device_id'    => $conv['device_id']     ?? '',
            'google_aid'   => $conv['google_aid']    ?? '',
            'iosidfa'      => $conv['ios_idfa']      ?? '',
            'offer_id'     => $conv['offer_id']      ?? '',
            'offer_name'   => $conv['offer_name']    ?? '',
            'ip'           => $conv['ip']            ?? '',
            'country'      => $conv['country']       ?? '',
            'os'           => $conv['os']            ?? '',
            'os_ver'       => $conv['os_ver']        ?? '',
            'raw_useragent'=> $conv['useragent']     ?? '',
            'adv_id'       => $conv['advertiser_id'] ?? '',
            'timestamp'    => $conv['date']          ?? '',
            'useragent'    => $conv['useragent']     ?? '',
            'deep_link'    => '',
        ];

        $queryParts = [];
        foreach ($paramMap as $colName => $actualValue) {
            $paramAlias = $config[$colName] ?? null; // alias the affiliate chose
            if ($paramAlias !== null && $paramAlias !== '') {
                $queryParts[$paramAlias] = $actualValue;
            }
        }

        $separator = str_contains($base, '?') ? '&' : '?';
        return $queryParts ? $base . $separator . http_build_query($queryParts) : $base;
    }

    public function fireAffiliatePostback(int $conversionId, bool $dryRun = false): array
    {
        $conversion = Conversion::find($conversionId);
        if (!$conversion) {
            return ['success' => false, 'error' => "Conversion #{$conversionId} not found."];
        }

        $config = $this->getAffiliatePostbackConfig((int) $conversion->affiliate_id);
        if (!$config) {
            return [
                'success' => false,
                'error'   => "No postback URL configured for affiliate ID {$conversion->affiliate_id}.",
            ];
        }

        $firedUrl = $this->buildAffiliatePostbackUrl($config, $conversion->toArray());
        if (!$firedUrl) {
            return ['success' => false, 'error' => 'Postback URL is empty after building.'];
        }

        $convSummary = $conversion->only(['id', 'tid', 'offer_name', 'affiliate_name', 'event', 'status', 'payout', 'currency']);

        if ($dryRun) {
            return [
                'success'      => true,
                'dry_run'      => true,
                'template_url' => $config['url'],
                'built_url'    => $firedUrl,
                'conversion'   => $convSummary,
            ];
        }

        $start = microtime(true);
        try {
            $response = Http::timeout(15)->get($firedUrl);
            $duration = round((microtime(true) - $start) * 1000);

            AffiliatePostbackLog::create([
                'conversion_id' => $conversion->id,
                'affiliate_id'  => $conversion->affiliate_id,
                'url_fired'     => $firedUrl,
                'http_status'   => $response->status(),
                'response_body' => substr($response->body(), 0, 5000),
            ]);

            return [
                'success'       => true,
                'dry_run'       => false,
                'template_url'  => $config['url'],
                'built_url'     => $firedUrl,
                'http_status'   => $response->status(),
                'response_body' => $response->body(),
                'duration_ms'   => $duration,
                'conversion'    => $convSummary,
            ];
        } catch (\Exception $e) {
            AffiliatePostbackLog::create([
                'conversion_id' => $conversion->id,
                'affiliate_id'  => $conversion->affiliate_id,
                'url_fired'     => $firedUrl,
                'http_status'   => 0,
                'response_body' => 'Error: ' . $e->getMessage(),
            ]);
            return [
                'success'    => false,
                'dry_run'    => false,
                'built_url'  => $firedUrl,
                'error'      => $e->getMessage(),
                'conversion' => $convSummary,
            ];
        }
    }

    // ─── Lookup helpers ───────────────────────────────────────────────────────

    public function getRecentClicks(int $limit = 50): array
    {
        return DB::table('offer_trackings')
            ->select('id', 'tid', 'aff_id', 'offer_id', 'offer_name', 'country', 'created_at')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getRecentConversions(int $limit = 50): array
    {
        return DB::table('conversions')
            ->select('id', 'tid', 'offer_name', 'affiliate_name', 'affiliate_id', 'event', 'status', 'payout', 'currency', 'date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
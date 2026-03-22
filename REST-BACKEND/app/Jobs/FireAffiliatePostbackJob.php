<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Conversion;
use App\Models\AffiliatePostbackColumn;
use App\Models\AffiliatePostbackLog;

class FireAffiliatePostbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $conversionId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($conversionId)
    {
        $this->conversionId = $conversionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $conversion = Conversion::find($this->conversionId);

            if (!$conversion) {
                return;
            }

            // Find the global postback URL for this affiliate
            $postbackSettings = AffiliatePostbackColumn::where('uid', $conversion->affiliate_id)
                ->whereNull('oid')
                ->first();

            // If no settings or no URL, nothing to fire
            if (!$postbackSettings || empty($postbackSettings->url)) {
                return;
            }

            $url = $postbackSettings->url;

            // Macro Replacements
            $url = str_replace('{clickid}', $conversion->aff_click_id, $url);
            $url = str_replace('{payout}', $conversion->payout, $url);
            $url = str_replace('{status}', $conversion->status, $url); // Since this is fired on approval, it passes 'confirmed'
            $url = str_replace('{currency}', $conversion->currency, $url);

            // In case affiliates use other standard macros, we can replace them too to be helpful
            $url = str_replace('{event_id}', $conversion->event_id, $url);
            $url = str_replace('{offer_id}', $conversion->offer_id, $url);
            $url = str_replace('{adv_id}', $conversion->advertiser_id, $url);
            $url = str_replace('{sub1}', $conversion->aff_sub1, $url);
            $url = str_replace('{sub2}', $conversion->aff_sub2, $url);

            // Fire the request securely with a short timeout to prevent hanging worker
            $response = Http::timeout(10)->get($url);

            $httpStatus = $response->status();
            $responseBody = $response->body();

            // Store the result in our new log table
            AffiliatePostbackLog::create([
                'conversion_id' => $conversion->id,
                'affiliate_id' => $conversion->affiliate_id,
                'url_fired' => $url,
                'http_status' => (string) $httpStatus,
                'response_body' => $responseBody
            ]);

        } catch (\Exception $e) {
            Log::error("FireAffiliatePostbackJob Failed: " . $e->getMessage());

            // If it fails on the network level, log the exception message as response
            if (isset($conversion) && isset($url)) {
                AffiliatePostbackLog::create([
                    'conversion_id' => $conversion->id,
                    'affiliate_id'  => $conversion->affiliate_id,
                    'url_fired'     => $url,
                    'http_status'   => 'ERR',
                    'response_body' => substr($e->getMessage(), 0, 1000)
                ]);
            }
        }
    }
}

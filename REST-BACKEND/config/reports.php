<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Report Export Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the async report export system.
    | Reports are ephemeral and will be automatically cleaned up.
    |
    */

    // How long exports remain valid (in minutes)
    'export_ttl_minutes' => env('REPORTS_EXPORT_TTL_MINUTES', 10),

    // Dedicated queue name for report generation jobs
    'queue' => env('REPORTS_QUEUE', 'reports'),

    // Temporary storage path for generated reports
    'temp_path' => storage_path('app/tmp/reports'),

    // Temporary rollout switch for the new weekly payouts management flow.
    'weekly_payouts_management_enabled' => env('WEEKLY_PAYOUTS_MANAGEMENT_ENABLED', true),

    // Supported report types and their query builders
    'types' => [
        'clicks' => \App\Models\OfferTracking::class,
        'conversions' => \App\Models\OfferTracking::class,
        'payouts' => \App\Models\PayoutRule::class,
    ],

];

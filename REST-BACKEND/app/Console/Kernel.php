<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        \App\Console\Commands\AffiliatePushData::class,
        \App\Console\Commands\CleanupReportExports::class,
        // ── Notifications ────────────────────────────────────────────────
        \App\Console\Commands\ProcessScheduledNotifications::class,
        \App\Console\Commands\SubscriptionExpiryNotifier::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('affiliate:push-data')->hourly();

        // Cleanup expired report exports every 5 minutes
        $schedule->command('reports:cleanup')->everyFiveMinutes();

        // ── Notifications ────────────────────────────────────────────────
        $schedule->command('notifications:process-scheduled')->everyMinute();
        $schedule->command('notifications:subscription-expiry')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

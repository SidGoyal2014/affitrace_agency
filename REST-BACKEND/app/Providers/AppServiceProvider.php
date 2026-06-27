<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\PdfGeneratorInterface;
use App\Services\Adapters\DomPdfGeneratorAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PdfGeneratorInterface::class, DomPdfGeneratorAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::find(1);
                if ($settings) {
                    $view->with('branding', [
                        'platform_name' => $settings->platform_name ?? 'AffiTrace Agency',
                        'platform_logo' => $settings->platform_logo ? url('storage/branding/' . $settings->platform_logo) : null,
                        'platform_favicon' => $settings->platform_favicon ? url('storage/branding/' . $settings->platform_favicon) : null,
                        'primary_color' => $settings->primary_color ?? '#2563EB',
                        'secondary_color' => $settings->secondary_color ?? '#1E293B',
                        'accent_color' => $settings->accent_color ?? '#10B981',
                        'support_email' => $settings->support_email ?? null,
                        'custom_copyright' => $settings->custom_copyright ?? null,
                    ]);
                    return;
                }
            }

            // Defaults fallback
            $view->with('branding', [
                'platform_name' => 'AffiTrace Agency',
                'platform_logo' => null,
                'platform_favicon' => null,
                'primary_color' => '#2563EB',
                'secondary_color' => '#1E293B',
                'accent_color' => '#10B981',
                'support_email' => null,
                'custom_copyright' => null,
            ]);
        });
    }

}

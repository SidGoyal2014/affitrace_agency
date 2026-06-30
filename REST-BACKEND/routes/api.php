<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication
Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::get('branding', [App\Http\Controllers\Api\AuthController::class, 'get_branding']);

// Public Tracking (Server-to-Server)
// api.tenant is now in Kernel, so it applies to all API routes
Route::post('offer/sed/{oid}/{eid}', [App\Http\Controllers\TrackingController::class, 'sed']);
Route::get('offer/sd', [App\Http\Controllers\TrackingController::class, 'sd']);
Route::get('pixel', [App\Http\Controllers\PixelController::class, 'track']);

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('refresh-token', [App\Http\Controllers\Api\AuthController::class, 'refreshToken']);
    Route::get('me', [App\Http\Controllers\Api\AuthController::class, 'me']);
    Route::post('update-timezone', [App\Http\Controllers\Api\AuthController::class, 'updateTimezone']);

    // ─── Notifications: Inbox (all authenticated users) ───────────────────────
    Route::prefix('v1/notifications')->group(function () {
        Route::get('inbox', [App\Http\Controllers\Api\V1\NotificationController::class, 'inbox']);
        Route::post('inbox/read-all', [App\Http\Controllers\Api\V1\NotificationController::class, 'markAllRead']);
        Route::get('inbox/{id}', [App\Http\Controllers\Api\V1\NotificationController::class, 'inboxShow']);
        Route::patch('inbox/{id}/read', [App\Http\Controllers\Api\V1\NotificationController::class, 'markRead']);
        Route::get('unread-count', [App\Http\Controllers\Api\V1\NotificationController::class, 'unreadCount']);
        // Route::post('send', [App\Http\Controllers\Api\V1\NotificationController::class, 'send']);
        // Route::get('sent', [App\Http\Controllers\Api\V1\NotificationController::class, 'sentIndex']);
    });

    // Impersonation Exit (Must be outside 'admin' middleware so impersonated users can call it)
    Route::post('admin/stop-impersonate', [App\Http\Controllers\Admin\ImpersonationController::class, 'stop']);

    // Admin Routes
    Route::prefix('admin')->middleware(['admin'])->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'dashboard']);
        Route::get('dashboard/revenue-payout-chart', [App\Http\Controllers\Admin\AdminDashboardController::class, 'revenuePayoutChart']);

        // Settings Management
        Route::get('settings', [App\Http\Controllers\Admin\AdminController::class, 'get_settings']);
        Route::post('settings', [App\Http\Controllers\Admin\AdminController::class, 'update_settings']);

        // General Admin
        Route::get('profile', [App\Http\Controllers\Admin\AdminController::class, 'profile']);
        Route::post('update_profile', [App\Http\Controllers\Admin\AdminController::class, 'update_profile']);

        Route::get('advertisers/list', [App\Http\Controllers\Admin\AdminController::class, 'list_advertisers']);
        Route::get('advertisers/create', [App\Http\Controllers\Admin\AdminController::class, 'create_advertiser']);
        Route::post('advertisers/save', [App\Http\Controllers\Admin\AdminController::class, 'save_advertiser']);
        Route::get('advertisers/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'edit_advertiser']);
        Route::post('advertisers/update', [App\Http\Controllers\Admin\AdminController::class, 'update_advertiser']);
        Route::post('advertisers/{id}/status', [App\Http\Controllers\Admin\AdminController::class, 'update_advertiser_status']);
        Route::post('advertisers/{id}/notes-tags', [App\Http\Controllers\Admin\AdminController::class, 'update_advertiser_notes_tags']);
        Route::get('advertisers/{id}/offers', [App\Http\Controllers\Admin\AdminController::class, 'get_advertiser_offers']);
        Route::get('advertisers/{id}/stats', [App\Http\Controllers\Admin\AdminController::class, 'get_advertiser_stats']);
        Route::get('advertisers/{id}/invoices', [App\Http\Controllers\Admin\AdminController::class, 'get_advertiser_invoices']);
        Route::get('advertisers/export', [App\Http\Controllers\Admin\AdminController::class, 'export_advertisers']);

        Route::get('affiliates/list', [App\Http\Controllers\Admin\AdminController::class, 'list_affiliates']);
        Route::get('affiliates/create', [App\Http\Controllers\Admin\AdminController::class, 'create_affiliate']);
        Route::post('affiliates/save', [App\Http\Controllers\Admin\AdminController::class, 'save_affiliate']);
        Route::get('affiliates/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'edit_affiliate']);
        Route::post('affiliates/update', [App\Http\Controllers\Admin\AdminController::class, 'update_affiliate']);
        Route::post('affiliates/{id}/status', [App\Http\Controllers\Admin\AdminController::class, 'update_affiliate_status']);
        Route::post('affiliates/bulk-status', [App\Http\Controllers\Admin\AdminController::class, 'bulk_affiliates_status']);
        Route::delete('affiliates/{id}', [App\Http\Controllers\Admin\AdminController::class, 'delete_affiliate']);
        Route::post('affiliates/{id}/assign-manager', [App\Http\Controllers\Admin\AdminController::class, 'assign_affiliate_manager']);
        Route::get('affiliates/{id}/stats', [App\Http\Controllers\Admin\AdminController::class, 'get_affiliate_stats']);
        Route::post('affiliates/payout-override', [App\Http\Controllers\Admin\AdminController::class, 'save_payout_override']);
        Route::get('affiliates/{id}/referrals', [App\Http\Controllers\Admin\AdminController::class, 'get_affiliate_referrals']);
        Route::get('affiliates/export', [App\Http\Controllers\Admin\AdminController::class, 'export_affiliates']);
        Route::post('affiliates/upload-doc', [App\Http\Controllers\Admin\AdminController::class, 'upload_affiliate_doc']);

        Route::get('employees/list', [App\Http\Controllers\Admin\AdminController::class, 'list_employees']);
        Route::get('users/search', [App\Http\Controllers\Admin\AdminController::class, 'search_users']);
        Route::get('employees/create', [App\Http\Controllers\Admin\AdminController::class, 'create_employee']);
        Route::post('employees/save', [App\Http\Controllers\Admin\AdminController::class, 'save_employee']);
        Route::get('employees/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'edit_employee']);
        Route::post('employees/update', [App\Http\Controllers\Admin\AdminController::class, 'update_employee']);

        // MMP
        Route::get('mmp/platforms', [App\Http\Controllers\Admin\MMPlatformsController::class, 'list_platforms']);
        Route::get('mmp/setup/{id}', [App\Http\Controllers\Admin\MMPlatformsController::class, 'setup_platform']);

        // Offers
        Route::get('offer/index', [App\Http\Controllers\Admin\OfferController::class, 'index']);
        Route::get('offer/create', [App\Http\Controllers\Admin\OfferController::class, 'create']);
        Route::post('offer/save', [App\Http\Controllers\Admin\OfferController::class, 'save']);

        Route::post('offer/update-mmp-details', [App\Http\Controllers\Admin\OfferController::class, 'update_mmp_details']);
        Route::get('offer/edit/{id}', [App\Http\Controllers\Admin\OfferController::class, 'edit']);
        Route::post('offer/update_offer', [App\Http\Controllers\Admin\OfferController::class, 'update_offer']);
        Route::delete('offer/delete/{id}', [App\Http\Controllers\Admin\OfferController::class, 'delete_offer']);
        Route::post('offer/restore/{id}', [App\Http\Controllers\Admin\OfferController::class, 'restore_offer']);
        Route::post('offer/clone/{id}', [App\Http\Controllers\Admin\OfferController::class, 'clone_offer']);
        /*
        Route::get('offer/approved', [App\Http\Controllers\Admin\OfferController::class, 'offers_approved']);
        Route::get('offer/pending', [App\Http\Controllers\Admin\OfferController::class, 'offers_pending']);
        Route::get('offer/ended', [App\Http\Controllers\Admin\OfferController::class, 'offers_ended']);
        Route::get('offer/deleted', [App\Http\Controllers\Admin\OfferController::class, 'offers_deleted']);
        */

        Route::post('offer/update-targetting', [App\Http\Controllers\Admin\OfferController::class, 'update_targetting']);
        Route::post('offer/save-payout-rules', [App\Http\Controllers\Admin\OfferController::class, 'save_payout_rules']);
        Route::post('offer/stu', [App\Http\Controllers\Admin\OfferController::class, 'stu']); // Save Tracking URL

        Route::get('offer/show-events/{id}', [App\Http\Controllers\Admin\OfferController::class, 'show_events']);
        Route::get('offer/assign/{oid}/{aid}', [App\Http\Controllers\Admin\OfferController::class, 'assign']);
        Route::get('offer/reject/{oid}/{aid}', [App\Http\Controllers\Admin\OfferController::class, 'reject']);
        Route::get('offer/unassign/{oid}/{aid}', [App\Http\Controllers\Admin\OfferController::class, 'unassign']);

        Route::post('offer/save-event', [App\Http\Controllers\Admin\OfferController::class, 'save_event']);
        Route::get('offer/get-event/{eid}', [App\Http\Controllers\Admin\OfferController::class, 'get_event']);
        Route::get('offer/get-rule/{oid}/{eid}', [App\Http\Controllers\Admin\OfferController::class, 'get_rule']);
        Route::post('offer/update-event', [App\Http\Controllers\Admin\OfferController::class, 'update_event']);
        Route::post('offer/update-event-postback', [App\Http\Controllers\Admin\OfferController::class, 'update_event_postback']);
        Route::get('offer/remove-event/{oid}/{eid}', [App\Http\Controllers\Admin\OfferController::class, 'remove_event']);


        // Payments
        Route::get('payments/plan-details', [App\Http\Controllers\Admin\PaymentController::class, 'get_plan_details']);
        Route::get('payments/show-subscriptions', [App\Http\Controllers\Admin\PaymentController::class, 'show_subscriptions']);
        Route::get('payments/pause-subscription/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'pause_subscription']);
        Route::get('payments/resume-subscription/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'resume_subscription']);
        Route::get('payments/delete-subscription/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'delete_subscription']);

        // Reports
        Route::get('reports/transactions', [App\Http\Controllers\Admin\ReportController::class, 'list_transactions']);
        Route::get('reports/subPmtDetails/{sub_id}', [App\Http\Controllers\Admin\ReportController::class, 'sub_pmt_details']);
        Route::get('reports/logs', [App\Http\Controllers\Admin\ReportController::class, 'logs']);
        Route::get('reports/postback-logs', [App\Http\Controllers\Admin\ReportController::class, 'postback_logs']);
        Route::get('reports/all-offers', [App\Http\Controllers\Admin\ReportController::class, 'all_offers']);
        Route::get('reports/offer-clicks/{offer_id}', [App\Http\Controllers\Admin\ReportController::class, 'offer_clicks']);
        Route::post('reports/offer-clicks/search', [App\Http\Controllers\Admin\ReportController::class, 'search_offer_clicks']);
        Route::get('reports/devices', [App\Http\Controllers\Admin\ReportController::class, 'devices_list']);


        // Logs
        Route::get('logs/system_logs', [App\Http\Controllers\Admin\LogController::class, 'system_logs']);
        Route::get('logs/postback_logs', [App\Http\Controllers\Admin\LogController::class, 'postback_logs']);
        Route::get('logs/audit_logs', [App\Http\Controllers\Admin\LogController::class, 'audit_logs']);
        Route::get('logs/impersonation_logs', [App\Http\Controllers\Admin\LogController::class, 'impersonation_logs']);

        /*
        Route::get('reports/all-payouts', [App\Http\Controllers\Admin\ReportController::class, 'all_payouts']);
        Route::post('reports/all-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_all_payouts']);

        Route::get('reports/weekly-payouts/feature-status', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts_feature_status']);
        Route::get('reports/weekly-payouts', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts']);
        Route::get('reports/weekly-payouts/stats', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts_stats']);
        Route::post('reports/weekly-payouts/pay', [App\Http\Controllers\Admin\ReportController::class, 'pay_weekly_payouts']);
        Route::post('reports/weekly-payouts/reject', [App\Http\Controllers\Admin\ReportController::class, 'reject_weekly_payout']);
        Route::post('reports/weekly-payouts/manual', [App\Http\Controllers\Admin\ReportController::class, 'create_manual_weekly_payout']);

        Route::get('reports/offer-payouts/{oid}', [App\Http\Controllers\Admin\ReportController::class, 'offer_payouts']);
        Route::post('reports/offer-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_offer_payouts']);

        Route::get('reports/affiliate-payouts/{aid}', [App\Http\Controllers\Admin\ReportController::class, 'affiliate_payouts']);
        Route::post('reports/affiliate-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_affiliate_payouts']);
        */
        Route::get('reports/offers-assigned/{aid}', [App\Http\Controllers\Admin\ReportController::class, 'get_offers_assigned_to_affiliate']);
        Route::post('reports/click-conversion-report', [App\Http\Controllers\Admin\ReportController::class, 'click_conversion_report']);
        Route::post('reports/clicks', [App\Http\Controllers\Admin\ReportController::class, 'clicks_report']);
        Route::post('reports/conversions', [App\Http\Controllers\Admin\ReportController::class, 'conversions_report']);
        // New endpoints for UI enhancements
        Route::get('reports/conversions-dropdown-data', [App\Http\Controllers\Admin\ReportController::class, 'dropdown_data']);
        Route::get('reports/conversion-detail/{id}', [App\Http\Controllers\Admin\ReportController::class, 'conversion_detail']);
        Route::get('reports/click-detail/{id}', [App\Http\Controllers\Admin\ReportController::class, 'click_detail']);
        Route::get('reports/conversion/bulk-template', [App\Http\Controllers\Admin\ReportController::class, 'download_bulk_conversion_template']);
        Route::post('reports/conversion/update-status', [App\Http\Controllers\Admin\ReportController::class, 'update_conversion_status']);
        Route::post('reports/conversion/bulk-update-status', [App\Http\Controllers\Admin\ReportController::class, 'bulk_update_conversion_status']);

        // Integrations
        Route::get('integrations/config', [App\Http\Controllers\Admin\IntegrationController::class, 'get_config']);
        Route::post('integrations/save', [App\Http\Controllers\Admin\IntegrationController::class, 'save_config']);
        Route::post('integrations/sync', [App\Http\Controllers\Admin\IntegrationController::class, 'sync']);
        Route::post('integrations/upload-csv', [App\Http\Controllers\Admin\IntegrationController::class, 'upload_csv']);

        // Invoice Routes
        Route::get('invoices/stats', [\App\Http\Controllers\Admin\InvoiceController::class, 'stats']);
        Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class);
        Route::post('invoices/{id}/send', [\App\Http\Controllers\Admin\InvoiceController::class, 'send']);
        Route::get('invoices/{id}/download', [\App\Http\Controllers\Admin\InvoiceController::class, 'download'])->name('invoices.download');
        Route::post('invoices/{id}/pay', [\App\Http\Controllers\Admin\InvoiceController::class, 'pay']);
        Route::post('invoices/{id}/email', [\App\Http\Controllers\Admin\InvoiceController::class, 'email']);
        Route::post('invoices/{id}/revert-draft', [\App\Http\Controllers\Admin\InvoiceController::class, 'revertToDraft']);

        // Payouts

        Route::get('payouts/all-payouts', [App\Http\Controllers\Admin\ReportController::class, 'all_payouts']);
        Route::post('payouts/all-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_all_payouts']);

        Route::get('payouts/weekly-payouts/feature-status', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts_feature_status']);
        Route::get('payouts/weekly-payouts', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts']);
        Route::get('payouts/weekly-payouts/stats', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts_stats']);
        Route::post('payouts/weekly-payouts/pay', [App\Http\Controllers\Admin\ReportController::class, 'pay_weekly_payouts']);
        Route::post('payouts/weekly-payouts/reject', [App\Http\Controllers\Admin\ReportController::class, 'reject_weekly_payout']);
        Route::post('payouts/weekly-payouts/manual', [App\Http\Controllers\Admin\ReportController::class, 'create_manual_weekly_payout']);

        Route::get('payouts/offer-payouts/{oid}', [App\Http\Controllers\Admin\ReportController::class, 'offer_payouts']);
        Route::post('payouts/offer-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_offer_payouts']);

        Route::get('payouts/affiliate-payouts/{aid}', [App\Http\Controllers\Admin\ReportController::class, 'affiliate_payouts']);
        Route::post('payouts/affiliate-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_affiliate_payouts']);


        // Report Export Routes (Async)
        Route::get('reports/exports', [\App\Http\Controllers\Admin\ReportExportController::class, 'index']);
        Route::post('reports/export', [\App\Http\Controllers\Admin\ReportExportController::class, 'export']);
        Route::get('reports/export/{id}/status', [\App\Http\Controllers\Admin\ReportExportController::class, 'status']);
        Route::get('reports/export/{id}/download', [\App\Http\Controllers\Admin\ReportExportController::class, 'download']);

        // ─── Redirect Checker ─────────────────────────────
        Route::get('redirect-checker/devices', [App\Http\Controllers\Admin\RedirectCheckerController::class, 'devices']);
        Route::get('redirect-checker', [App\Http\Controllers\Admin\RedirectCheckerController::class, 'index']);
        Route::post('redirect-checker/check', [App\Http\Controllers\Admin\RedirectCheckerController::class, 'check']);
        Route::post('redirect-checker/test-proxy', [App\Http\Controllers\Admin\RedirectCheckerController::class, 'testProxy']);
        Route::get('redirect-checker/{id}', [App\Http\Controllers\Admin\RedirectCheckerController::class, 'show']);
        Route::delete('redirect-checker/{id}', [App\Http\Controllers\Admin\RedirectCheckerController::class, 'destroy']);

        // ─── Role Management ──────────────────────────────
        Route::get('roles', [App\Http\Controllers\Admin\RoleController::class, 'index']);
        Route::post('roles', [App\Http\Controllers\Admin\RoleController::class, 'store']);
        Route::get('roles/{id}', [App\Http\Controllers\Admin\RoleController::class, 'show']);
        Route::put('roles/{id}', [App\Http\Controllers\Admin\RoleController::class, 'update']);
        Route::delete('roles/{id}', [App\Http\Controllers\Admin\RoleController::class, 'destroy']);

        // ─── Designations ─────────────────────────────────
        Route::get('designations', [App\Http\Controllers\Admin\DesignationController::class, 'index']);
        Route::post('designations', [App\Http\Controllers\Admin\DesignationController::class, 'store']);
        Route::put('designations/{id}', [App\Http\Controllers\Admin\DesignationController::class, 'update']);
        Route::delete('designations/{id}', [App\Http\Controllers\Admin\DesignationController::class, 'destroy']);

        // ─── Categories ───────────────────────────────────
        Route::get('categories', [App\Http\Controllers\Admin\CategoryController::class, 'index']);
        Route::get('categories/form-data', [App\Http\Controllers\Admin\CategoryController::class, 'formData']);
        Route::get('categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'show']);
        Route::post('categories', [App\Http\Controllers\Admin\CategoryController::class, 'store']);
        Route::put('categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'update']);
        Route::patch('categories/{id}/toggle-status', [App\Http\Controllers\Admin\CategoryController::class, 'toggleStatus']);


        // ─── Modules (read-only) ──────────────────────────
        Route::get('modules', [App\Http\Controllers\Admin\ModuleController::class, 'index']);

        // ─── Impersonation ────────────────────────────────
        Route::post('impersonate/{user_id}', [App\Http\Controllers\Admin\ImpersonationController::class, 'start']);
        //Route::get('impersonation-logs', [App\Http\Controllers\Admin\ImpersonationController::class, 'logs']);

        // ─── Notifications: Sent management (admin only) ──
        Route::prefix('v1/notifications')->group(function () {
            Route::post('', [App\Http\Controllers\Api\V1\NotificationController::class, 'send']);
            Route::get('sent', [App\Http\Controllers\Api\V1\NotificationController::class, 'sentIndex']);
            Route::get('sent/{id}', [App\Http\Controllers\Api\V1\NotificationController::class, 'sentShow']);
            Route::put('sent/{id}', [App\Http\Controllers\Api\V1\NotificationController::class, 'sentUpdate']);
            Route::delete('sent/{id}', [App\Http\Controllers\Api\V1\NotificationController::class, 'sentDestroy']);
        });

        // ─── Postback Simulator Tool ───────────────────────────────────────────────
        Route::prefix('tools/postback-simulator')->group(function () {
            Route::get('recent-clicks', [App\Http\Controllers\Admin\PostbackSimulatorController::class, 'recentClicks']);
            Route::get('recent-conversions', [App\Http\Controllers\Admin\PostbackSimulatorController::class, 'recentConversions']);
            Route::post('fire-advertiser-postback', [App\Http\Controllers\Admin\PostbackSimulatorController::class, 'fireAdvertiserPostback']);
            Route::post('fire-affiliate-postback', [App\Http\Controllers\Admin\PostbackSimulatorController::class, 'fireAffiliatePostback']);
        });

        // ─── Fraud Detection ───────────────────────────────────────────────────────
        Route::prefix('fraud')->group(function () {
            // Dashboard & Reporting
            Route::get('dashboard', [App\Http\Controllers\Admin\FraudController::class, 'dashboard']);
            Route::get('alerts', [App\Http\Controllers\Admin\FraudController::class, 'alerts']);
            Route::get('click-scores', [App\Http\Controllers\Admin\FraudController::class, 'clickScores']);
            Route::post('bulk-block', [App\Http\Controllers\Admin\FraudController::class, 'bulkBlock']);

            // Rules
            Route::get('rules', [App\Http\Controllers\Admin\FraudController::class, 'listRules']);
            Route::get('rules/{id}', [App\Http\Controllers\Admin\FraudController::class, 'getRule']);
            Route::post('rules', [App\Http\Controllers\Admin\FraudController::class, 'createRule']);
            Route::put('rules/{id}', [App\Http\Controllers\Admin\FraudController::class, 'updateRule']);
            Route::delete('rules/{id}', [App\Http\Controllers\Admin\FraudController::class, 'deleteRule']);
            Route::patch('rules/{id}/toggle', [App\Http\Controllers\Admin\FraudController::class, 'toggleRule']);

            // IP Blacklist
            Route::get('blacklist/ip', [App\Http\Controllers\Admin\FraudController::class, 'listIpBlacklist']);
            Route::post('blacklist/ip', [App\Http\Controllers\Admin\FraudController::class, 'addIpBlacklist']);
            Route::post('blacklist/ip/import', [App\Http\Controllers\Admin\FraudController::class, 'importIpBlacklist']);
            Route::delete('blacklist/ip/{id}', [App\Http\Controllers\Admin\FraudController::class, 'removeIpBlacklist']);

            // IP Whitelist
            Route::get('whitelist/ip', [App\Http\Controllers\Admin\FraudController::class, 'listIpWhitelist']);
            Route::post('whitelist/ip', [App\Http\Controllers\Admin\FraudController::class, 'addIpWhitelist']);
            Route::delete('whitelist/ip/{id}', [App\Http\Controllers\Admin\FraudController::class, 'removeIpWhitelist']);

            // Device Blacklist
            Route::get('blacklist/device', [App\Http\Controllers\Admin\FraudController::class, 'listDeviceBlacklist']);
            Route::post('blacklist/device', [App\Http\Controllers\Admin\FraudController::class, 'addDeviceBlacklist']);
            Route::delete('blacklist/device/{id}', [App\Http\Controllers\Admin\FraudController::class, 'removeDeviceBlacklist']);

            // Third-Party Config
            Route::get('third-party-config', [App\Http\Controllers\Admin\FraudController::class, 'getThirdPartyConfig']);
            Route::put('third-party-config', [App\Http\Controllers\Admin\FraudController::class, 'updateThirdPartyConfig']);
        });

    });

    // Affiliate Routes
    Route::prefix('affiliate')->middleware(['affiliate'])->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Affiliate\AffiliateDashboardController::class, 'dashboard']);

        // Reports
        Route::middleware(['module.access:reports,can_view'])->group(function () {
            Route::get('reports/summary', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'summary']);
            Route::get('reports/offer-clicks/{offer_id}', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'offer_clicks']);
            Route::post('reports/offer-clicks/search', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'search_offer_clicks']);
            Route::get('reports/click-conversion-report', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'click_conversion_report']);
            Route::get('reports/offer-payouts/{oid}', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'offer_payouts']);
            Route::post('reports/offer-payouts/search', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'search_offer_payouts']);
            Route::get('reports/payouts', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'payouts']);
            Route::post('reports/payouts/search', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'search_affiliate_payouts']);
            Route::post('reports/clicks', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'clicks_report']);
            Route::post('reports/conversions', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'conversions_report']);
            Route::get('reports/click-detail/{id}', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'click_detail']);
        });

        // Offers
        Route::middleware(['module.access:offers,can_view'])->group(function () {
            Route::get('offers/browse', [App\Http\Controllers\Affiliate\AffiliateOfferController::class, 'browse']);
            Route::post('offers/request/{oid}', [App\Http\Controllers\Affiliate\AffiliateOfferController::class, 'request_access']);
            Route::get('offers/approved', [App\Http\Controllers\Affiliate\AffiliateOfferController::class, 'offers_approved']);
            Route::get('offers/tracking-url/{oid}', [App\Http\Controllers\Affiliate\AffiliateOfferController::class, 'tracking_url']);
        });

        // Profile
        Route::get('profile', [App\Http\Controllers\Affiliate\AffiliateController::class, 'profile']);
        Route::post('profile', [App\Http\Controllers\Affiliate\AffiliateController::class, 'update_profile']);
        Route::get('postback-cols', [App\Http\Controllers\Affiliate\AffiliateController::class, 'a_postback_cols']);
        Route::post('postback-cols', [App\Http\Controllers\Affiliate\AffiliateController::class, 'update_a_postback_cols']);
    });

    // Advertiser Routes
    Route::prefix('advertiser')->middleware(['advertiser'])->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Advertiser\AdvertiserDashboardController::class, 'dashboard']);

        // Profile
        Route::get('profile', [App\Http\Controllers\Advertiser\AdvertiserController::class, 'profile']);
        Route::post('update_profile', [App\Http\Controllers\Advertiser\AdvertiserController::class, 'update_profile']);

        // Offers
        Route::middleware(['module.access:offers,can_view'])->group(function () {
            Route::get('offers/my', [App\Http\Controllers\Advertiser\AdvertiserOfferController::class, 'my_offers']);
            Route::get('offers/approved', [App\Http\Controllers\Advertiser\AdvertiserOfferController::class, 'approved_offers']);
        });

        // Reports
        Route::middleware(['module.access:reports,can_view'])->group(function () {
            Route::get('reports/summary', [App\Http\Controllers\Advertiser\AdvertiserReportController::class, 'summary']);
            Route::get('reports/clicks-conversions', [App\Http\Controllers\Advertiser\AdvertiserReportController::class, 'clickConversionReport']);
            Route::post('reports/clicks', [App\Http\Controllers\Advertiser\AdvertiserReportController::class, 'clicks_report']);
            Route::post('reports/conversions', [App\Http\Controllers\Advertiser\AdvertiserReportController::class, 'conversions_report']);
            Route::get('reports/click-detail/{id}', [App\Http\Controllers\Advertiser\AdvertiserReportController::class, 'click_detail']);
        });

        // Invoices
        Route::middleware(['module.access:invoices,can_view'])->group(function () {
            Route::get('invoices', [App\Http\Controllers\Advertiser\AdvertiserReportController::class, 'invoices']);
            Route::get('invoices/{id}/download', [App\Http\Controllers\Advertiser\AdvertiserReportController::class, 'downloadInvoice']);
        });
    });

    // Employee Routes
    Route::prefix('employee')->middleware(['employee'])->group(function () {
        // ── Profile (no module gate) ──
        Route::get('profile', [App\Http\Controllers\Employee\EmployeeController::class, 'profile']);
        Route::post('update_profile', [App\Http\Controllers\Employee\EmployeeController::class, 'update_profile']);

        // ── Dashboard ──
        Route::get('dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'dashboard']);

        // ── Affiliates ──
        Route::middleware(['module.access:affiliates,can_view'])->group(function () {
            Route::get('affiliates/list', [App\Http\Controllers\Admin\AdminController::class, 'list_affiliates']);
            Route::get('affiliates/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'edit_affiliate']);
            Route::get('affiliates/{id}/stats', [App\Http\Controllers\Admin\AdminController::class, 'get_affiliate_stats']);
            Route::get('affiliates/{id}/referrals', [App\Http\Controllers\Admin\AdminController::class, 'get_affiliate_referrals']);
            Route::get('affiliates/export', [App\Http\Controllers\Admin\AdminController::class, 'export_affiliates']);
            Route::get('employees/list', [App\Http\Controllers\Admin\AdminController::class, 'list_employees']);
            Route::get('reports/offers-assigned/{aid}', [App\Http\Controllers\Admin\ReportController::class, 'get_offers_assigned_to_affiliate']);
            Route::get('reports/affiliate-payouts/{aid}', [App\Http\Controllers\Admin\ReportController::class, 'affiliate_payouts']);
        });
        Route::middleware(['module.access:affiliates,can_create'])->group(function () {
            Route::get('affiliates/create', [App\Http\Controllers\Admin\AdminController::class, 'create_affiliate']);
            Route::post('affiliates/save', [App\Http\Controllers\Admin\AdminController::class, 'save_affiliate']);
        });
        Route::middleware(['module.access:affiliates,can_edit'])->group(function () {
            Route::post('affiliates/update', [App\Http\Controllers\Admin\AdminController::class, 'update_affiliate']);
            Route::post('affiliates/{id}/status', [App\Http\Controllers\Admin\AdminController::class, 'update_affiliate_status']);
            Route::post('affiliates/bulk-status', [App\Http\Controllers\Admin\AdminController::class, 'bulk_affiliates_status']);
            Route::delete('affiliates/{id}', [App\Http\Controllers\Admin\AdminController::class, 'delete_affiliate']);
            Route::post('affiliates/{id}/assign-manager', [App\Http\Controllers\Admin\AdminController::class, 'assign_affiliate_manager']);
            Route::post('affiliates/payout-override', [App\Http\Controllers\Admin\AdminController::class, 'save_payout_override']);
            Route::post('affiliates/upload-doc', [App\Http\Controllers\Admin\AdminController::class, 'upload_affiliate_doc']);
        });

        // ── Advertisers ──
        Route::middleware(['module.access:advertisers,can_view'])->group(function () {
            Route::get('advertisers/list', [App\Http\Controllers\Admin\AdminController::class, 'list_advertisers']);
            Route::get('advertisers/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'edit_advertiser']);
            Route::get('advertisers/{id}/offers', [App\Http\Controllers\Admin\AdminController::class, 'get_advertiser_offers']);
            Route::get('advertisers/{id}/stats', [App\Http\Controllers\Admin\AdminController::class, 'get_advertiser_stats']);
            Route::get('advertisers/{id}/invoices', [App\Http\Controllers\Admin\AdminController::class, 'get_advertiser_invoices']);
            Route::get('advertisers/export', [App\Http\Controllers\Admin\AdminController::class, 'export_advertisers']);
        });
        Route::middleware(['module.access:advertisers,can_create'])->group(function () {
            Route::get('advertisers/create', [App\Http\Controllers\Admin\AdminController::class, 'create_advertiser']);
            Route::post('advertisers/save', [App\Http\Controllers\Admin\AdminController::class, 'save_advertiser']);
        });
        Route::middleware(['module.access:advertisers,can_edit'])->group(function () {
            Route::post('advertisers/update', [App\Http\Controllers\Admin\AdminController::class, 'update_advertiser']);
            Route::post('advertisers/{id}/status', [App\Http\Controllers\Admin\AdminController::class, 'update_advertiser_status']);
            Route::post('advertisers/{id}/notes-tags', [App\Http\Controllers\Admin\AdminController::class, 'update_advertiser_notes_tags']);
        });

        // ── Employees ──
        Route::middleware(['module.access:role_management,can_view'])->group(function () {
            Route::get('employees/list', [App\Http\Controllers\Admin\AdminController::class, 'list_employees']);
            Route::get('employees/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'edit_employee']);
        });
        Route::middleware(['module.access:role_management,can_create'])->group(function () {
            Route::get('employees/create', [App\Http\Controllers\Admin\AdminController::class, 'create_employee']);
            Route::post('employees/save', [App\Http\Controllers\Admin\AdminController::class, 'save_employee']);
        });
        Route::middleware(['module.access:role_management,can_edit'])->group(function () {
            Route::post('employees/update', [App\Http\Controllers\Admin\AdminController::class, 'update_employee']);
        });

        // ── Offers ──
        Route::middleware(['module.access:offers,can_view'])->group(function () {
            Route::get('offer/index', [App\Http\Controllers\Admin\OfferController::class, 'index']);
            //Route::get('offer/approved', [App\Http\Controllers\Admin\OfferController::class, 'offers_approved']);
            //Route::get('offer/pending', [App\Http\Controllers\Admin\OfferController::class, 'offers_pending']);
            //Route::get('offer/ended', [App\Http\Controllers\Admin\OfferController::class, 'offers_ended']);
            Route::get('offer/edit/{id}', [App\Http\Controllers\Admin\OfferController::class, 'edit']);
            Route::get('offer/create', [App\Http\Controllers\Admin\OfferController::class, 'create']);
            Route::get('offer/show-events/{id}', [App\Http\Controllers\Admin\OfferController::class, 'show_events']);
            Route::get('offer/get-event/{eid}', [App\Http\Controllers\Admin\OfferController::class, 'get_event']);
            Route::get('offer/get-rule/{oid}/{eid}', [App\Http\Controllers\Admin\OfferController::class, 'get_rule']);
        });
        Route::middleware(['module.access:offers,can_create'])->group(function () {
            Route::post('offer/save', [App\Http\Controllers\Admin\OfferController::class, 'save']);
        });
        Route::middleware(['module.access:offers,can_edit'])->group(function () {
            Route::post('offer/update_offer', [App\Http\Controllers\Admin\OfferController::class, 'update_offer']);
            Route::post('offer/update-mmp-details', [App\Http\Controllers\Admin\OfferController::class, 'update_mmp_details']);
            Route::post('offer/update-targetting', [App\Http\Controllers\Admin\OfferController::class, 'update_targetting']);
            Route::post('offer/save-payout-rules', [App\Http\Controllers\Admin\OfferController::class, 'save_payout_rules']);
            Route::post('offer/stu', [App\Http\Controllers\Admin\OfferController::class, 'stu']);
            Route::post('offer/save-event', [App\Http\Controllers\Admin\OfferController::class, 'save_event']);
            Route::post('offer/update-event', [App\Http\Controllers\Admin\OfferController::class, 'update_event']);
            Route::post('offer/update-event-postback', [App\Http\Controllers\Admin\OfferController::class, 'update_event_postback']);
            Route::get('offer/assign/{oid}/{aid}', [App\Http\Controllers\Admin\OfferController::class, 'assign']);
            Route::get('offer/reject/{oid}/{aid}', [App\Http\Controllers\Admin\OfferController::class, 'reject']);
            Route::get('offer/unassign/{oid}/{aid}', [App\Http\Controllers\Admin\OfferController::class, 'unassign']);
            Route::get('offer/remove-event/{oid}/{eid}', [App\Http\Controllers\Admin\OfferController::class, 'remove_event']);
        });
        Route::middleware(['module.access:offers,can_delete'])->group(function () {
            Route::delete('offer/delete/{id}', [App\Http\Controllers\Admin\OfferController::class, 'delete_offer']);
        });

        // ── Reports ──
        Route::middleware(['module.access:reports,can_view'])->group(function () {
            Route::get('reports/all-offers', [App\Http\Controllers\Admin\ReportController::class, 'all_offers']);
            Route::get('reports/offer-clicks/{offer_id}', [App\Http\Controllers\Admin\ReportController::class, 'offer_clicks']);
            Route::post('reports/offer-clicks/search', [App\Http\Controllers\Admin\ReportController::class, 'search_offer_clicks']);
            Route::get('reports/all-payouts', [App\Http\Controllers\Admin\ReportController::class, 'all_payouts']);
            Route::post('reports/all-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_all_payouts']);
            Route::get('reports/offer-payouts/{oid}', [App\Http\Controllers\Admin\ReportController::class, 'offer_payouts']);
            Route::post('reports/offer-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_offer_payouts']);
            Route::get('reports/affiliate-payouts/{aid}', [App\Http\Controllers\Admin\ReportController::class, 'affiliate_payouts']);
            Route::post('reports/affiliate-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_affiliate_payouts']);
            Route::get('reports/offers-assigned/{aid}', [App\Http\Controllers\Admin\ReportController::class, 'get_offers_assigned_to_affiliate']);
            Route::post('reports/click-conversion-report', [App\Http\Controllers\Admin\ReportController::class, 'click_conversion_report']);
            Route::post('reports/clicks', [App\Http\Controllers\Admin\ReportController::class, 'clicks_report']);
            Route::post('reports/conversions', [App\Http\Controllers\Admin\ReportController::class, 'conversions_report']);
            Route::get('reports/click-detail/{id}', [App\Http\Controllers\Admin\ReportController::class, 'click_detail']);
            Route::get('reports/conversion/bulk-template', [App\Http\Controllers\Admin\ReportController::class, 'download_bulk_conversion_template']);
            Route::get('reports/transactions', [App\Http\Controllers\Admin\ReportController::class, 'list_transactions']);
            Route::get('reports/logs', [App\Http\Controllers\Admin\ReportController::class, 'logs']);
            Route::get('reports/postback-logs', [App\Http\Controllers\Admin\ReportController::class, 'postback_logs']);
            // Report Exports
            Route::get('reports/exports', [\App\Http\Controllers\Admin\ReportExportController::class, 'index']);
            Route::post('reports/export', [\App\Http\Controllers\Admin\ReportExportController::class, 'export']);
            Route::get('reports/export/{id}/status', [\App\Http\Controllers\Admin\ReportExportController::class, 'status']);
            Route::get('reports/export/{id}/download', [\App\Http\Controllers\Admin\ReportExportController::class, 'download']);
        });

        // ── Invoices ──
        Route::middleware(['module.access:invoices,can_view'])->group(function () {
            Route::get('invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'index']);
            Route::get('invoices/stats', [\App\Http\Controllers\Admin\InvoiceController::class, 'stats']);
            Route::get('invoices/{id}', [\App\Http\Controllers\Admin\InvoiceController::class, 'show']);
            Route::get('invoices/{id}/download', [\App\Http\Controllers\Admin\InvoiceController::class, 'download']);
        });
        Route::middleware(['module.access:invoices,can_create'])->group(function () {
            Route::post('invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'store']);
        });
        Route::middleware(['module.access:invoices,can_edit'])->group(function () {
            Route::put('invoices/{id}', [\App\Http\Controllers\Admin\InvoiceController::class, 'update']);
            Route::post('invoices/{id}/send', [\App\Http\Controllers\Admin\InvoiceController::class, 'send']);
            Route::post('invoices/{id}/pay', [\App\Http\Controllers\Admin\InvoiceController::class, 'pay']);
            Route::post('invoices/{id}/email', [\App\Http\Controllers\Admin\InvoiceController::class, 'email']);
            Route::post('invoices/{id}/revert-draft', [\App\Http\Controllers\Admin\InvoiceController::class, 'revertToDraft']);
        });

        // ── Payouts ──
        Route::middleware(['module.access:payouts,can_view'])->group(function () {
            Route::get('payouts/all-payouts', [App\Http\Controllers\Admin\ReportController::class, 'all_payouts']);
            Route::post('payouts/all-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_all_payouts']);

            Route::get('payouts/weekly-payouts/feature-status', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts_feature_status']);
            Route::get('payouts/weekly-payouts', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts']);
            Route::get('payouts/weekly-payouts/stats', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts_stats']);
            Route::post('payouts/weekly-payouts/pay', [App\Http\Controllers\Admin\ReportController::class, 'pay_weekly_payouts']);
            Route::post('payouts/weekly-payouts/reject', [App\Http\Controllers\Admin\ReportController::class, 'reject_weekly_payout']);
            Route::post('payouts/weekly-payouts/manual', [App\Http\Controllers\Admin\ReportController::class, 'create_manual_weekly_payout']);

            Route::get('payouts/offer-payouts/{oid}', [App\Http\Controllers\Admin\ReportController::class, 'offer_payouts']);
            Route::post('payouts/offer-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_offer_payouts']);
            Route::get('payouts/affiliate-payouts/{aid}', [App\Http\Controllers\Admin\ReportController::class, 'affiliate_payouts']);
            Route::post('payouts/affiliate-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_affiliate_payouts']);
        });


        Route::middleware(['module.access:payouts,can_create'])->group(function () {
            Route::get('payouts/all-payouts', [App\Http\Controllers\Admin\ReportController::class, 'all_payouts']);
            Route::post('payouts/all-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_all_payouts']);

            Route::get('payouts/weekly-payouts/feature-status', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts_feature_status']);
            Route::get('payouts/weekly-payouts', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts']);
            Route::get('payouts/weekly-payouts/stats', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts_stats']);
            Route::post('payouts/weekly-payouts/pay', [App\Http\Controllers\Admin\ReportController::class, 'pay_weekly_payouts']);
            Route::post('payouts/weekly-payouts/reject', [App\Http\Controllers\Admin\ReportController::class, 'reject_weekly_payout']);
            Route::post('payouts/weekly-payouts/manual', [App\Http\Controllers\Admin\ReportController::class, 'create_manual_weekly_payout']);

            Route::get('payouts/offer-payouts/{oid}', [App\Http\Controllers\Admin\ReportController::class, 'offer_payouts']);
            Route::post('payouts/offer-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_offer_payouts']);
            Route::get('payouts/affiliate-payouts/{aid}', [App\Http\Controllers\Admin\ReportController::class, 'affiliate_payouts']);
            Route::post('payouts/affiliate-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_affiliate_payouts']);
        });
        Route::middleware(['module.access:payouts,can_edit'])->group(function () {
            Route::get('payouts/all-payouts', [App\Http\Controllers\Admin\ReportController::class, 'all_payouts']);
            Route::post('payouts/all-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_all_payouts']);

            Route::get('payouts/weekly-payouts/feature-status', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts_feature_status']);
            Route::get('payouts/weekly-payouts', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts']);
            Route::get('payouts/weekly-payouts/stats', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts_stats']);
            Route::post('payouts/weekly-payouts/pay', [App\Http\Controllers\Admin\ReportController::class, 'pay_weekly_payouts']);
            Route::post('payouts/weekly-payouts/reject', [App\Http\Controllers\Admin\ReportController::class, 'reject_weekly_payout']);
            Route::post('payouts/weekly-payouts/manual', [App\Http\Controllers\Admin\ReportController::class, 'create_manual_weekly_payout']);

            Route::get('payouts/offer-payouts/{oid}', [App\Http\Controllers\Admin\ReportController::class, 'offer_payouts']);
            Route::post('payouts/offer-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_offer_payouts']);
            Route::get('payouts/affiliate-payouts/{aid}', [App\Http\Controllers\Admin\ReportController::class, 'affiliate_payouts']);
            Route::post('payouts/affiliate-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_affiliate_payouts']);
        });
        Route::middleware(['module.access:payouts,can_delete'])->group(function () {
            Route::get('payouts/all-payouts', [App\Http\Controllers\Admin\ReportController::class, 'all_payouts']);
            Route::post('payouts/all-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_all_payouts']);

            Route::get('payouts/weekly-payouts/feature-status', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts_feature_status']);
            Route::get('payouts/weekly-payouts', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts']);
            Route::get('payouts/weekly-payouts/stats', [App\Http\Controllers\Admin\ReportController::class, 'weekly_payouts_stats']);
            Route::post('payouts/weekly-payouts/pay', [App\Http\Controllers\Admin\ReportController::class, 'pay_weekly_payouts']);
            Route::post('payouts/weekly-payouts/reject', [App\Http\Controllers\Admin\ReportController::class, 'reject_weekly_payout']);
            Route::post('payouts/weekly-payouts/manual', [App\Http\Controllers\Admin\ReportController::class, 'create_manual_weekly_payout']);


            Route::get('payouts/offer-payouts/{oid}', [App\Http\Controllers\Admin\ReportController::class, 'offer_payouts']);
            Route::post('payouts/offer-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_offer_payouts']);
            Route::get('payouts/affiliate-payouts/{aid}', [App\Http\Controllers\Admin\ReportController::class, 'affiliate_payouts']);
            Route::post('payouts/affiliate-payouts/search', [App\Http\Controllers\Admin\ReportController::class, 'search_affiliate_payouts']);
        });




        // ── Integrations ──
        Route::middleware(['module.access:integrations,can_view'])->group(function () {
            Route::get('integrations/config', [App\Http\Controllers\Admin\IntegrationController::class, 'get_config']);
            Route::get('mmp/platforms', [App\Http\Controllers\Admin\MMPlatformsController::class, 'list_platforms']);
            Route::get('mmp/setup/{id}', [App\Http\Controllers\Admin\MMPlatformsController::class, 'setup_platform']);
        });
        Route::middleware(['module.access:integrations,can_edit'])->group(function () {
            Route::post('integrations/save', [App\Http\Controllers\Admin\IntegrationController::class, 'save_config']);
            Route::post('integrations/sync', [App\Http\Controllers\Admin\IntegrationController::class, 'sync']);
            Route::post('integrations/upload-csv', [App\Http\Controllers\Admin\IntegrationController::class, 'upload_csv']);
        });

        // ── Role Management ──
        Route::middleware(['module.access:role_management,can_view'])->group(function () {
            Route::get('roles', [App\Http\Controllers\Admin\RoleController::class, 'index']);
            Route::get('roles/{id}', [App\Http\Controllers\Admin\RoleController::class, 'show']);
            Route::get('designations', [App\Http\Controllers\Admin\DesignationController::class, 'index']);
            Route::get('modules', [App\Http\Controllers\Admin\ModuleController::class, 'index']);
        });
        Route::middleware(['module.access:role_management,can_create'])->group(function () {
            Route::post('roles', [App\Http\Controllers\Admin\RoleController::class, 'store']);
            Route::post('designations', [App\Http\Controllers\Admin\DesignationController::class, 'store']);
        });
        Route::middleware(['module.access:role_management,can_edit'])->group(function () {
            Route::put('roles/{id}', [App\Http\Controllers\Admin\RoleController::class, 'update']);
            Route::put('designations/{id}', [App\Http\Controllers\Admin\DesignationController::class, 'update']);
        });
        Route::middleware(['module.access:role_management,can_delete'])->group(function () {
            Route::delete('roles/{id}', [App\Http\Controllers\Admin\RoleController::class, 'destroy']);
            Route::delete('designations/{id}', [App\Http\Controllers\Admin\DesignationController::class, 'destroy']);
        });

        // ── Impersonation ──
        Route::middleware(['module.access:impersonation,can_view'])->group(function () {
            Route::post('impersonate/{user_id}', [App\Http\Controllers\Admin\ImpersonationController::class, 'start']);
            Route::get('impersonation-logs', [App\Http\Controllers\Admin\ImpersonationController::class, 'logs']);
        });

        // ── Payments ──
        Route::middleware(['module.access:payments,can_view'])->group(function () {
            Route::get('payments/plan-details', [App\Http\Controllers\Admin\PaymentController::class, 'get_plan_details']);
            Route::get('payments/show-subscriptions', [App\Http\Controllers\Admin\PaymentController::class, 'show_subscriptions']);
        });

        // ── Conversions ──
        Route::middleware(['module.access:reports,can_edit'])->group(function () {
            Route::post('reports/conversion/update-status', [App\Http\Controllers\Admin\ReportController::class, 'update_conversion_status']);
            Route::post('reports/conversion/bulk-update-status', [App\Http\Controllers\Admin\ReportController::class, 'bulk_update_conversion_status']);
        });

        // ── Notifications ──
        Route::middleware(['module.access:role_management,can_create'])->group(function () {
            Route::prefix('v1/notifications')->group(function () {
                Route::post('', [App\Http\Controllers\Api\V1\NotificationController::class, 'send']);
                Route::get('sent', [App\Http\Controllers\Api\V1\NotificationController::class, 'sentIndex']);
                Route::get('sent/{id}', [App\Http\Controllers\Api\V1\NotificationController::class, 'sentShow']);
                Route::put('sent/{id}', [App\Http\Controllers\Api\V1\NotificationController::class, 'sentUpdate']);
                Route::delete('sent/{id}', [App\Http\Controllers\Api\V1\NotificationController::class, 'sentDestroy']);
            });
        });
    });

    // Tracking (Server-to-Server)

    // ─── Notifications: System triggers (protected by X-Notification-Secret header) ─
    // The controller's authoriseTrigger() validates the header value against
    // NOTIFICATION_TRIGGER_SECRET in .env. No additional role middleware is needed.
    Route::prefix('v1/notifications/triggers')->group(function () {
        Route::post('subscription-expiry', [App\Http\Controllers\Api\V1\NotificationController::class, 'triggerSubscriptionExpiry']);
        Route::post('payment-reminder', [App\Http\Controllers\Api\V1\NotificationController::class, 'triggerPaymentReminder']);
        Route::post('custom', [App\Http\Controllers\Api\V1\NotificationController::class, 'triggerCustom']);
    });

});

// Admin Dashboard Extra
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('admin/approve-assign-request/{oaid}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'approve_offer_assign_requests']);
    Route::get('admin/reject-assign-request/{oaid}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'reject_offer_assign_requests']);
    Route::get('admin/reports/top-offers/{dod}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'top_offers']);
    Route::get('admin/reports/top-affiliates/{dod}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'top_affiliates']);

    // ReportController Missing??
    Route::get('admin/reports/get-all-offer-clicks', [App\Http\Controllers\Admin\ReportController::class, 'all_offers']); // Mapping to all_offers as logic seems same or similar
});
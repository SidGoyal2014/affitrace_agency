<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication
Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login'])->middleware('api.tenant');

// Protected Routes
Route::middleware(['auth:sanctum', 'api.tenant'])->group(function () {

    Route::post('logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('refresh-token', [App\Http\Controllers\Api\AuthController::class, 'refreshToken']);
    Route::get('me', [App\Http\Controllers\Api\AuthController::class, 'me']);

    // Admin Routes
    Route::prefix('admin')->middleware(['admin'])->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'dashboard']);

        // General Admin
        Route::get('profile', [App\Http\Controllers\Admin\AdminController::class, 'profile']);
        Route::post('update_profile', [App\Http\Controllers\Admin\AdminController::class, 'update_profile']);

        Route::get('advertisers/list', [App\Http\Controllers\Admin\AdminController::class, 'list_advertisers']);
        Route::get('advertisers/create', [App\Http\Controllers\Admin\AdminController::class, 'create_advertiser']);
        Route::post('advertisers/save', [App\Http\Controllers\Admin\AdminController::class, 'save_advertiser']);
        Route::get('advertisers/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'edit_advertiser']);
        Route::post('advertisers/update', [App\Http\Controllers\Admin\AdminController::class, 'update_advertiser']);

        Route::get('affiliates/list', [App\Http\Controllers\Admin\AdminController::class, 'list_affiliates']);
        Route::get('affiliates/create', [App\Http\Controllers\Admin\AdminController::class, 'create_affiliate']);
        Route::post('affiliates/save', [App\Http\Controllers\Admin\AdminController::class, 'save_affiliate']);
        Route::get('affiliates/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'edit_affiliate']);
        Route::post('affiliates/update', [App\Http\Controllers\Admin\AdminController::class, 'update_affiliate']);

        // MMP
        Route::get('mmp/list', [App\Http\Controllers\Admin\MMPlatformsController::class, 'list_platforms']);
        Route::get('mmp/setup/{id}', [App\Http\Controllers\Admin\MMPlatformsController::class, 'setup_platform']);

        // Offers
        Route::get('offer/index', [App\Http\Controllers\Admin\OfferController::class, 'index']);
        Route::get('offer/create', [App\Http\Controllers\Admin\OfferController::class, 'create']);
        Route::post('offer/save', [App\Http\Controllers\Admin\OfferController::class, 'save']);
        Route::get('offer/edit/{id}', [App\Http\Controllers\Admin\OfferController::class, 'edit']);
        Route::post('offer/update_offer', [App\Http\Controllers\Admin\OfferController::class, 'update_offer']);
        Route::delete('offer/delete/{id}', [App\Http\Controllers\Admin\OfferController::class, 'delete_offer']);
        Route::post('offer/restore/{id}', [App\Http\Controllers\Admin\OfferController::class, 'restore_offer']);
        Route::get('offer/approved', [App\Http\Controllers\Admin\OfferController::class, 'offers_approved']);
        Route::get('offer/pending', [App\Http\Controllers\Admin\OfferController::class, 'offers_pending']);
        Route::get('offer/ended', [App\Http\Controllers\Admin\OfferController::class, 'offers_ended']);
        Route::get('offer/deleted', [App\Http\Controllers\Admin\OfferController::class, 'offers_deleted']);

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
        Route::get('payments/plan-details', [App\Http\Controllers\Admin\PaymentController::class, 'plan_details']);
        Route::get('payments/show-subscriptions', [App\Http\Controllers\Admin\PaymentController::class, 'show_subscriptions']);
        Route::get('payments/pause-subscription/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'pause_subscription']);
        Route::get('payments/resume-subscription/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'resume_subscription']);
        Route::get('payments/delete-subscription/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'delete_subscription']);

        // Reports
        Route::get('reports/transactions', [App\Http\Controllers\Admin\ReportController::class, 'list_transactions']);
        Route::get('reports/logs', [App\Http\Controllers\Admin\ReportController::class, 'logs']);
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
    });

    // Affiliate Routes
    Route::prefix('affiliate')->middleware(['affiliate'])->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Affiliate\AffiliateDashboardController::class, 'dashboard']);
        Route::get('reports/summary', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'summary']);
        Route::get('reports/offer-clicks/{offer_id}', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'offer_clicks']);
        Route::post('reports/offer-clicks/search', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'search_offer_clicks']);
        Route::get('reports/click-conversion-report', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'click_conversion_report']);

        Route::get('reports/offer-payouts/{oid}', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'offer_payouts']);
        Route::post('reports/offer-payouts/search', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'search_offer_payouts']);
        Route::get('reports/payouts', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'payouts']);
        Route::post('reports/payouts/search', [App\Http\Controllers\Affiliate\AffiliateReportController::class, 'search_affiliate_payouts']);

        // Offers
        Route::get('offers/approved', [App\Http\Controllers\Affiliate\AffiliateOfferController::class, 'offers_approved']);
        Route::get('offers/tracking-url/{oid}', [App\Http\Controllers\Affiliate\AffiliateOfferController::class, 'tracking_url']);

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
        Route::get('offers/my', [App\Http\Controllers\Advertiser\AdvertiserOfferController::class, 'my_offers']);
        Route::get('offers/approved', [App\Http\Controllers\Advertiser\AdvertiserOfferController::class, 'approved_offers']);

        // Reports
        Route::get('reports/summary', [App\Http\Controllers\Advertiser\AdvertiserReportController::class, 'summary']);
        Route::get('invoices', [App\Http\Controllers\Advertiser\AdvertiserReportController::class, 'invoices']);
    });

    // Employee Routes
    Route::prefix('employee')->middleware(['employee'])->group(function () {
        Route::get('profile', [App\Http\Controllers\Employee\EmployeeController::class, 'profile']);
        Route::post('update_profile', [App\Http\Controllers\Employee\EmployeeController::class, 'update_profile']);
    });

    // Tracking (Server-to-Server)
    Route::post('offer/sed/{oid}/{eid}', [App\Http\Controllers\TrackingController::class, 'sed']);
    Route::get('offer/sd', [App\Http\Controllers\TrackingController::class, 'sd']);

});

// Admin Dashboard Extra
Route::middleware(['auth:sanctum', 'api.tenant', 'admin'])->group(function () {
    Route::get('admin/reject-assign-request/{oaid}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'reject_offer_assign_requests']);
    Route::get('admin/reports/top-offers/{dod}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'top_offers']);
    Route::get('admin/reports/top-affiliates/{dod}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'top_affiliates']);

    // ReportController Missing??
    Route::get('admin/reports/get-all-offer-clicks', [App\Http\Controllers\Admin\ReportController::class, 'all_offers']); // Mapping to all_offers as logic seems same or similar
});
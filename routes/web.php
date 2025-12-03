<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\LoginController;

use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\TrackingController;

use App\Http\Controllers\MailController;
use App\Http\Controllers\ForgotPasswordController;

use App\Http\Middleware\TenantSwitchingMiddleware;


use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\MMPlatformsController;
use App\Http\Controllers\Admin\PaymentController;



use App\Http\Controllers\Affiliate\AffiliateController;
use App\Http\Controllers\Affiliate\AffiliateDashboardController;
use App\Http\Controllers\Affiliate\AffiliateOfferController;
use App\Http\Controllers\Affiliate\AffiliateReportController;


use App\Http\Controllers\Advertiser\AdvertiserController;

use App\Http\Controllers\Employee\EmployeeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('register/{eai}', [LoginController::class, 'registerform']);
Route::post('doRegister', [LoginController::class, 'doRegister']);

Route::get('login/{eai}', [LoginController::class, 'loginform']);
Route::post('/doLogin', [LoginController::class, 'doLogin'])->middleware('tenant.switch');
Route::get('logout', [LoginController::class, 'logout']);


Route::get('contact', [WebsiteController::class, 'contact']);

Route::post('contact_email', [MailController::class, 'contact_email']);

Route::get('forget-password/{eai}', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');

Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');

Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');

Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');




Route::get('offer/gourl/{oid}/{aid}', [TrackingController::class, 'gourl']);
Route::get('offer/sd', [TrackingController::class, 'sd']);




/*
Route::middleware(['cache.page'])->group(function () {

	Route::get('offer/gourl/{oid}/{aid}', [TrackingController::class, 'gourl']);
	Route::get('offer/sd', [TrackingController::class, 'sd']);

});
*/

Route::post('offer/sed/{oid}/{eid}', [TrackingController::class, 'sed']);

Route::get('transfer_data', [AffiliateReportController::class, 'transfer_data']);


Route::get('invalid_country', [WebsiteController::class, 'invalid_country'])->name('ic');
Route::get('verify', [WebsiteController::class, 'verify']);


Route::group([
		'namespace' => 'Admin',
		'prefix'     => 'admin',
		'middleware' => ['auth', 'admin'],
	], function () {

		Route::get('dashboard', [AdminDashboardController::class, 'dashboard']);
		
		Route::get('reject_offer_assign_requests/{oaid}', [AdminDashboardController::class, 'reject_offer_assign_requests']);

		Route::get('profile', [AdminController::class, 'profile']);
		Route::post('update_profile', [AdminController::class, 'update_profile']);
		

		Route::get('list_affiliates', [AdminController::class, 'list_affiliates']);

		Route::get('create_affiliate', [AdminController::class, 'create_affiliate']);
		Route::post('save_affiliate', [AdminController::class, 'save_affiliate']);

		Route::get('edit_affiliate/{id}', [AdminController::class, 'edit_affiliate']);
		Route::post('update_affiliate', [AdminController::class, 'update_affiliate']);



		Route::get('list_advertisers', [AdminController::class, 'list_advertisers']);

		Route::get('create_advertiser', [AdminController::class, 'create_advertiser']);
		Route::post('save_advertiser', [AdminController::class, 'save_advertiser']);

		Route::get('edit_advertiser/{id}', [AdminController::class, 'edit_advertiser']);
		Route::post('update_advertiser', [AdminController::class, 'update_advertiser']);

		Route::get('offer/create', [OfferController::class, 'create']);
		Route::post('offer/save', [OfferController::class, 'save']);

		Route::get('offer/delete_offer/{id}', [OfferController::class, 'delete_offer']);
		Route::get('offer/restore_offer/{id}', [OfferController::class, 'restore_offer']);

		Route::get('offer/edit/{id}', [OfferController::class, 'edit']);
		Route::post('offer/update_offer', [OfferController::class, 'update_offer']);
		Route::post('offer/update_targetting', [OfferController::class, 'update_targetting']);

		Route::get('offer/show_events/{id}', [OfferController::class, 'show_events']);

		Route::post('offer/events/save', [OfferController::class, 'save_event']);

		Route::get('offer/events/get_event/{eid}', [OfferController::class, 'get_event']);

		Route::post('offer/events/update', [OfferController::class, 'update_event']);

		Route::post('offer/events/update_event_postback', [OfferController::class, 'update_event_postback']);

		Route::get('offer/events/remove/{oid}/{eid}', [OfferController::class, 'remove_event']);

		Route::post('offer/events/save_payout_rules', [OfferController::class, 'save_payout_rules']);

		Route::get('offer/events/get_rule/{oid}/{eid}', [OfferController::class, 'get_rule']);

		//Route::post('offer/updateTrackingURL', [OfferController::class, 'updateTrackingURL']);
		
		// MMP = Mobile marketing Platforms
		Route::get('mmp/list_platforms', [MMPlatformsController::class, 'list_platforms']);
		Route::get('mmp/setup_platform/{id}', [MMPlatformsController::class, 'setup_platform']);


		//stu = Save Tracking Url
		Route::post('offer/stu', [OfferController::class, 'stu']);

		Route::get('offer/assign/{oid}/{aid}', [OfferController::class, 'assign']);
		Route::get('offer/reject/{oid}/{aid}', [OfferController::class, 'reject']);
		Route::get('offer/unassign/{oid}/{aid}', [OfferController::class, 'unassign']);

		Route::get('offer/offers_approved', [OfferController::class, 'offers_approved']);
		Route::get('offer/offers_pending', [OfferController::class, 'offers_pending']);
		Route::get('offer/offers_ended', [OfferController::class, 'offers_ended']);
		Route::get('offer/offers_deleted', [OfferController::class, 'offers_deleted']);

		/*Route::get('reports/logs', [ReportController::class, 'logs']);*/

		// Reports section routes

		Route::get('reports/all_offers', [ReportController::class, 'all_offers']);

		Route::get('reports/getAllOfferClicks', [ReportController::class, 'getAllOfferClicks'])->name('getAllOfferClicks');
		
		Route::get('reports/offer_clicks/{offer_id}/{get_conversions?}', [ReportController::class, 'offer_clicks'])->name('offer_clicks');

		Route::post('reports/search_offer_clicks', [ReportController::class, 'search_offer_clicks']);

		Route::get('reports/all_payouts', [ReportController::class, 'all_payouts']);
		Route::post('reports/search_all_payouts', [ReportController::class, 'search_all_payouts']);

		Route::get('reports/offer_payouts/{oid}', [ReportController::class, 'offer_payouts']);
		Route::post('reports/search_offer_payouts', [ReportController::class, 'search_offer_payouts']);

		//Route::get('reports/affiliate_payouts/{oid}/{aid}', [ReportController::class, 'affiliate_payouts']);
		Route::get('reports/affiliate_payouts/{aid}', [ReportController::class, 'affiliate_payouts']);
		Route::post('reports/search_affiliate_payouts', [ReportController::class, 'search_affiliate_payouts']);


		Route::get('reports/get_offers_assigned_to_affiliate/{aid}', [ReportController::class, 'get_offers_assigned_to_affiliate']);

		Route::get('reports/list_transactions', [ReportController::class, 'list_transactions']);

		// Dashboard section routes
		Route::get('reports/top_offers/{dod}', [DashboardController::class, 'top_offers']);

		Route::get('reports/top_affiliates/{dod}', [DashboardController::class, 'top_affiliates']);

		// Payments section routes
		
		/*
		Route::get('payments/confirm_password', [PaymentController::class, 'confirm_password']);
		Route::post('payments/do_confirm_password', [PaymentController::class, 'do_confirm_password']);
		*/

		Route::get('payments/plan_details', [PaymentController::class, 'plan_details']);
		Route::get('payments/show_subscriptions', [PaymentController::class, 'show_subscriptions']);

		Route::get('payments/pause_subscription/{id}', [PaymentController::class, 'pause_subscription']);
		Route::get('payments/resume_subscription/{id}', [PaymentController::class, 'resume_subscription']);
		Route::get('payments/delete_subscription/{id}', [PaymentController::class, 'delete_subscription']);


	}

);
	



Route::group([
		'namespace' => 'Affiliate',
		'prefix'     => 'affiliate',
		'middleware' => ['auth', 'affiliate'],
	], function () {

		Route::get('dashboard', [AffiliateDashboardController::class, 'dashboard']);
		Route::get('send_offer_assign_request/{oid}', [AffiliateDashboardController::class, 'send_offer_assign_request']);



		Route::get('profile', [AffiliateController::class, 'profile']);
		Route::post('update_profile', [AffiliateController::class, 'update_profile']);


		Route::get('a_postback_cols', [AffiliateController::class, 'a_postback_cols']);
		Route::post('update_a_postback_cols', [AffiliateController::class, 'update_a_postback_cols']);


		Route::get('offer/offers_approved', [AffiliateOfferController::class, 'offers_approved']);
		Route::get('offer/tracking_url/{oid}', [AffiliateOfferController::class, 'tracking_url']);

		Route::get('reports/summary', [AffiliateReportController::class, 'summary']);


		Route::get('reports/offer_clicks/{offer_id}/{get_conversions?}', [AffiliateReportController::class, 'offer_clicks'])->name('affiliate_offer_clicks');

		//Route::get('reports/offer_clicks/{offer_id}/{get_conversions?}', [AffiliateReportController::class, 'offer_clicks']);


		Route::post('reports/search_offer_clicks', [AffiliateReportController::class, 'search_offer_clicks']);


		//Route::get('reports/get_json', [AffiliateReportController::class, 'get_json']);
		Route::get('reports/get_json/{aid?}/{ft?}', [AffiliateReportController::class, 'get_json']);

		/***/
		Route::get('reports/offer_payouts/{oid}', [AffiliateReportController::class, 'offer_payouts']);
		Route::post('reports/search_offer_payouts', [AffiliateReportController::class, 'search_offer_payouts']);

		Route::get('reports/payouts', [AffiliateReportController::class, 'payouts']);
		Route::post('reports/search_affiliate_payouts', [AffiliateReportController::class, 'search_affiliate_payouts']);



		/***/


	}

);





Route::group([
		'prefix'     => 'advertiser',
		'middleware' => ['auth', 'advertiser'],
	], function () {

		Route::get('profile', [AdvertiserController::class, 'profile']);
		Route::post('update_profile', [AdvertiserController::class, 'update_profile']);

	}

);


Route::group([
		'prefix'     => 'employee',
		'middleware' => ['auth', 'employee'],
	], function () {

		Route::get('profile', [EmployeeController::class, 'profile']);
		Route::post('update_profile', [EmployeeController::class, 'update_profile']);

	}

);
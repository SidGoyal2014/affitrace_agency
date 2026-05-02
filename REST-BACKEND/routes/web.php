<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\WebsiteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| These routes are for browser-facing functionality like redirects
| and verification pages. All API data logic has been moved to api.php.
|
*/

Route::get('/', function () {
	return view('welcome');
});

// Tracking Redirects
Route::middleware('api.tenant')->group(function () {
	Route::get('offer/gourl/{oid}/{aid}', [TrackingController::class, 'gourl']);
});

// Verification / Error Pages
Route::get('invalid_country', [WebsiteController::class, 'invalid_country'])->name('ic');
Route::get('verify', [WebsiteController::class, 'verify']);

// Cap Reached Pages
Route::get('cap/clicks-reached', [WebsiteController::class, 'clicks_cap_reached'])->name('cap.clicks.reached');
Route::get('cap/conversions-reached', [WebsiteController::class, 'conversions_cap_reached'])->name('cap.conversions.reached');

// Fallback for non-existent web routes?
// Route::fallback(function () {
//     return response()->json(['message' => 'Not Found'], 404);
// });
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
Route::get('offer/gourl/{oid}/{aid}', [TrackingController::class, 'gourl']);

// Verification / Error Pages
Route::get('invalid_country', [WebsiteController::class, 'invalid_country'])->name('ic');
Route::get('verify', [WebsiteController::class, 'verify']);

// Fallback for non-existent web routes?
// Route::fallback(function () {
//     return response()->json(['message' => 'Not Found'], 404);
// });
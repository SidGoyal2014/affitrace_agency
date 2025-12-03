<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


/**
# IMPORTANT COMMENT:
1. This route method are called from main website to get user token
*/

Route::post('userlogin', [UserController::class, 'userlogin']);



Route::get('get_category_wise_revenue/{affiliate_id?}', [DashboardController::class, 'get_category_wise_revenue'])->name('gcwr_api');


Route::get('get_monthly_revenue/{affiliate_id?}', [DashboardController::class, 'get_monthly_revenue'])->name('gmr_api');




Route::middleware('auth:sanctum')->group(function () {

    Route::post('save_user_subscription', [TransactionController::class, 'save_user_subscription']);
    Route::post('save_user_subscription_details', [TransactionController::class, 'save_user_subscription_details']);
    Route::post('save_user_transaction', [TransactionController::class, 'save_user_transaction']);
	
});
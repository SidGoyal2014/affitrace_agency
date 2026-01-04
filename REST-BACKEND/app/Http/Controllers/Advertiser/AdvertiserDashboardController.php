<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Advertiser\AdvertiserDashboardService;

class AdvertiserDashboardController extends Controller
{
    public function dashboard(Request $request, AdvertiserDashboardService $service)
    {
        $logged_in_user_type = $request->user()->user_type;
        $advertiser_id = Auth::id();

        return response()->json($service->getDashboardStats($advertiser_id, $logged_in_user_type));
    }
}



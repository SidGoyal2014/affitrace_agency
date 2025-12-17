<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Advertiser\AdvertiserOfferService;

class AdvertiserOfferController extends Controller
{
    public function my_offers(Request $request, AdvertiserOfferService $service)
    {
        try {
            return response()->json($service->getMyOffers(Auth::id()));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function approved_offers(Request $request, AdvertiserOfferService $service)
    {
        try {
            return response()->json($service->getApprovedOffers(Auth::id()));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}


<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Advertiser\AdvertiserService;

class AdvertiserController extends Controller
{
	public function profile(Request $request, AdvertiserService $service)
	{
		return response()->json($service->getProfileData($request->user()));
	}

	public function update_profile(Request $request, AdvertiserService $service)
	{
		try {
			return $service->updateProfile($request);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}
}


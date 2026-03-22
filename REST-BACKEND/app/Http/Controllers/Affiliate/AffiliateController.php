<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Affiliate\AffiliateService;

class AffiliateController extends Controller
{
	public function profile(Request $request, AffiliateService $service)
	{
		return response()->json($service->getProfileData($request->user()));
	}

	public function update_profile(Request $request, AffiliateService $service)
	{
		try {
			return $service->updateProfile($request);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	public function a_postback_cols(Request $request, AffiliateService $service)
	{
		return response()->json($service->getPostbackColumns(Auth::id(), $request->user()->user_type));
	}

	public function update_a_postback_cols(Request $request, AffiliateService $service)
	{
		try {
			$result = $service->updatePostbackColumns($request->all(), Auth::id());
			return response()->json($result);
		} catch (\Exception $e) {
			\Illuminate\Support\Facades\Log::error("Postback Update Error: " . $e->getMessage());
			\Illuminate\Support\Facades\Log::error($e->getTraceAsString());
			return response()->json(['error' => 'Postback columns could not be updated', 'details' => $e->getMessage()], 500);
		}
	}
}


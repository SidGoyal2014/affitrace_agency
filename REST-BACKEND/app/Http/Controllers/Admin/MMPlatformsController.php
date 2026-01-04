<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminMMPlatformsService;

class MMPlatformsController extends Controller
{
	protected $mmPlatformsService;

	public function __construct(AdminMMPlatformsService $mmPlatformsService)
	{
		$this->mmPlatformsService = $mmPlatformsService;
	}

	public function list_platforms()
	{
		return response()->json($this->mmPlatformsService->listPlatforms());
	}

	public function setup_platform(Request $request)
	{
		return response()->json($this->mmPlatformsService->setupPlatform($request));
	}
}

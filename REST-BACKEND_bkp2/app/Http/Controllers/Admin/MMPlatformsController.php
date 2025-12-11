<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use DB, Auth, Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\User;
use App\Http\Traits\CommonFunctions;
use App\Http\Traits\CommonProfileFunctions;

use App\Http\Traits\SystemSettings;

class MMPlatformsController extends Controller
{

	use CommonFunctions, CommonProfileFunctions, SystemSettings;

	public function list_platforms()
	{

		// Currently view returned hardcoded list or partials.
		// For API, we might return static list or config.
		// Assuming empty for now or static list if defined later.

		return response()->json([
			'module_name' => "List mobile marketing platforms",
			'platforms' => [] // Populate if DB or static list exists
		]);


	}//list_platforms



	public function setup_platform($id)
	{

		// Logic to get platform details if available

		return response()->json([
			'module_name' => "Setup platform",
			'platform_id' => $id
		]);


	}//setup_platform


}//End of class AdminController

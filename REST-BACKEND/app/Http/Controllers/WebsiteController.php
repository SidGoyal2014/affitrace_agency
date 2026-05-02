<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Event;

use DB;

class WebsiteController extends Controller
{

	protected $websiteService;

	public function __construct(\App\Services\WebsiteService $websiteService)
	{
		$this->websiteService = $websiteService;
	}

	public function invalid_country()
	{
		$this->websiteService->getInvalidCountryViewData();
		return view('invalid_country');

	}//about


	public function verify()
	{
		$this->websiteService->getVerifyViewData();
		return view()->file($this->resolveUiFilePath('verify_response.php'));

	}//verify


	public function clicks_cap_reached()
	{
		return view()->file($this->resolveUiFilePath('cap_clicks_reached.php'));
	}


	public function conversions_cap_reached()
	{
		return view()->file($this->resolveUiFilePath('cap_conversions_reached.php'));
	}

	private function resolveUiFilePath(string $fileName): string
	{
		$candidates = [
			base_path('UI/' . $fileName),
			base_path('../UI/' . $fileName),
		];

		foreach ($candidates as $path) {
			if (is_file($path)) {
				return $path;
			}
		}

		return $candidates[0];
	}




}//WebsiteController

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
		return view('verify_response');

	}//about




}//WebsiteController

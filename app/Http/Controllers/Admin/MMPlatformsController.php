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

class MMPlatformsController extends Controller {

	use CommonFunctions, CommonProfileFunctions, SystemSettings;

	public function list_platforms(){

		return view('admin.mmp.list_platforms')
			->with('module_name', "List mobile marketing platforms")
			;



	}//list_platforms



	public function setup_platform($id){



		return view('admin.mmp.setup_platform')
			
			->with('module_name', "Setup platform")
			;


	}//setup_platform


	/*
	public function list_platforms_old(){

		//$url = env('MAIN_WEBSITE')."/api/get_platforms";
		$url = env('MAIN_WEBSITE')."/api/get_plans";

	    $header = array(
	        'Content-Type: application/json'
	    );


		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$response = json_decode(curl_exec($ch));

		curl_close($ch);


		return view('admin.mmp.list_platforms')
			->with('data', $response->data)
			->with('module_name', "List mobile marketing platforms")
			;



	}//list_platforms_old

	*/


}//End of class AdminController

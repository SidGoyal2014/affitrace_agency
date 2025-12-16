<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Event;

use DB;

class WebsiteController extends Controller{
    

	public function invalid_country(){
		return view('invalid_country');

	}//about


	public function verify(){
		return view('verify_response');

	}//about




}//WebsiteController

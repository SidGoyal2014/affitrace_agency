<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

use foroco\BrowserDetection;

use DB, Auth, Hash;

use Carbon\Carbon;

use App\Models\User;
use App\Models\Offer;
use App\Models\OfferTracking;
use App\Models\Setting;


trait SystemSettings{

	public function get_settings($column_name=""){

		$data = Setting::find(1);

		if($column_name != ""){
			return $data->$column_name;

		}else{
			return $data;

		}

	}//get_setting


	public function get_total_offers(){

		return Offer::count();


	}//get_total_offers

	public function get_total_affiliates(){

		return User::where('user_type', 'affiliate')->get()->count();


	}//get_total_offers


	public function get_total_clicks(){

		//DB::enableQueryLog();

		$currentMonth = Carbon::now()->month;

		return OfferTracking::where('tid', '!=', '')->whereMonth('created_at', $currentMonth)->get()->count();

		//return $data;
		//dd(DB::getQueryLog());

	}//get_total_clicks


	public function get_total_conversions(){

		$currentMonth = Carbon::now()->month;
		
		return OfferTracking::where('aff_click_id', '!=', '')->whereMonth('created_at', $currentMonth)->get()->count();


	}//get_total_conversions


}//SystemSettings

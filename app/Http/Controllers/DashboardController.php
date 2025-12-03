<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DB, Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\Offer;
use App\Models\OfferTracking;
use App\Models\User;
use App\Models\Category;
use App\Models\AffiliateUserDetail;
use App\Models\AdvertiserUserDetail;


class DashboardController extends Controller
{


	
	public function top_affiliates($dod="Y"){

		//dod = display on dashboard

		$strLimit = "";

		if($dod == "Y"){
			$strLimit = " LIMIT 0,5";

		}//if


		$sql = "SELECT U.id as uid, OT.aff_id, U.name, sum(OT.payout) as payout FROM  users U INNER JOIN offer_trackings OT ON U.id=OT.aff_id GROUP BY OT.aff_id ORDER BY revenue DESC".$strLimit;

		$data = DB::select($sql);

		if($dod == "Y"){
			return $data;

		}else{


			return view('admin.reports.top_affiliates')
				->with('data', $data)
				->with('show_advrt_name', 'Y')
				->with('advrt_name', "")
				->with('module_name', 'Top Affiliates');



		}//if-else

	}//top_affiliates


	public function top_offers($dod="Y"){

		$sql = "SELECT OT.offer_id, O.offer_name, sum(OT.revenue) as revenue, O.offer_status FROM  offers O INNER JOIN offer_trackings OT ON O.id=OT.offer_id GROUP BY OT.offer_id ORDER BY revenue DESC LIMIT 0,5";

		$arr = DB::select($sql);


		if($dod == "Y"){
			return $arr;

		}else{


			return view('admin.reports.top_offers')
				->with('data', $arr)
				->with('show_advrt_name', 'Y')
				->with('advrt_name', "")
				->with('module_name', 'Top Affiliates');



		}//if-else



	}//top_offers


	public function get_category_wise_revenue($affiliate_id = ""){

		$strWhereClause="";

		if($affiliate_id > 0){
			$strWhereClause = " AND ot.aff_id = ".$affiliate_id;

		}//if

		$sql = "SELECT 
		    MONTH(o.created_at) AS month,
		    c.category_name,
		    SUM(ot.revenue_usd) AS revenue
		FROM 
		    offer_trackings ot
		JOIN 
		    offers o ON ot.offer_id = o.id
		JOIN 
		    categories c ON o.category_id = c.id
		WHERE
			aff_click_id != ''
		    ".$strWhereClause."

		GROUP BY 
		    MONTH(o.created_at), c.category_name
		ORDER BY 
		    MONTH(o.created_at), c.category_name
		LIMIT 0,5";
		

		$arr = DB::select($sql);

        if(count($arr) > 0){
        	return response()->json(array('success' => true, 'data' => $arr), 200);

    	}else{
    		return response()->json(array('success' => false), 500);

    	}/**/

	}//get_category_wise_revenue


	public function get_monthly_revenue($affiliate_id = ""){

		$strWhereClause="";

		if($affiliate_id > 0){
			$strWhereClause = " AND ot.aff_id = ".$affiliate_id;

		}//if

		$arrSTR = array();

		$sql = "SELECT sum(revenue_usd) as revenue, month(created_at) as month FROM offer_trackings where aff_click_id != ''  ".$strWhereClause." group by month(created_at)";



		$arr = DB::select($sql);
		
		foreach($arr as $obj){

			$month=0;

			if(trim($obj->month) == ""){
				$month=0;
			}else{
				$month=$obj->month;

			}

			$arrSTR[$month] = $obj->revenue;

		}//foreach


		for($index = 1; $index < 12; $index++){

			if(!isset($arrSTR[$index])){
				$arrSTR[$index] = 0;
			}

		}//foreach

		ksort($arrSTR, SORT_NUMERIC);

		$arrMonthlyRevenue ='['.implode(',', $arrSTR).']';


        if(count($arrSTR) > 0){
        	return response()->json(array('success' => true, 'data' => $arrMonthlyRevenue), 200);

    	}else{
    		return response()->json(array('success' => false), 500);

    	}

		//return $arrMonthlyRevenue;


	}//get_monthly_revenue

	private function get_total_offers(){

		if (Auth::check()){
			return Offer::count();
		}else{
			return redirect('/login');
		
		}


	}//get_total_offers


	private function get_total_revenue(){

		if (Auth::check()){

			$total_rev = OfferTracking::select(DB::raw('SUM(revenue_usd) as total'))
                    ->where('aff_click_id', '!=', '')
                    ->get()->toArray();

            return $total_rev[0]['total'];


		}else{
			return redirect('/login');
		
		}


	}//get_total_revenue


	private function get_total_payouts(){

		if (Auth::check()){

			$to_payout = OfferTracking::select(DB::raw('SUM(payout_usd) as total'))
                    ->where('aff_click_id', '!=', '')
                    ->get()->toArray();

            return $to_payout[0]['total'];



		}else{
			return redirect('/login');
		
		}


	}//get_total_payouts


	private function get_total_no_of_affiliates(){

		if (Auth::check()){
			return User::WHERE('user_type','affiliate')->count();

		}else{
			return redirect('/login');
		
		}


	}//get_total_no_of_affiliates


	private function get_average_revenue_per_offer(){

		if (Auth::check()){

			if($this->get_total_offers() > 0){
				return $this->get_total_revenue() / $this->get_total_offers();

			}else{

				return '0';
			}
			
			
		}else{
			return redirect('/login');
		
		}


	}//get_average_payout_per_offer	



	private function get_average_revenue_per_affiliate(){

		if (Auth::check()){

			if($this->get_total_no_of_affiliates() > 0){
				return $this->get_total_revenue() / $this->get_total_no_of_affiliates();

			}else{

				return '0';
			}
			
		}else{
			return redirect('/login');
		
		}


	}//get_average_payouts_per_affiliate	

	private function get_average_payout_per_offer(){

		if (Auth::check()){

			if($this->get_total_offers() > 0){
				return $this->get_total_payouts() / $this->get_total_offers();
			}else{
				return '0';
			}

			
		}else{
			return redirect('/login');
		
		}


	}//get_average_payout_per_offer	



	private function get_average_payout_per_affiliate(){

		if (Auth::check()){

			if($this->get_total_no_of_affiliates() > 0){
				return $this->get_total_payouts() / $this->get_total_no_of_affiliates();

			}else{
				return '0';

			}
			
		}else{
			return redirect('/login');
		
		}


	}//get_average_payouts_per_affiliate	




}//DashboardController

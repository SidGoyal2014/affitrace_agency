<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DB, Auth, Http, Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\Offer;
use App\Models\OfferAffiliate;
use App\Models\OfferAssignRequest;
use App\Models\OfferTracking;
use App\Models\User;
use App\Models\Category;
use App\Models\AffiliateUserDetail;
use App\Models\AdvertiserUserDetail;

use App\Http\Traits\CommonFunctions;

class AffiliateDashboardController extends Controller
{

	use CommonFunctions;

	public function dashboard(Request $request){


		if (Auth::check()){

			$logged_in_user_type = $request->session()->get('user_type');

			$arr = $this->get_category_wise_revenue();

			$arrCats = array();
			$arrRevenue = array();

			foreach($arr as $obj){

				$arrCats[] = $obj->category_name;
				$arrRevenue[] = $obj->revenue;

			}//foreach

			$labelCategories = '['.implode(',', $arrCats).']';

			$strRevenue ='['.implode(',', $arrRevenue).']';

			/*
			$arrOAR = array();
            foreach($this->get_offer_assign_requests() as $k=>$vArr){
                $arrOAR[] = $vArr->offer_id;
            }
            */



			$obj_offers = $this->get_affiliate_offers();




			return view($logged_in_user_type.'/dashboard')

				->with('title', ucfirst($logged_in_user_type).' Dashboard')
				->with('module_name', ucfirst($logged_in_user_type).' Dashboard')
				->with('logged_in_user_type', $logged_in_user_type)
				->with('all_offers', $this->get_all_offers() )
				->with('arr_affiliate_offers', explode(",", $obj_offers->all_offer_ids) )

				->with('offers_assigned', $this->get_offers_assigned() )
				//->with('offer_assign_requests', $this->get_offer_assign_requests() )
				//->with('arrOAR', $arrOAR )



				->with('arrMonthlyRevenue', $this->get_monthly_revenue() )
				->with('arrCats', $arrCats )
				->with('labelCategories', $labelCategories )
				->with('strRevenue', $strRevenue )
				->with('topEarningOffers', $this->top_offers() )
				->with('topEarningAffiliates', $this->top_affiliates() )
				;


		}else{
			return redirect('/login');
		
		}

	}//dashboard

	
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




	private function get_category_wise_revenue(){

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
			ot.aff_id = ".Auth::id()."
		GROUP BY 
		    MONTH(o.created_at), c.category_name
		ORDER BY 
		    MONTH(o.created_at), c.category_name
		LIMIT 0,5";


		$arr = DB::select($sql);

		return $arr;

	}//get_category_wise_revenue


	private function get_monthly_revenue(){

		//where year(campaign_start_date) = year(now()) 
		
		$arrSTR = array();

		$sql = "SELECT sum(revenue_usd) as revenue, month(created_at) as month FROM offer_trackings where aff_id = ".Auth::id()." AND aff_click_id != '' group by month(created_at); ";

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


		return $arrMonthlyRevenue;


	}//get_monthly_revenue

	private function get_all_offers(){

		if (Auth::check()){

			$sql = "
					SELECT 
						*, offers.id as oid 

					FROM 
						categories 

					INNER JOIN 
						offers 

					ON 
						categories.id = offers.category_id 
				";

			$all_offers = DB::select($sql);

			return $all_offers;


		}else{
			return redirect('/login');
		
		}


	}//get_all_offers


	function get_affiliate_offers(){

		if (Auth::check()){

			$sql = "
					SELECT 
						GROUP_CONCAT( offer_id ) AS all_offer_ids
					FROM 
						offer_affiliates 
					WHERE
						affiliate_id = ".Auth::id();
				

			$data = DB::select($sql);

			return $data[0];


		}else{
			return redirect('/login');
		
		}


	}//get_affiliate_offers




	private function get_offers_assigned(){

		try{

			$sql = "
					SELECT 
						* 
					FROM 
						offers INNER JOIN offer_affiliates 
					ON 
						offers.id = offer_affiliates.offer_id 
					WHERE 
						offer_affiliates.affiliate_id = ".Auth::id();
				

			$data = DB::select($sql);

			return $data;

			//return Offer::all();
		}catch( \Exception $e ){
			return redirect('/login')->withErrors($e->getMessage());
		
		}


	}//get_offer_assign_requests




	public function send_offer_assign_request($oid){


		if (Auth::check()){

			/*

			OfferAffiliate::where('offer_id', $oid)
							->where('affiliate_id', Auth::id())
							->update([
								'request_made' => 'Y',
								'request_status' => 'Pending'
							]);

			*/
			$rec = new OfferAffiliate();
			$rec->offer_id = $oid;
			$rec->affiliate_id = Auth::id();			
			$rec->request_made = 'Y';
			$rec->request_status = 'Pending';
			$rec->save();

			return redirect('affiliate/dashboard')->with('success', 'Approval request sent successfully');


		}else{
			return redirect('/login');
		
		}


	}//send_offer_assign_request


	/*

	private function get_offer_assign_requests(){

		if (Auth::check()){

			$sql = "
					SELECT 
						*
					FROM 
						offer_assign_requests
					WHERE
						affiliate_id = ".Auth::id();
				

			$data = DB::select($sql);

			return $data;

			//return Offer::all();
		}else{
			return redirect('/login');
		
		}


	}//get_offer_assign_requests




	public function send_offer_assign_request($oid){


		if (Auth::check()){

			$rec = new OfferAssignRequest();
			$rec->offer_id = $oid;
			$rec->affiliate_id = Auth::id();			
			$rec->request_made = 'Y';
			$rec->request_status = 'Pending';
			$rec->save();

			return redirect('affiliate/dashboard')->with('success', 'Approval request sent successfully');


		}else{
			return redirect('/login');
		
		}


	}//send_offer_assign_request


	*/

}//DashboardController

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DB, Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\Offer;
use App\Models\OfferAffiliate;
use App\Models\OfferAssignRequest;
use App\Models\OfferTracking;
use App\Models\User;
use App\Models\Category;
use App\Models\AffiliateUserDetail;
use App\Models\AdvertiserUserDetail;

use App\Http\Traits\SystemSettings;

class AdminDashboardController extends Controller {

	use SystemSettings;

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



			return view($logged_in_user_type.'/dashboard')

				->with('title', ucfirst($logged_in_user_type).' Dashboard')
				->with('module_name', ucfirst($logged_in_user_type).' Dashboard')
				->with('logged_in_user_type', $logged_in_user_type)
				->with('click_message', $this->get_clicks_message())

				->with('total_offers', $this->get_total_offers() )
				->with('total_revenue', $this->get_total_revenue() )
				->with('total_payouts', $this->get_total_payouts() )
				->with('total_no_of_affiliates', $this->get_total_no_of_affiliates() )
				->with('all_requests', $this->get_offer_assign_requests() )
				->with('average_revenue_per_offer', $this->get_average_revenue_per_offer() )
				->with('average_revenue_per_affiliate', $this->get_average_revenue_per_affiliate() )
				->with('average_payout_per_offer', $this->get_average_payout_per_offer() )
				->with('average_payout_per_affiliate', $this->get_average_payout_per_affiliate() )
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

	
	public function get_clicks_message(){

		$click_message = "";
		if($this->get_total_clicks() >= $this->get_settings("num_clicks")){

			$click_message = "Clicks not getting saved as maximum allowed limit as per purchased plan already reached for this month. Please upgrade.";
			
		}elseif($this->get_settings("num_clicks") - $this->get_total_clicks() < 1000){

			$click_message = "Maximum allowed limit of clicks is approaching fast. Less than 1000 clicks remaining for this month. Please upgrade.";

		}

		return $click_message;

	}//get_clicks_message


	public function top_affiliates($dod="Y"){

		//dod = display on dashboard

		$strLimit = "";

		if($dod == "Y"){
			$strLimit = " LIMIT 0,5";

		}//if


		$sql = "SELECT U.id as uid, OT.aff_id, U.name, sum(OT.payout) as payout FROM  users U INNER JOIN offer_trackings OT ON U.id=OT.aff_id GROUP BY OT.aff_id, U.id, U.name ORDER BY payout DESC".$strLimit;

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

		$sql = "SELECT OT.offer_id, O.offer_name, sum(OT.revenue) as revenue, O.offer_status FROM  offers O INNER JOIN offer_trackings OT ON O.id=OT.offer_id GROUP BY OT.offer_id, O.offer_name, O.offer_status ORDER BY revenue DESC LIMIT 0,5";

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


		//$sql = "SELECT sum(revenue_usd) as revenue, month(campaign_start_date) as month FROM offers group by month(campaign_start_date)";


		$sql = "SELECT sum(revenue_usd) as revenue, month(created_at) as month FROM offer_trackings where aff_click_id != '' group by month(created_at)";



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


	private function get_offer_assign_requests(){

		if (Auth::check()){

			/*$sql = "
					SELECT 
						*, offers.id as oid, users.id as aid, offer_assign_requests.id as oaid
					FROM 
						offers 
						INNER JOIN 
							offer_assign_requests 
							ON 
								offers.id = offer_assign_requests.offer_id
						INNER JOIN 
							users 
							ON 
								offer_assign_requests.affiliate_id = users.id
					WHERE
						request_made = 'Y'
						AND
						request_status != 'Accepted'
				";*/
			$sql = "
					SELECT 
						*, offers.id as oid, users.id as aid, offer_affiliates.id as oaid
					FROM 
						offers 
						INNER JOIN 
							offer_affiliates
							ON 
								offers.id = offer_affiliates.offer_id
						INNER JOIN 
							users 
							ON 
								offer_affiliates.affiliate_id = users.id
					WHERE
						request_made = 'Y'
						AND
						request_status != 'Accepted'
				";
			$all_requests = DB::select($sql);

			return $all_requests;

			//return Offer::all();
		}else{
			return redirect('/login');
		
		}


	}//get_offer_assign_requests



	public function reject_offer_assign_requests($oaid){

		if (Auth::check()){

			//$rec = OfferAssignRequest::find($oaid);
			$rec = OfferAffiliate::find($oaid);
			$rec->request_status = 'Rejected';
			$rec->save();

			return redirect('admin/dashboard')->with('success', 'Offer assign request updated successfully');


		}else{
			return redirect('/login');
		
		}


	}//reject_offer_assign_requests



}//AdminDashboardController

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

use DB, Auth, Session;

use App\Models\User;

use App\Models\AdModel;
use App\Models\Device;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Setting;

use App\Models\AffiliateUserDetail;
use App\Models\AdvertiserUserDetail;
use App\Models\OfferTracking;
use App\Http\Traits\CommonFunctions;
use App\Http\Traits\CommonOfferFunctions;

use App\Models\Subscription;
use App\Models\SubscriptionStatusDetail;



class PaymentController extends Controller
{
    use CommonFunctions, CommonOfferFunctions;


    public function plan_details(){


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

		

		$data = Setting::all();

		pr($data);
		pr($response);

		

		return view('admin.payments.plan_details')
			->with('obj', $data[0])
			->with('dataPlans', $response->data)
			->with('show_advrt_name', 'Y')
			->with('advrt_name', "")
			->with('module_name', 'Show plan details');


    }//plan_details




    public function show_subscriptions(){



    	$data = DB::table('subscriptions as s')
    			->leftJoin('subscription_status_details as ssd', function ($join) {
        				$join->on('s.id', '=', 'ssd.subscription_id')
             			->whereRaw('ssd.id = (select max(id) from subscription_status_details where subscription_status_details.subscription_id = s.id)');
   			 		}
   				)
    			->select('s.*', 'ssd.*', 'ssd.id as ssdid')
    			->get();


		return view('admin.payments.show_subscriptions')
			->with('data', $data)
			->with('show_advrt_name', 'Y')
			->with('advrt_name', "")
			->with('module_name', 'Show Subscriptions');



    }//show_subscriptions



    public function pause_subscription($id){


    	$obj = SubscriptionStatusDetail::find($id);
    	/**/

    	$postData = array();
		$postData['subscription_id'] = $obj->subscription_id;
		$postData['stripe_subscription_id'] = $obj->stripe_subscription_id;


		$url = env('MAIN_WEBSITE')."/api/pause-subscription";

		$response = $this->send_data($postData, $url);


		if($response->success == "Y"){

			$objS = Subscription::find($obj->subscription_id);
			$objS->status = "Paused";
    		$objS->save();
	    		
			$objSSD = new SubscriptionStatusDetail();
			$objSSD->subscription_id = $obj->subscription_id;
			$objSSD->stripe_subscription_id = $obj->stripe_subscription_id;
			$objSSD->status = "Paused";
    		$objSSD->save();

			return redirect('admin/payments/show_subscription')->with('success', 'Subscription paused successfully');

		}else{

			return redirect('admin/payments/show_subscription')->with('success', 'Subscription could not be updated.');
		}



    }//pause_subscription



    public function resume_subscription($id){

    	try{

	    	$obj = SubscriptionStatusDetail::find($id);

	    	$postData = array();
			$postData['subscription_id'] = $obj->subscription_id;
			$postData['stripe_subscription_id'] = $obj->stripe_subscription_id;


			$url = env('MAIN_WEBSITE')."/api/resume-subscription";

			$response = $this->send_data($postData, $url);


			if($response->success == "Y"){

				$objS = Subscription::find($obj->subscription_id);
				$objS->status = "Resumed";
	    		$objS->save();

				$objSSD = new SubscriptionStatusDetail();
				$objSSD->subscription_id = $obj->subscription_id;
				$objSSD->stripe_subscription_id = $obj->stripe_subscription_id;
				$objSSD->status = "Resumed";
	    		$objSSD->save();

				return redirect('admin/payments/show_subscription')->with('success', 'Subscription resumed successfully');

			}else{

				return redirect('admin/payments/show_subscription')->with('success', 'Subscription could not be updated.');
			}


		}catch(\Exception $e){

			return redirect('admin/payments/show_subscription')->with('error', $e->getMessage());

		}


    }//resume_subscription


    public function delete_subscription($id){

    	try{

	    	$obj = SubscriptionStatusDetail::find($id);

	    	$postData = array();
			$postData['subscription_id'] = $obj->subscription_id;
			$postData['stripe_subscription_id'] = $obj->stripe_subscription_id;


			$url = env('MAIN_WEBSITE')."/api/delete-subscription";

			$response = $this->send_data($postData, $url);


			if($response->success == "Y"){

				$objS = Subscription::find($obj->subscription_id);
				$objS->status = "Deleted";
	    		$objS->save();

				$objSSD = new SubscriptionStatusDetail();
				$objSSD->subscription_id = $obj->subscription_id;
				$objSSD->stripe_subscription_id = $obj->stripe_subscription_id;
				$objSSD->status = "Deleted";
	    		$objSSD->save();

				return redirect('admin/payments/show_subscription')->with('success', 'Subscription resumed successfully');

			}else{

				return redirect('admin/payments/show_subscription')->with('success', 'Subscription could not be updated.');
			}


		}catch(\Exception $e){

			return redirect('admin/payments/show_subscription')->with('error', $e->getMessage());

		}


    }//delete_subscription


	public function send_data($postData, $url, $bearer_token=''){

		try{

			$postData = json_encode($postData);

		    $header = array(
		        'Content-Type: application/json'/*,
		        'Authorization:Bearer ' . $bearer_token*/
		    );

			$ch = curl_init($url);

			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

			
			$resp = curl_exec($ch);


			if (curl_errno($ch)) {
			    echo $error_msg = curl_error($ch);
			    curl_close($ch);
			}

/*echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~";
pr($resp);*/

			$response = json_decode($resp);

/*pr($response);

die("-=-=-!!!!!!!".$response->success."!!!!!!!!=-=-=-=-");*/

			return $response;


        }catch( \Exception $e ){

            return response()->json(array('success' => false, 'message' => $e->getMessage()), 500);

        }//try-catch


	}//send_data


    /*
    public function confirm_password(){

    	return view('admin/payments/admin_login_form')->with('module_name', 'Comfirm Password');
    	
    }//confirm_password


    public function do_confirm_password(Request $request){


		$postinfo = "email=".Auth::user()->email."&password=".$request->password;

		$ch = curl_init(env('MAIN_WEBSITE').'/api/userlogin');

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$response = json_decode(curl_exec($ch));

		curl_close($ch);

		Session::put('bearer_token', $response->token);

		return redirect('admin/payments/transactions');


    }//do_confirm_password
    */




}//PaymentsController

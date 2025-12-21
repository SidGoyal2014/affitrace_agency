<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminPaymentService;

class PaymentController extends Controller
{
	protected $paymentService;

	public function __construct(AdminPaymentService $paymentService)
	{
		$this->paymentService = $paymentService;
	}

	public function index()
	{
		return response()->json(['module_name' => 'Payments Index', 'success' => true]);
	}

	public function get_plan_details(Request $request)
	{
		return response()->json($this->paymentService->getPlanDetails());
	}

	public function show_subscriptions(Request $request)
	{
		return $this->paymentService->showSubscriptions($request);
	}

	public function pause_subscription($id)
	{
		$result = $this->paymentService->pauseSubscription($id);
		return response()->json($result);
	}

	public function resume_subscription($id)
	{
		$result = $this->paymentService->resumeSubscription($id);
		return response()->json($result);
	}

	public function delete_subscription($id)
	{
		$result = $this->paymentService->deleteSubscription($id);
		return response()->json($result);
	}
}


// 	public function send_data($postData, $url, $bearer_token=''){

// 		try{

// 			$postData = json_encode($postData);

// 		    $header = array(
// 		        'Content-Type: application/json'/*,
// 		        'Authorization:Bearer ' . $bearer_token*/
// 		    );

// 			$ch = curl_init($url);

// 			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
// 			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 			curl_setopt($ch, CURLOPT_POST, true);
// 			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
// 			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);


// 			$resp = curl_exec($ch);


// 			if (curl_errno($ch)) {
// 			    echo $error_msg = curl_error($ch);
// 			    curl_close($ch);
// 			}

// /*echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~";
// pr($resp);*/

// 			$response = json_decode($resp);

// /*pr($response);

// die("-=-=-!!!!!!!".$response->success."!!!!!!!!=-=-=-=-");*/

// 			return $response;


//         }catch( \Exception $e ){

//             return response()->json(array('success' => false, 'message' => $e->getMessage()), 500);

//         }//try-catch


// 	}//send_data


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




//}//PaymentsController

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Subscription;
use App\Models\SubscriptionStatusDetail;
use App\Models\Transaction;

use DB;

class TransactionController extends Controller{


    public function  save_user_subscription_details(Request $request){

        /**
        # IMPORTANT
        # COMMENT:
        1. This method is API method
        2. It is called from main parent website to save each subscription status details in db
        */

        try{

            $objSSD = new SubscriptionStatusDetail();
            $objSSD->subscription_id = $request->subscription_id;
            $objSSD->stripe_subscription_id = $request->stripe_subscription_id;
            $objSSD->status = $request->status;
            $objSSD->save();



            if($objSSD->id > 0){
                return response()->json(array('success' => true, 'last_insert_id' => $objSSD->id), 200);

            }else{
                return response()->json(array('success' => false), 500);

            }


        }catch( \Exception $e ){

            return response()->json(array('success' => false, 'message' => $e->getMessage()), 500);

        }//try-catch

    }//save_user_subscription_details



    public function save_user_subscription(Request $request){

        /**
        # IMPORTANT
        # COMMENT:
        1. This method is API method
        2. It is called from main parent website to save each subscription data in db
        */
        
        DB::beginTransaction();

        try{

            $objSub = new Subscription();

            $objSub->stripe_plan_id = $request->stripe_plan_id;
            $objSub->plan_name = $request->plan_name;
            $objSub->plan_type = $request->plan_type;
            $objSub->price = $request->price;

            $objSub->pg_customer_id = isset($request->pg_customer_id)?$request->pg_customer_id:null;

            $objSub->stripe_subscription_id = isset($request->stripe_subscription_id)?$request->stripe_subscription_id:null;

            $objSub->subscription_amount = isset($request->subscription_amount)?$request->subscription_amount:null;

            $objSub->subscription_start_date = isset($request->subscription_start_date)?$request->subscription_start_date:null;

            $objSub->subscription_end_date = isset($request->subscription_end_date)?$request->subscription_end_date:null;

            $objSub->subscription_details = isset($request->subscription_details)?$request->subscription_details:null;

            $objSub->is_active = isset($request->is_active)?$request->is_active:null;
            $objSub->status = isset($request->status)?$request->status:null;
            $objSub->save();


            if($objSub->id > 0){

                $objSSD = new SubscriptionStatusDetail();
                $objSSD->subscription_id = $objSub->id;
                $objSSD->stripe_subscription_id = isset($request->stripe_subscription_id)?$request->stripe_subscription_id:null;
                $objSSD->status = isset($request->status)?$request->status:null;
                $objSSD->save();

                DB::commit();

            }else{
                //Log::info('Error indormation subscription: ' . $e->getMessage());
                return response()->json(array('success' => false), 500);

            }


        }catch( \Exception $e ){
            DB::rollBack();
            //Log::error('Error saving subscription: ' . $e->getMessage());
            return response()->json(array('success' => false, 'message' => $e->getMessage()), 500);

        }//try-catch

    }//save_user_subscription



    public function save_user_transaction(Request $request){

        /**
        # IMPORTANT
        # COMMENT:
        1. This method is API method
        2. It is called from main parent website to save each transaction data in db
        */

        DB::beginTransaction();

        try {


            $objSSD = new SubscriptionStatusDetail();
            $objSSD->subscription_id = $request->subscription_id;
            $objSSD->stripe_subscription_id = $request->stripe_subscription_id;
            $objSSD->status = $request->activity;
            $objSSD->save();

            $obj = new Transaction();
            $obj->invoice_id = $request->invoice_id;
            $obj->billing = $request->billing;
            $obj->billing_reason = $request->billing_reason;
            $obj->billing_charge = $request->billing_charge;
            $obj->pg_customer_id = $request->pg_customer_id;
            $obj->stripe_subscription_id = $request->stripe_subscription_id;
            $obj->transaction_date = $request->transaction_date;
            $obj->activity = $request->activity;
            $obj->subscription_id = $request->subscription_id;
            $obj->save();

            DB::commit();

            if($obj->id > 0){
                return response()->json(array('success' => true, 'last_insert_id' => $obj->id), 200);

            }else{
                return response()->json(array('success' => false), 500);

            }

        }catch( \Exception $e ){

            DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()), 500);

        }//try-catch

    }//save_user_transaction
    /**/


    /*


    public function save_transaction(Request $request){


        try {





            $obj = new Transaction();

            $obj->invoice_id = $request->invoice_id;
            $obj->billing = $request->billing;
            $obj->billing_reason = $request->billing_reason;
            $obj->billing_charge = $request->billing_charge;
            $obj->stripe_plan_id = $request->stripe_plan_id;

            $obj->plan_name = $request->plan_name;
            $obj->plan_type = $request->plan_type;
            $obj->price = $request->price;
            $obj->pg_customer_id = $request->pg_customer_id;

            $obj->stripe_subscription_id = $request->stripe_subscription_id;

            $obj->subscription_amount = $request->subscription_amount;
            $obj->subscription_start_date = $request->subscription_start_date;
            $obj->subscription_end_date = $request->subscription_end_date;
            $obj->transaction_date = $request->transaction_date;
            $obj->activity = $request->activity;

            $obj->save();

            if($obj->id > 0){
                return response()->json(array('success' => true, 'last_insert_id' => $obj->id), 200);

            }else{
                return response()->json(array('success' => false), 500);

            }

        }catch( \Exception $e ){


            return response()->json(array('success' => false, 'message' => $e->getMessage()), 500);

        }//try-catch

    }//save_transaction



    public function save_user_transaction(Request $request){

        / **
        # IMPORTANT
        # COMMENT:
        1. This method is API method
        2. It is called from main parent website to save each transaction data in db
        * /

        try {

            $obj = new Transaction();
            $obj->invoice_id = $request->invoice_id;
            $obj->billing = $request->billing;
            $obj->billing_reason = $request->billing_reason;
            $obj->billing_charge = $request->billing_charge;
            $obj->pg_customer_id = $request->pg_customer_id;
            $obj->stripe_subscription_id = $request->stripe_subscription_id;
            $obj->transaction_date = $request->transaction_date;
            $obj->activity = $request->activity;
            $obj->subscription_id = $request->subscription_id;
            $obj->save();



            if($obj->id > 0){
                return response()->json(array('success' => true, 'last_insert_id' => $obj->id), 200);

            }else{
                return response()->json(array('success' => false), 500);

            }

        }catch( \Exception $e ){


            return response()->json(array('success' => false, 'message' => $e->getMessage()), 500);

        }//try-catch

    }//save_user_transaction

    */




}//TransactionController

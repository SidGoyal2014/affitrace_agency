<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Subscription;
use App\Models\SubscriptionStatusDetail;
use App\Models\Transaction;

class AdminTransactionService
{
    public function saveUserSubscriptionDetails($request)
    {
        $objSSD = new SubscriptionStatusDetail();
        $objSSD->subscription_id = $request->subscription_id;
        $objSSD->stripe_subscription_id = $request->stripe_subscription_id;
        $objSSD->status = $request->status;
        $objSSD->save();

        if ($objSSD->id > 0) {
            return ['success' => true, 'last_insert_id' => $objSSD->id, 'code' => 200];
        } else {
            return ['success' => false, 'code' => 500];
        }
    }

    public function saveUserSubscription($request)
    {
        DB::beginTransaction();

        try {
            $objSub = new Subscription();
            $objSub->stripe_plan_id = $request->stripe_plan_id;
            $objSub->plan_name = $request->plan_name;
            $objSub->plan_type = $request->plan_type;
            $objSub->price = $request->price;
            $objSub->pg_customer_id = isset($request->pg_customer_id) ? $request->pg_customer_id : null;
            $objSub->stripe_subscription_id = isset($request->stripe_subscription_id) ? $request->stripe_subscription_id : null;
            $objSub->subscription_amount = isset($request->subscription_amount) ? $request->subscription_amount : null;
            $objSub->subscription_start_date = isset($request->subscription_start_date) ? $request->subscription_start_date : null;
            $objSub->subscription_end_date = isset($request->subscription_end_date) ? $request->subscription_end_date : null;
            $objSub->subscription_details = isset($request->subscription_details) ? $request->subscription_details : null;
            $objSub->is_active = isset($request->is_active) ? $request->is_active : null;
            $objSub->status = isset($request->status) ? $request->status : null;
            $objSub->save();

            if ($objSub->id > 0) {
                $objSSD = new SubscriptionStatusDetail();
                $objSSD->subscription_id = $objSub->id;
                $objSSD->stripe_subscription_id = isset($request->stripe_subscription_id) ? $request->stripe_subscription_id : null;
                $objSSD->status = isset($request->status) ? $request->status : null;
                $objSSD->save();

                DB::commit();
                return ['success' => true, 'code' => 200]; // Assuming no content needed, original returns nothing explicit on success?
                // Original: if($objSub->id > 0) { ... DB::commit(); } else { return 500 }
                // Ah, original method save_user_subscription didn't return json on success path? It just finished.
                // Wait, it says "It is called from main parent website", likely expecting status code.
                // It does not return anything on success block end. Laravel controller returns 200 OK by default if no return?
                // I will return empty success response.
            } else {
                return ['success' => false, 'code' => 500];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
    }

    public function saveUserTransaction($request)
    {
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

            if ($obj->id > 0) {
                return ['success' => true, 'last_insert_id' => $obj->id, 'code' => 200];
            } else {
                return ['success' => false, 'code' => 500];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
    }
}

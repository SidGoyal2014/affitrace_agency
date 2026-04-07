<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SubscriptionStatusDetail;
use App\Http\Traits\CommonFunctions;
use App\Http\Traits\CommonOfferFunctions;

class AdminPaymentService
{
    use CommonFunctions, CommonOfferFunctions;

    public function getPlanDetails()
    {
        $url = env('MAIN_WEBSITE') . "/api/get_plans";

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

        return [
            'module_name' => 'Show plan details',
            'settings' => $data[0] ?? null,
            'dataPlans' => $response->data ?? []
        ];
    }

    public function showSubscriptions($request)
    {
        $data = DB::table('subscriptions as s')
            ->leftJoin(
                'subscription_status_details as ssd',
                function ($join) {
                    $join->on('s.id', '=', 'ssd.subscription_id')
                        ->whereRaw('ssd.id = (select max(id) from subscription_status_details where subscription_status_details.subscription_id = s.id)');
                }
            )
            ->select(
                's.id',
                's.plan_name',
                's.stripe_plan_id as plan_id',
                's.subscription_amount',
                's.status',
                's.subscription_end_date as expires_at',
                's.created_at as subscription_created_at',
                'ssd.*',
                'ssd.id as ssdid'
            );

        if ($request->has('status') && $request->status != "") {
            $data = $data->where('s.status', $request->status);
        }

        $data = $data->get();

        return DataTables::of($data)
            ->editColumn('subscription_created_at', function ($row) {
                return date("d-m-Y H:i:s", strtotime($row->subscription_created_at));
            })
            ->make(true);
    }

    public function pauseSubscription($id)
    {
        $obj = SubscriptionStatusDetail::find($id);
        $postData = array();
        $postData['subscription_id'] = $obj->subscription_id;
        $postData['stripe_subscription_id'] = $obj->stripe_subscription_id;

        $url = env('MAIN_WEBSITE') . "/api/pause-subscription";
        $response = $this->send_data($postData, $url);

        if ($response->success == "Y") {
            $objS = Subscription::find($obj->subscription_id);
            $objS->status = "Paused";
            $objS->save();

            $objSSD = new SubscriptionStatusDetail();
            $objSSD->subscription_id = $obj->subscription_id;
            $objSSD->stripe_subscription_id = $obj->stripe_subscription_id;
            $objSSD->status = "Paused";
            $objSSD->save();

            return ['success' => true, 'msg' => 'Subscription paused successfully', 'code' => 200];
        } else {
            return ['success' => false, 'msg' => 'Subscription could not be updated.', 'code' => 500];
        }
    }

    public function resumeSubscription($id)
    {
        $obj = SubscriptionStatusDetail::find($id);
        $postData = array();
        $postData['subscription_id'] = $obj->subscription_id;
        $postData['stripe_subscription_id'] = $obj->stripe_subscription_id;

        $url = env('MAIN_WEBSITE') . "/api/resume-subscription";
        $response = $this->send_data($postData, $url);

        if ($response->success == "Y") {
            $objS = Subscription::find($obj->subscription_id);
            $objS->status = "Resumed";
            $objS->save();

            $objSSD = new SubscriptionStatusDetail();
            $objSSD->subscription_id = $obj->subscription_id;
            $objSSD->stripe_subscription_id = $obj->stripe_subscription_id;
            $objSSD->status = "Resumed";
            $objSSD->save();

            return ['success' => true, 'msg' => 'Subscription resumed successfully', 'code' => 200];
        } else {
            return ['success' => false, 'msg' => 'Subscription could not be updated.', 'code' => 500];
        }
    }

    public function deleteSubscription($id)
    {
        $obj = SubscriptionStatusDetail::find($id);
        $postData = array();
        $postData['subscription_id'] = $obj->subscription_id;
        $postData['stripe_subscription_id'] = $obj->stripe_subscription_id;

        $url = env('MAIN_WEBSITE') . "/api/delete-subscription";
        $response = $this->send_data($postData, $url);

        if ($response->success == "Y") {
            $objS = Subscription::find($obj->subscription_id);
            $objS->status = "Deleted";
            $objS->save();

            $objSSD = new SubscriptionStatusDetail();
            $objSSD->subscription_id = $obj->subscription_id;
            $objSSD->stripe_subscription_id = $obj->stripe_subscription_id;
            $objSSD->status = "Deleted";
            $objSSD->save();

            return ['success' => true, 'msg' => 'Subscription deleted successfully', 'code' => 200];
        } else {
            return ['success' => false, 'msg' => 'Subscription could not be updated.', 'code' => 500];
        }
    }

    public function send_data($postData, $url, $bearer_token = '')
    {
        $postData = json_encode($postData);

        $header = array(
            'Content-Type: application/json'
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
            // error
            curl_close($ch);
            // In original code, it echoes error or returns response.
        }

        $response = json_decode($resp);
        // Original code has return logic inside catch.
        return $response;
    }
}

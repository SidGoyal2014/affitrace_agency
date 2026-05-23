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
                's.id as sub_id',
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
        $subscription = Subscription::find($id);
        if (!$subscription) {
            return ['success' => false, 'msg' => 'Subscription not found.', 'code' => 404];
        }

        $lastStatus = SubscriptionStatusDetail::where('subscription_id', $id)->latest('id')->first();
        $stripeSubscriptionId = $lastStatus->stripe_subscription_id ?? $subscription->stripe_subscription_id;

        if (empty($stripeSubscriptionId)) {
            return ['success' => false, 'msg' => 'Stripe subscription id not found.', 'code' => 422];
        }

        $postData = array();
        $postData['subscription_id'] = $id;
        $postData['stripe_subscription_id'] = $stripeSubscriptionId;

        $url = env('MAIN_WEBSITE') . "/api/pause-subscription";
        $response = $this->send_data($postData, $url);

        if ($this->isSuccessfulGatewayResponse($response)) {
            $subscription->status = "Paused";
            $subscription->save();

            $objSSD = new SubscriptionStatusDetail();
            $objSSD->subscription_id = $id;
            $objSSD->stripe_subscription_id = $stripeSubscriptionId;
            $objSSD->status = "Paused";
            $objSSD->save();

            return ['success' => true, 'msg' => 'Subscription paused successfully', 'code' => 200];
        } else {
            if ($this->isNoSuchSubscriptionResponse($response)) {
                return [
                    'success' => false,
                    'msg' => 'This subscription does not exist in the connected payment gateway. Please verify gateway environment/keys for this record.',
                    'code' => 404
                ];
            }

            return [
                'success' => false,
                'msg' => $this->extractGatewayMessage($response, 'Subscription could not be updated.'),
                'code' => $this->extractGatewayCode($response, 500)
            ];
        }
    }

    public function resumeSubscription($id)
    {
        $subscription = Subscription::find($id);
        if (!$subscription) {
            return ['success' => false, 'msg' => 'Subscription not found.', 'code' => 404];
        }

        $lastStatus = SubscriptionStatusDetail::where('subscription_id', $id)->latest('id')->first();
        $stripeSubscriptionId = $lastStatus->stripe_subscription_id ?? $subscription->stripe_subscription_id;

        if (empty($stripeSubscriptionId)) {
            return ['success' => false, 'msg' => 'Stripe subscription id not found.', 'code' => 422];
        }

        $postData = array();
        $postData['subscription_id'] = $id;
        $postData['stripe_subscription_id'] = $stripeSubscriptionId;

        $url = env('MAIN_WEBSITE') . "/api/resume-subscription";
        $response = $this->send_data($postData, $url);

        if ($this->isSuccessfulGatewayResponse($response)) {
            $subscription->status = "Resumed";
            $subscription->save();

            $objSSD = new SubscriptionStatusDetail();
            $objSSD->subscription_id = $id;
            $objSSD->stripe_subscription_id = $stripeSubscriptionId;
            $objSSD->status = "Resumed";
            $objSSD->save();

            return ['success' => true, 'msg' => 'Subscription resumed successfully', 'code' => 200];
        } else {
            if ($this->isNoSuchSubscriptionResponse($response)) {
                return [
                    'success' => false,
                    'msg' => 'This subscription does not exist in the connected payment gateway. Please verify gateway environment/keys for this record.',
                    'code' => 404
                ];
            }

            return [
                'success' => false,
                'msg' => $this->extractGatewayMessage($response, 'Subscription could not be updated.'),
                'code' => $this->extractGatewayCode($response, 500)
            ];
        }
    }

    public function deleteSubscription($id)
    {
        $subscription = Subscription::find($id);
        if (!$subscription) {
            return ['success' => false, 'msg' => 'Subscription not found.', 'code' => 404];
        }

        $lastStatus = SubscriptionStatusDetail::where('subscription_id', $id)->latest('id')->first();
        $stripeSubscriptionId = $lastStatus->stripe_subscription_id ?? $subscription->stripe_subscription_id;

        if (empty($stripeSubscriptionId)) {
            return ['success' => false, 'msg' => 'Stripe subscription id not found.', 'code' => 422];
        }

        $postData = array();
        $postData['subscription_id'] = $id;
        $postData['stripe_subscription_id'] = $stripeSubscriptionId;

        $url = env('MAIN_WEBSITE') . "/api/delete-subscription";
        $response = $this->send_data($postData, $url);

        if ($this->isSuccessfulGatewayResponse($response)) {
            $subscription->status = "Deleted";
            $subscription->save();

            $objSSD = new SubscriptionStatusDetail();
            $objSSD->subscription_id = $id;
            $objSSD->stripe_subscription_id = $stripeSubscriptionId;
            $objSSD->status = "Deleted";
            $objSSD->save();

            return ['success' => true, 'msg' => 'Subscription deleted successfully', 'code' => 200];
        } else {
            if ($this->isNoSuchSubscriptionResponse($response)) {
                $subscription->status = "Deleted";
                $subscription->save();

                $objSSD = new SubscriptionStatusDetail();
                $objSSD->subscription_id = $id;
                $objSSD->stripe_subscription_id = $stripeSubscriptionId;
                $objSSD->status = "Deleted";
                $objSSD->save();

                return [
                    'success' => true,
                    'msg' => 'Subscription not found in gateway; marked as Deleted locally.',
                    'code' => 200
                ];
            }

            return [
                'success' => false,
                'msg' => $this->extractGatewayMessage($response, 'Subscription could not be updated.'),
                'code' => $this->extractGatewayCode($response, 500)
            ];
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $resp = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            curl_close($ch);
            return (object) [
                'success' => false,
                'message' => !empty($curlError) ? $curlError : 'Unable to connect to subscription gateway.',
                'http_code' => $httpCode
            ];
        }

        curl_close($ch);

        $response = json_decode($resp);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $rawText = $this->formatGatewayRawPreview($resp);
            return (object) [
                'success' => ($httpCode >= 200 && $httpCode < 300),
                'message' => 'Gateway returned non-JSON response (HTTP ' . $httpCode . '). ' . $rawText,
                'raw' => $resp,
                'http_code' => $httpCode
            ];
        }

        if (is_array($response)) {
            $response = (object) $response;
        }

        $response->http_code = $httpCode;
        return $response;
    }

    private function isSuccessfulGatewayResponse($response)
    {
        if (!$response) {
            return false;
        }

        $success = $response->success ?? $response->status ?? null;

        if (is_bool($success)) {
            return $success;
        }

        if (is_numeric($success)) {
            return (int) $success === 1;
        }

        if (is_string($success)) {
            $normalized = strtolower(trim($success));
            if (in_array($normalized, ['y', 'yes', 'true', '1', 'success', 'ok'], true)) {
                return true;
            }
            if (in_array($normalized, ['n', 'no', 'false', '0', 'failed', 'error'], true)) {
                return false;
            }
        }

        $code = $this->extractGatewayCode($response, 0);
        return ($code >= 200 && $code < 300);
    }

    private function extractGatewayMessage($response, $default)
    {
        if (!$response) {
            return $default;
        }

        foreach (['msg', 'message', 'error', 'detail'] as $field) {
            if (isset($response->{$field}) && is_string($response->{$field}) && trim($response->{$field}) !== '') {
                return html_entity_decode(trim($response->{$field}), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        if (isset($response->errors)) {
            if (is_string($response->errors) && trim($response->errors) !== '') {
                return html_entity_decode(trim($response->errors), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            if (is_array($response->errors)) {
                return json_encode($response->errors);
            }
        }

        return $default;
    }

    private function extractGatewayCode($response, $default)
    {
        if (!$response) {
            return $default;
        }

        foreach (['code', 'status_code', 'http_code'] as $field) {
            if (isset($response->{$field}) && is_numeric($response->{$field})) {
                $code = (int) $response->{$field};
                if ($code >= 100 && $code <= 599) {
                    return $code;
                }
            }
        }

        return $default;
    }

    private function formatGatewayRawPreview($raw)
    {
        $raw = (string) $raw;
        if (trim($raw) === '') {
            return 'Empty response body.';
        }

        $title = null;
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $raw, $matches) === 1) {
            $title = trim(strip_tags($matches[1]));
        }

        $text = trim(preg_replace('/\s+/', ' ', strip_tags($raw)));
        if ($text === '') {
            $text = trim(preg_replace('/\s+/', ' ', $raw));
        }

        if ($title) {
            $preview = 'Page title: ' . $title;
            if ($text !== '' && stripos($text, $title) === false) {
                $preview .= ' | Body: ' . mb_substr($text, 0, 220);
            }
            return $preview;
        }

        return 'Body: ' . mb_substr($text, 0, 240);
    }

    private function isNoSuchSubscriptionResponse($response)
    {
        $message = strtolower($this->extractGatewayMessage($response, ''));
        return $message !== '' && strpos($message, 'no such subscription') !== false;
    }
}

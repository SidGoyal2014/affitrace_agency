<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\PostbackSimulatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PostbackSimulatorController extends Controller
{
    protected PostbackSimulatorService $service;

    public function __construct(PostbackSimulatorService $service)
    {
        $this->service = $service;
    }

    // GET /api/admin/tools/postback-simulator/recent-clicks
    public function recentClicks(Request $request)
    {
        $limit = min((int) $request->get('limit', 50), 200);
        return response()->json([
            'status' => 1,
            'data'   => $this->service->getRecentClicks($limit),
        ]);
    }

    // GET /api/admin/tools/postback-simulator/recent-conversions
    public function recentConversions(Request $request)
    {
        $limit = min((int) $request->get('limit', 50), 200);
        return response()->json([
            'status' => 1,
            'data'   => $this->service->getRecentConversions($limit),
        ]);
    }

    // POST /api/admin/tools/postback-simulator/fire-advertiser-postback
    // Body: { aff_click_id*, event_id, sale, currency, adv_sub1-5, status }
    public function fireAdvertiserPostback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'aff_click_id' => 'required|string|max:255',
            'event_id'     => 'nullable|string|max:100',
            'sale'         => 'nullable|numeric|min:0',
            'currency'     => 'nullable|string|max:10',
            'adv_sub1'     => 'nullable|string|max:255',
            'adv_sub2'     => 'nullable|string|max:255',
            'adv_sub3'     => 'nullable|string|max:255',
            'adv_sub4'     => 'nullable|string|max:255',
            'adv_sub5'     => 'nullable|string|max:255',
            'status'       => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);
        }

        $params = $request->only([
            'aff_click_id', 'event_id', 'sale', 'currency',
            'adv_sub1', 'adv_sub2', 'adv_sub3', 'adv_sub4', 'adv_sub5', 'status',
        ]);

        $result = $this->service->fireAdvertiserPostback($params);

        return response()->json([
            'status' => $result['success'] ? 1 : 0,
            'data'   => $result,
        ]);
    }

    // POST /api/admin/tools/postback-simulator/fire-affiliate-postback
    // Body: { conversion_id*, dry_run }
    public function fireAffiliatePostback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conversion_id' => 'required|integer|min:1',
            'dry_run'       => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);
        }

        $result = $this->service->fireAffiliatePostback(
            (int) $request->conversion_id,
            (bool) $request->get('dry_run', false)
        );

        return response()->json([
            'status' => $result['success'] ? 1 : 0,
            'data'   => $result,
        ]);
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\FraudService;
use App\Services\Admin\FraudDashboardService;

class FraudController extends Controller
{
    protected FraudService $fraudService;
    protected FraudDashboardService $dashboardService;

    public function __construct(FraudService $fraudService, FraudDashboardService $dashboardService)
    {
        $this->fraudService     = $fraudService;
        $this->dashboardService = $dashboardService;
    }

    // ─── Dashboard ────────────────────────────────────────────────────

    public function dashboard(Request $request)
    {
        $result = $this->dashboardService->getDashboardStats();
        return response()->json($result, $result['code'] ?? 200);
    }

    public function alerts(Request $request)
    {
        $limit  = (int) $request->get('limit', 50);
        $page   = (int) $request->get('page', 1);
        $result = $this->dashboardService->getAlerts($limit, $page);
        return response()->json($result, $result['code'] ?? 200);
    }

    public function clickScores(Request $request)
    {
        $result = $this->dashboardService->getClickScores($request->all());
        return response()->json($result, $result['code'] ?? 200);
    }

    public function bulkBlock(Request $request)
    {
        $type   = $request->input('type');
        $values = $request->input('values', []);
        $result = $this->dashboardService->bulkBlock($type, (array) $values, $request->user()->id);
        return response()->json($result, $result['code'] ?? 200);
    }

    // ─── Rules ────────────────────────────────────────────────────────

    public function listRules(Request $request)
    {
        $result = $this->fraudService->listRules();
        return response()->json($result, $result['code'] ?? 200);
    }

    public function getRule(int $id)
    {
        $result = $this->fraudService->getRule($id);
        return response()->json($result, $result['code'] ?? 200);
    }

    public function createRule(Request $request)
    {
        $result = $this->fraudService->createRule($request->all(), $request->user()->id);
        return response()->json($result, $result['code'] ?? 200);
    }

    public function updateRule(Request $request, int $id)
    {
        $result = $this->fraudService->updateRule($id, $request->all());
        return response()->json($result, $result['code'] ?? 200);
    }

    public function deleteRule(int $id)
    {
        $result = $this->fraudService->deleteRule($id);
        return response()->json($result, $result['code'] ?? 200);
    }

    public function toggleRule(int $id)
    {
        $result = $this->fraudService->toggleRule($id);
        return response()->json($result, $result['code'] ?? 200);
    }

    // ─── IP Blacklist ─────────────────────────────────────────────────

    public function listIpBlacklist()
    {
        $result = $this->fraudService->listIpBlacklist();
        return response()->json($result, $result['code'] ?? 200);
    }

    public function addIpBlacklist(Request $request)
    {
        $result = $this->fraudService->addIpBlacklist($request->all(), $request->user()->id);
        return response()->json($result, $result['code'] ?? 200);
    }

    public function importIpBlacklist(Request $request)
    {
        $ips    = $request->input('ips', []);
        $result = $this->fraudService->importIpBlacklist((array) $ips, $request->user()->id);
        return response()->json($result, $result['code'] ?? 200);
    }

    public function removeIpBlacklist(int $id)
    {
        $result = $this->fraudService->removeIpBlacklist($id);
        return response()->json($result, $result['code'] ?? 200);
    }

    // ─── IP Whitelist ─────────────────────────────────────────────────

    public function listIpWhitelist()
    {
        $result = $this->fraudService->listIpWhitelist();
        return response()->json($result, $result['code'] ?? 200);
    }

    public function addIpWhitelist(Request $request)
    {
        $result = $this->fraudService->addIpWhitelist($request->all(), $request->user()->id);
        return response()->json($result, $result['code'] ?? 200);
    }

    public function removeIpWhitelist(int $id)
    {
        $result = $this->fraudService->removeIpWhitelist($id);
        return response()->json($result, $result['code'] ?? 200);
    }

    // ─── Device Blacklist ─────────────────────────────────────────────

    public function listDeviceBlacklist()
    {
        $result = $this->fraudService->listDeviceBlacklist();
        return response()->json($result, $result['code'] ?? 200);
    }

    public function addDeviceBlacklist(Request $request)
    {
        $result = $this->fraudService->addDeviceBlacklist($request->all(), $request->user()->id);
        return response()->json($result, $result['code'] ?? 200);
    }

    public function removeDeviceBlacklist(int $id)
    {
        $result = $this->fraudService->removeDeviceBlacklist($id);
        return response()->json($result, $result['code'] ?? 200);
    }

    // ─── Third-Party Config ───────────────────────────────────────────

    public function getThirdPartyConfig()
    {
        $result = $this->fraudService->getThirdPartyConfig();
        return response()->json($result, $result['code'] ?? 200);
    }

    public function updateThirdPartyConfig(Request $request)
    {
        $result = $this->fraudService->updateThirdPartyConfig($request->all());
        return response()->json($result, $result['code'] ?? 200);
    }
}

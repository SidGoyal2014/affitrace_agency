<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RedirectCheck;
use App\Services\RedirectCheckerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RedirectCheckerController extends Controller
{
    public function __construct(private RedirectCheckerService $service) {}

    /**
     * GET /api/admin/redirect-checker
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        $checks = RedirectCheck::orderByDesc('id')->paginate($perPage);

        $checks->getCollection()->transform(function (RedirectCheck $item) {
            return [
                'id'            => $item->id,
                'checked_on'    => $item->created_at?->format('Y-m-d H:i:s'),
                'url'           => $item->input_url,
                'final_url'     => $item->final_url,
                'hops'          => $item->hop_count,
                'device'        => $item->device_label,
                'status'        => $item->status,
                'error_message' => $item->error_message,
            ];
        });

        return response()->json(['success' => true, 'data' => $checks]);
    }

    /**
     * POST /api/admin/redirect-checker/check
     */
    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url'    => 'required|url|max:2048',
            'proxy'  => 'nullable|string|max:500',   // accepts host:port:user:pass OR http://user:pass@host:port
            'device' => 'nullable|string|in:' . implode(',', array_keys(RedirectCheck::DEVICES)),
        ]);

        $check = $this->service->trace(
            $validated['url'],
            $validated['proxy']  ?? null,
            $validated['device'] ?? 'desktop_windows'
        );

        return response()->json([
            'success' => true,
            'message' => $check->status === RedirectCheck::STATUS_COMPLETED
                ? 'Redirect trace completed.'
                : 'Trace failed: ' . $check->error_message,
            'data'    => $this->formatDetail($check),
        ]);
    }

    /**
     * POST /api/admin/redirect-checker/test-proxy
     */
    public function testProxy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'proxy' => 'required|string|max:500',
        ]);

        try {
            $result = $this->service->testProxy($validated['proxy']);
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proxy test failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * GET /api/admin/redirect-checker/{id}
     */
    public function show(int $id): JsonResponse
    {
        $check = RedirectCheck::findOrFail($id);

        return response()->json(['success' => true, 'data' => $this->formatDetail($check)]);
    }

    /**
     * DELETE /api/admin/redirect-checker/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        RedirectCheck::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Deleted.']);
    }

    /**
     * GET /api/admin/redirect-checker/devices
     */
    public function devices(): JsonResponse
    {
        $list = array_map(
            fn($k, $v) => ['value' => $k, 'label' => $v],
            array_keys(RedirectCheck::DEVICES),
            array_values(RedirectCheck::DEVICES)
        );

        return response()->json(['success' => true, 'data' => $list]);
    }

    private function formatDetail(RedirectCheck $check): array
    {
        return [
            'id'            => $check->id,
            'checked_on'    => $check->created_at?->format('Y-m-d H:i:s'),
            'input_url'     => $check->input_url,
            'final_url'     => $check->final_url,
            'hop_count'     => $check->hop_count,
            'device'        => $check->device,
            'device_label'  => $check->device_label,
            'proxy_display' => $check->proxy ? $this->maskProxy($check->proxy) : null,
            'status'        => $check->status,
            'error_message' => $check->error_message,
            'hops'          => $check->hops ?? [],
        ];
    }

    /**
     * Return a proxy string safe for display — credentials replaced with ***.
     * host:port:user:pass  → host:port:***:***
     * http://user:pass@h:p → http://***:***@h:p
     */
    private function maskProxy(string $proxy): string
    {
        // URL form: strip user:pass from authority
        if (preg_match('#^(https?|socks5)://#i', $proxy)) {
            return preg_replace('#(https?|socks5)://[^@]+@#i', '$1://***:***@', $proxy);
        }

        // host:port:user:pass form
        $parts = explode(':', $proxy, 4);
        if (count($parts) === 4) {
            return $parts[0] . ':' . $parts[1] . ':***:***';
        }

        // host:port — no credentials to mask
        return $proxy;
    }    
    
}
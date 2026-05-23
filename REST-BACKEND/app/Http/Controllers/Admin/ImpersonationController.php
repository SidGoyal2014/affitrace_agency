<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Admin\ImpersonationService;

class ImpersonationController extends Controller
{
    protected $impersonationService;

    public function __construct(ImpersonationService $impersonationService)
    {
        $this->impersonationService = $impersonationService;
    }

    /**
     * Start impersonating a user (affiliate or advertiser).
     */
    public function start(Request $request, $userId)
    {
        $result = $this->impersonationService->startImpersonation(Auth::user(), $userId, $request->ip());

        if (!$result['status']) {
            return response()->json(['message' => $result['message']], $result['code']);
        }

        return response()->json([
            'message'       => $result['message'],
            'token'         => $result['token'],
            'token_type'    => $result['token_type'],
            'user'          => $result['user'],
            'expires_in'    => $result['expires_in'],
            'log_id'        => $result['log_id'],
            'impersonator'  => $result['impersonator'],
        ]);
    }

    /**
     * Stop impersonating a user.
     */
    public function stop(Request $request)
    {
        $user = Auth::user();
        $token = $user->currentAccessToken();

        $result = $this->impersonationService->stopImpersonation($user, $token);

        if (!$result['status']) {
            return response()->json(['message' => $result['message']], $result['code']);
        }

        return response()->json(['message' => $result['message']]);
    }

    /**
     * List impersonation audit logs.
     */
    public function logs(Request $request)
    {
        $filters = $request->only(['impersonator_id', 'impersonated_id']);
        $logs = $this->impersonationService->getLogs($filters);

        return response()->json($logs);
    }
}

<?php

namespace App\Http\Middleware;

use Closure, Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AffiliateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //return $next($request);

        if (strtolower(Auth::user()->user_type) != \App\Enums\UserType::Affiliate->value) {
            \Illuminate\Support\Facades\Log::info('AffiliateMiddleware: Access Denied', [
                'user_id' => Auth::id(),
                'user_type' => Auth::user()->user_type,
                'expected' => \App\Enums\UserType::Affiliate->value,
                'raw_user' => Auth::user()->toArray()
            ]);

            // Revoke the token if it's not transient
            $accessToken = $request->user()->currentAccessToken();
            if ($accessToken && !($accessToken instanceof \Laravel\Sanctum\TransientToken)) {
                $accessToken->delete();
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthorised attempt.',
                    'debug_db_connection' => \Illuminate\Support\Facades\DB::connection()->getDatabaseName(),
                    'debug_user_connection' => \Illuminate\Support\Facades\Auth::user()->getConnectionName(),
                    'debug_user_type' => \Illuminate\Support\Facades\Auth::user()->user_type,
                    'debug_user_profile' => \Illuminate\Support\Facades\Auth::user()->toArray()
                ], 403);
            }

            return redirect()->to('/login')->withErrors(['message' => 'Unauthorised attempt.']);
        }

        return $next($request);
    }
}

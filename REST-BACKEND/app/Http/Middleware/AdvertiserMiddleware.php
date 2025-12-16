<?php

namespace App\Http\Middleware;

use Closure, Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdvertiserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //return $next($request);

        if (strtolower(Auth::user()->user_type) != 'advertiser') {

            // Revoke the token if it's not transient
            $accessToken = $request->user()->currentAccessToken();
            if ($accessToken && !($accessToken instanceof \Laravel\Sanctum\TransientToken)) {
                $accessToken->delete();
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthorised attempt.'], 403);
            }

            return redirect()->to('/login')->withErrors(['message' => 'Unauthorised attempt.']);
        }

        return $next($request);
    }
}

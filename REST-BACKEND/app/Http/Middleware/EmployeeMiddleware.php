<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //return $next($request);

        $user = Auth::user();
        if (!$user || !in_array($user->user_type, [\App\Enums\UserType::Admin->value, \App\Enums\UserType::Employee->value])) {

            // Revoke the token if it's not transient
            if ($user) {
                $accessToken = $request->user()->currentAccessToken();
                if ($accessToken && !($accessToken instanceof \Laravel\Sanctum\TransientToken)) {
                    $accessToken->delete();
                }
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthorised attempt.'], 403);
            }

            return redirect()->to('/login')->withErrors(['message' => 'Unauthorised attempt.']);

        }

        return $next($request);
    }
}

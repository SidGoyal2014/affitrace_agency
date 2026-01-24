<?php

namespace App\Http\Middleware;

use Closure, Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //return $next($request);

        if (Auth::user()->user_type != 'admin') {

            Auth::logout();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthorised attempt.'], 401);
            }

            return redirect()->to('/login')->withErrors(['message' => 'Unauthorised attempt.']);

        }

        return $next($request);

    }
}

<?php

namespace App\Http\Middleware;

use Closure;
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

	   	if(Auth::user()->user_type != 'employee'){

			Auth::logout();

			return redirect()->to('/login')->withErrors(['message'=>'Unauthorised attempt.']);

	   	}

		return $next($request);
    }
}

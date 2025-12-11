<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */



	/*
    protected function redirectTo($request)
    {

		$prefix = $request->route()->getPrefix();

        if (! $request->expectsJson()) {

			return 'login';

        }
    }

	*/

    protected function redirectTo(Request $request): ?string
    {

        \Log::debug('Redirecting due to unauthenticated');
        \Log::debug('Session:', session()->all());
        \Log::debug('Auth check: ' . (Auth::check() ? 'YES' : 'NO'));
        \Log::debug('Current DB connection: ' . DB::getDefaultConnection());

        // Temporarily remove die to see logs
        // print_r($request->all());
        // die("Died Here");
        return $request->expectsJson() ? null : 'login';
		//return $request->expectsJson() ? null : route('login');
    }

}

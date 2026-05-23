<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes: middleware('module.access:offers,can_view')
     *
     * @param  string  $moduleSlug  The module slug to check
     * @param  string  $action      The permission action (can_view, can_create, can_edit, can_delete)
     */
    public function handle(Request $request, Closure $next, string $moduleSlug, string $action = 'can_view'): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Admin always has full access
        if (strtolower($user->user_type) === 'admin') {
            return $next($request);
        }

        if (!$user->hasModuleAccess($moduleSlug, $action)) {
            return response()->json([
                'message' => 'You do not have permission to access this module.',
                'module'  => $moduleSlug,
                'action'  => $action,
            ], 403);
        }

        return $next($request);
    }
}

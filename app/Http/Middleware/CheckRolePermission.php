<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRolePermission
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes: ->middleware('role.permission:patients')
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->canAccess($feature)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Access denied.'], 403);
            }

            return redirect()->route($user->homeRoute())
                ->with('error', 'You do not have permission to access that feature.');
        }

        return $next($request);
    }
}

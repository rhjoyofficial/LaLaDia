<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures the authenticated user holds at least one admin-level role.
 *
 * Any role other than "Customer" is considered admin-level. This avoids
 * hard-coding every role name and automatically picks up new roles added
 * to RoleSeeder in the future.
 */
class EnsureUserIsAdmin
{
    /** Roles that are NOT allowed through the admin gate. */
    private const EXCLUDED_ROLES = ['Customer'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->guest(route('admin.login'));
        }

        // Reject deactivated accounts even if a valid session/token exists.
        if (! ($user->is_active ?? true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Account deactivated.'], 403);
            }
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            return redirect()->route('admin.login')->withErrors(['login' => 'Your account has been deactivated.']);
        }

        // User must have at least one role that is not in the excluded list.
        $hasAdminRole = $user->roles
            ->pluck('name')
            ->diff(self::EXCLUDED_ROLES)
            ->isNotEmpty();

        if (! $hasAdminRole) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }

            return redirect('/');
        }

        return $next($request);
    }
}

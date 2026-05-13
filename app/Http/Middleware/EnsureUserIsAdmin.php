<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures the authenticated user holds at least one explicitly-defined admin role.
 *
 * Uses an allowlist rather than a blocklist so that newly-seeded roles are
 * denied by default until they are deliberately granted admin access here.
 */
class EnsureUserIsAdmin
{
    /** Roles that ARE allowed through the admin gate. */
    private const ALLOWED_ROLES = [
        'Super Admin',
        'Admin',
        'Order Manager',
        'Inventory Clerk',
        'Marketing',
        'Customer Support'
    ];

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

        // User must have at least one role from the explicit allowlist.
        $hasAdminRole = $user->roles
            ->pluck('name')
            ->intersect(self::ALLOWED_ROLES)
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

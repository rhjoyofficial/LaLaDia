<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Dev-only sources — Vite HMR websocket + dev server. Stripped in production
        // so the production CSP doesn't expose a loopback bypass.
        $devSources = app()->environment('local')
            ? " http://127.0.0.1:* http://localhost:* ws://127.0.0.1:* ws://localhost:*"
            : '';

        // Admin panel uses the standard Alpine.js CDN build, which internally calls
        // new Function() to evaluate expressions — this requires 'unsafe-eval'.
        // We scope this to admin routes only to keep the storefront CSP strict.
        $isAdminRoute = $request->is('admin/*') || $request->is('admin');
        $evalDirective = $isAdminRoute ? " 'unsafe-eval'" : '';

        // 'unsafe-inline' is required while inline <script> blocks and Alpine.js
        // x-on handlers exist. Migrate to nonce-based CSP to remove it long-term.
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline'{$evalDirective} https://www.googletagmanager.com https://*.googletagmanager.com https://www.google-analytics.com https://*.google-analytics.com https://connect.facebook.net https://cdn.jsdelivr.net https://www.youtube.com https://s.ytimg.com https://player.vimeo.com{$devSources}",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net{$devSources}",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:",
            "img-src 'self' data: blob: https: https://*.googletagmanager.com https://*.google-analytics.com",
            "media-src 'self' blob:",
            "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com https://player.vimeo.com",
            "connect-src 'self' https://www.google-analytics.com https://*.google-analytics.com https://analytics.google.com https://*.analytics.google.com https://*.googletagmanager.com https://stats.g.doubleclick.net https://www.google.com https://www.facebook.com https://www.youtube.com https://cdn.jsdelivr.net https://mpc2-prod-28-is5qnl632q-ue.a.run.app https://demo-1.conversionsapigateway.com{$devSources}",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $response;
    }
}

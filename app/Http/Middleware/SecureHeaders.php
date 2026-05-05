<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // HSTS: only send over HTTPS in production to avoid breaking dev over HTTP
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Content-Security-Policy: restricts what resources the browser may load.
        // 'unsafe-inline' is required for Alpine.js and inline <script> blocks.
        // 'unsafe-eval' is required for some chart/template libraries.
        // Tighten per-directive once inline scripts are migrated to nonce-based CSP.
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com https://connect.facebook.net https://www.google-analytics.com https://cdn.jsdelivr.net https://www.youtube.com https://s.ytimg.com https://player.vimeo.com http://127.0.0.1:* http://localhost:*",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net http://127.0.0.1:* http://localhost:*",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
            "img-src 'self' data: blob: https:",
            "media-src 'self' blob:",
            "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com https://player.vimeo.com",
            "connect-src 'self' https://www.google-analytics.com https://analytics.google.com https://www.facebook.com https://www.youtube.com ws://127.0.0.1:* ws://localhost:* http://127.0.0.1:* http://localhost:*",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // Prevent browsers from silently upgrading insecure subresource requests
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $response;
    }
}

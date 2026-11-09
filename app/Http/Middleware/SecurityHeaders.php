<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Hardens every HTML/JSON response served by the storefront.
 *
 * - blocks clickjacking (frame-ancestors / X-Frame-Options)
 * - stops MIME-type sniffing
 * - limits referrer leakage
 * - disables unused browser capabilities
 * - enforces HTTPS on repeat visits (HSTS)
 * - CSP that allows only first-party scripts/styles plus the inline
 *   scripts the Blade views already use; everything else is blocked
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $csp = implode('; ', [
            "default-src 'self'",
            // js.stripe.com / www.paypal.com are only loaded on /checkout when a
            // real gateway key is configured; inline scripts are used by the theme
            "script-src 'self' 'unsafe-inline' https://js.stripe.com https://www.paypal.com",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data:",
            "font-src 'self' data:",
            "connect-src 'self' https://api.stripe.com",
            "form-action 'self'",
            "frame-ancestors 'self'",
            "frame-src https://js.stripe.com https://www.paypal.com",
            "base-uri 'self'",
            "object-src 'none'",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}

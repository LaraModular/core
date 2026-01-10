<?php

namespace LaraModule\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        /*
         |------------------------------------------------------------------
         | Strict Transport Security (HSTS)
         |------------------------------------------------------------------
         | Enforces HTTPS at browser level.
         */
        if (app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        /*
         |------------------------------------------------------------------
         | Clickjacking Protection
         |------------------------------------------------------------------
         */
        $response->headers->set(
            'X-Frame-Options',
            'DENY'
        );

        /*
         |------------------------------------------------------------------
         | MIME Sniffing Protection
         |------------------------------------------------------------------
         */
        $response->headers->set(
            'X-Content-Type-Options',
            'nosniff'
        );

        /*
         |------------------------------------------------------------------
         | Referrer Policy
         |------------------------------------------------------------------
         */
        $response->headers->set(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );

        /*
         |------------------------------------------------------------------
         | Content Security Policy (CSP)
         |------------------------------------------------------------------
         | This CSP is designed for:
         */
        $csp = [
            "default-src 'self'",

            // React / Vite scripts
            "script-src 'self'",

            // Tailwind + inline styles
            "style-src 'self' 'unsafe-inline'",

            // Images (allow data: for inline SVGs, icons, etc.)
            "img-src 'self' data:",

            // API calls, WebSockets
            "connect-src 'self'",

            // Fonts (local or bundled)
            "font-src 'self'",

            // Disallow <iframe> usage entirely
            "frame-ancestors 'none'",

            // Prevent <base> tag abuse
            "base-uri 'self'",

            // Prevent form submissions to other origins
            "form-action 'self'",
        ];

        $response->headers->set(
            'Content-Security-Policy',
            implode('; ', $csp)
        );

        return $response;
    }
}

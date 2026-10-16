<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security headers on every response (security audit, Verification No. I, item 4):
 * a Content-Security-Policy built on a per-request nonce, without 'unsafe-inline'
 * or 'unsafe-eval', plus X-XSS-Protection "1; mode=block" and the usual hardening
 * headers. The nonce is shared with Vite and Filament/Livewire asset tags through
 * Vite::useCspNonce(); Blade templates use it as {{ Vite::cspNonce() }}.
 *
 * Extra sources per directive come from config/security.php.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Vite::useCspNonce();

        $response = $next($request);

        $directives = [
            'default-src' => ["'self'"],
            'script-src' => ["'self'", "'nonce-{$nonce}'"],
            'style-src' => ["'self'", "'nonce-{$nonce}'"],
            'img-src' => ["'self'", 'data:'],
            'font-src' => ["'self'", 'data:'],
            'connect-src' => ["'self'"],
            'frame-src' => ["'self'"],
            'form-action' => ["'self'"],
            'object-src' => ["'none'"],
            'base-uri' => ["'self'"],
            'frame-ancestors' => ["'self'"],
        ];
        $extra = config('security.csp', []);
        // a page may embed another site (CMS > Pages > Special content): allow just that host
        $extra['frame-src'] = array_merge($extra['frame-src'] ?? [], (array) $request->attributes->get('csp.frame-src', []));
        foreach ($extra as $directive => $sources) {
            $directives[$directive] = array_values(array_unique(array_merge($directives[$directive] ?? [], (array) $sources)));
        }

        $policy = [];
        foreach ($directives as $directive => $sources) {
            $policy[] = $directive.' '.implode(' ', $sources);
        }
        if ($request->isSecure()) {
            $policy[] = 'upgrade-insecure-requests';
        }

        $headers = $response->headers;
        $headers->set('Content-Security-Policy', implode('; ', $policy));
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-XSS-Protection', '1; mode=block');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $headers->remove('X-Powered-By');
        if ($request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}

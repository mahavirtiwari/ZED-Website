<?php

namespace App\Http\Middleware;

use App\Support\HtmlHardener;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies App\Support\HtmlHardener to HTML responses.
 * Route middleware parameters: "harden:public" (obfuscate e-mail addresses) or
 * "harden:cms" (autocomplete off on forms and password fields).
 */
class HardenHtml
{
    public function handle(Request $request, Closure $next, string $profile = 'public'): Response
    {
        $response = $next($request);

        $type = (string) $response->headers->get('Content-Type', 'text/html');
        $content = $response->getContent();
        if (! str_contains($type, 'html') || ! is_string($content) || $content === '' || method_exists($response, 'getFile')) {
            return $response;
        }

        $hardener = new HtmlHardener(
            obfuscateEmails: $profile === 'public',
            autocompleteOff: $profile === 'cms',
        );
        $response->setContent($hardener->harden($content));

        return $response;
    }
}

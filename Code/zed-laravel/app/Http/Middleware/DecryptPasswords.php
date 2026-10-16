<?php

namespace App\Http\Middleware;

use App\Support\PasswordTransport;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Replaces "pwt1:..." values posted by public/js/zed-pwt.js with the decrypted
 * password before controllers and validation see the request. A value that
 * does not decrypt becomes '' so it can never match a real password.
 */
class DecryptPasswords
{
    public function __construct(private PasswordTransport $transport) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST')) {
            $changed = [];
            foreach ($request->request->all() as $field => $value) {
                $plain = $this->transport->decrypt($value);
                if ($plain !== null) {
                    $changed[$field] = $plain;
                }
            }
            if ($changed !== []) {
                $request->merge($changed);
                $this->transport->rotateNonce();
            }
        }

        return $next($request);
    }
}

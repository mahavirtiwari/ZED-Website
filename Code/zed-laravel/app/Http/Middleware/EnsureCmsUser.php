<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Signed-in CMS users only, with an optional role list: "cms.user" or
 * "cms.user:superadmin". An account that was deactivated or lost its role is
 * signed out at its next request.
 */
class EnsureCmsUser
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if ($user === null || ! $user->canUseCms()) {
            Auth::logout();

            return redirect()->guest(route('cms.login'));
        }
        abort_if($roles !== [] && ! $user->inGroup($roles), 403);

        return $next($request);
    }
}

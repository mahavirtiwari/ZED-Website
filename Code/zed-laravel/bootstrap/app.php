<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // security headers + CSP nonce on every response (security audit item 4)
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->alias([
            'harden' => \App\Http\Middleware\HardenHtml::class,
            'pwt' => \App\Http\Middleware\DecryptPasswords::class,
            'cms.user' => \App\Http\Middleware\EnsureCmsUser::class,
        ]);
        $middleware->redirectGuestsTo(fn () => route('cms.login'));
        $middleware->redirectUsersTo(fn () => route('cms.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

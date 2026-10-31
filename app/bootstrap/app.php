<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        if (env('RENDER', false)) {
            $middleware->trustProxies(at: '*', headers:
                \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
                \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT
            );
        }
        $middleware->alias(['active.account' => \App\Http\Middleware\EnsureActiveAccount::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

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
        $middleware->alias([
            'auth.user' => \Modules\AdminAuth\Http\Middleware\UserAuth::class,
            'auth.admin' => \Modules\AdminAuth\Http\Middleware\AdminAuth::class,
            'auth.agent' => \Modules\AdminAuth\Http\Middleware\AgentAuth::class,
            'auth.customer' => \Modules\AdminAuth\Http\Middleware\CustomerAuth::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

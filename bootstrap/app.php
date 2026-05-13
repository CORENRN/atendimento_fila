<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AuthenticateWithKeycloak;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckSuperAdmin;
use App\Http\Middleware\CheckAdminOrSuperAdmin;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware globais, se desejar
        // $middleware->global([
        //     AuthenticateWithKeycloak::class,
        // ]);

        // Middleware nomeados
        $middleware->alias([
            'auth.keycloak' => AuthenticateWithKeycloak::class,
            'admin' => CheckAdmin::class,
            'superAdmin' => CheckSuperAdmin::class,
            'has.guiche' => \App\Http\Middleware\EnsureUserHasGuiche::class,
            'adminOrSuper' => CheckAdminOrSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use \App\Http\Middleware\AuthenticateWithKeycloak;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function () {
        return [
            // Middleware global (opcional)
            // \App\Http\Middleware\AuthenticateWithKeycloak::class,

            // Middleware nomeado
            'auth.keycloak' => App\Http\Middleware\AuthenticateWithKeycloak::class,
        ];
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();


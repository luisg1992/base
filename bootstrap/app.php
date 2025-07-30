<?php

use App\Http\Middleware\VerificarPermisos;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',  // Asegúrate de tener el archivo api.php
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware global
        $middleware->append([
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\SessionTimeout::class,  
        ]);
        $middleware->alias([
            'verificar.permisos' => VerificarPermisos::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

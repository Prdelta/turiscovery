<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'socio.admin' => \App\Http\Middleware\EnsureSocioOrAdmin::class,
            'validate.params' => \App\Http\Middleware\ValidateRouteParameters::class,
            'api.cache' => \App\Http\Middleware\ApiQueryCacheMiddleware::class,
            'user.active' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);

        // Aplicar middleware a todas las rutas autenticadas
        $middleware->web(append: [
            \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

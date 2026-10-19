<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
            'validate.params' => \App\Http\Middleware\ValidateRouteParameters::class,
            'api.cache' => \App\Http\Middleware\ApiQueryCacheMiddleware::class,
            'user.active' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);

        // Sesión + CSRF en /api para el frontend React (Sanctum SPA)
        $middleware->statefulApi();
        $middleware->throttleApi();

        // Aplicar middleware a todas las rutas autenticadas
        $middleware->web(append: [
            \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Errores de la API con el mismo formato que las respuestas: { success: false, message, errors? }
        $isApi = fn (Request $request) => $request->is('api/*') || $request->expectsJson();

        $exceptions->render(function (ValidationException $e, Request $request) use ($isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() ?: 'Revisa los datos enviados.',
                'errors' => $e->errors(),
            ], $e->status);
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($isApi) {
            return $isApi($request)
                ? response()->json(['success' => false, 'message' => 'Inicia sesión para continuar.'], 401)
                : null;
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($isApi) {
            if (!$isApi($request)) {
                return null;
            }

            $status = $e->getStatusCode();
            $custom = $e->getMessage();
            // Mensajes genéricos en inglés de Laravel/Symfony: se reemplazan por uno en español.
            // Los propios (Policies, abort(403, '…')) ya vienen en español y se conservan.
            $isDefault = $custom === ''
                || in_array($custom, ['This action is unauthorized.', 'Unauthorized.', 'Forbidden', 'Not Found', 'Too Many Attempts.'], true)
                || str_starts_with($custom, 'No query results for model')
                || str_starts_with($custom, 'The route ');

            $message = match (true) {
                !$isDefault => $custom,
                $status === 403 => 'No tienes permiso para hacer esto.',
                $status === 404 => 'No encontramos lo que buscas.',
                $status === 405 => 'Método no permitido.',
                $status === 429 => 'Demasiadas solicitudes. Espera un momento e inténtalo otra vez.',
                default => $e->getMessage() ?: 'No se pudo completar la solicitud.',
            };

            return response()->json(['success' => false, 'message' => $message], $status, $e->getHeaders());
        });
    })->create();

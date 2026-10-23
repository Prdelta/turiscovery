<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

/*
 * Rutas que la SPA no usa y se retiraron: el login antiguo con tokens (la SPA entra con /session/*),
 * la ruta de prueba y las que duplicaban otra (toggle de favoritos, disponibilidad por días, etc.)
 */
/** Ruta que atiende la petición, o null si ninguna (ni la fallback de la web, que redirige a la SPA) */
function rutaQueAtiende(string $method, string $uri): ?string
{
    try {
        $route = Route::getRoutes()->match(Request::create($uri, $method));
    } catch (HttpException) {
        return null;
    }

    return $route->isFallback ? null : $route->uri();
}

test('las rutas retiradas ya no están registradas', function (string $method, string $url, string $removed) {
    // Se pregunta al router y no a un endpoint: un 404 por falta de datos no probaría nada
    expect(rutaQueAtiende($method, $url))->not->toBe($removed);
})->with([
    'login con token' => ['POST', '/api/login', 'api/login'],
    'logout con token' => ['POST', '/api/logout', 'api/logout'],
    'me con token' => ['GET', '/api/me', 'api/me'],
    'me de sesión' => ['GET', '/api/session/me', 'api/session/me'],
    'ruta de prueba' => ['GET', '/api/test', 'api/test'],
    // Ahora la toma /candelaria-danzas/{id}, que rechaza un id no numérico
    'danzas destacadas' => ['GET', '/api/candelaria-danzas/featured', 'api/candelaria-danzas/featured'],
    'comprobar disponibilidad' => ['POST', '/api/bookings/check-availability', 'api/bookings/check-availability'],
    'borrar favorito por id' => ['DELETE', '/api/favorites/1', 'api/favorites/{id}'],
    'editar asistencia por id' => ['PUT', '/api/event-attendances/1', 'api/event-attendances/{id}'],
]);

test('las danzas destacadas ya no responden como lista', function () {
    $this->getJson('/api/candelaria-danzas/featured')->assertStatus(400);
});

test('las rutas que usa la SPA siguen registradas', function (string $method, string $uri) {
    expect(Route::getRoutes()->match(Request::create($uri, $method))->uri())->toStartWith('api/');
})->with([
    ['POST', '/api/session/login'],
    ['POST', '/api/session/logout'],
    ['GET', '/api/session/check'],
    ['GET', '/api/candelaria-danzas/1'],
    ['GET', '/api/experiencias/1/availability'],
    ['POST', '/api/favorites/toggle'],
    ['DELETE', '/api/event-attendances/1'],
]);

test('el chequeo de salud de Laravel responde', function () {
    $this->get('/up')->assertOk();
});

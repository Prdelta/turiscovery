<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

/* La SPA usa la sesión de Laravel (cookie) a través de Sanctum, como en el navegador */
function sesionSpa($test)
{
    return $test->withHeaders(['Referer' => 'http://localhost:5173/', 'Origin' => 'http://localhost:5173']);
}

test('cerrar sesión termina la sesión en el servidor', function () {
    $user = User::factory()->create(['role' => 'tourist', 'is_active' => true]);
    $this->actingAs($user, 'web');

    sesionSpa($this)->postJson('/api/session/logout')->assertOk()->assertJsonPath('success', true);

    expect(Auth::guard('web')->guest())->toBeTrue();

    // Petición nueva, como la siguiente del navegador: sin guards con el usuario en memoria
    app('auth')->forgetGuards();
    sesionSpa($this)->getJson('/api/session/check')->assertOk()->assertJsonPath('authenticated', false);
});

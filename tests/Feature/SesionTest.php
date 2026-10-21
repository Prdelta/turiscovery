<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

/* La SPA usa la sesión de Laravel (cookie) a través de Sanctum, como en el navegador */
function sesionSpa($test)
{
    return $test->withHeaders(['Referer' => 'http://localhost:5173/', 'Origin' => 'http://localhost:5173']);
}

test('registrarse abre sesión sin emitir tokens de API', function () {
    sesionSpa($this)->postJson('/api/register', [
        'name' => 'Rosa Viajera',
        'email' => 'rosa@example.com',
        'password' => 'Clave-segura-123',
        'password_confirmation' => 'Clave-segura-123',
    ])->assertCreated()->assertJsonMissingPath('data.access_token');

    expect(Auth::guard('web')->user()?->email)->toBe('rosa@example.com')
        ->and(DB::table('personal_access_tokens')->count())->toBe(0);
});

test('cerrar sesión termina la sesión en el servidor', function () {
    $user = User::factory()->create(['role' => 'tourist', 'is_active' => true]);
    $this->actingAs($user, 'web');

    sesionSpa($this)->postJson('/api/session/logout')->assertOk()->assertJsonPath('success', true);

    expect(Auth::guard('web')->guest())->toBeTrue();

    // Petición nueva, como la siguiente del navegador: sin guards con el usuario en memoria
    app('auth')->forgetGuards();
    sesionSpa($this)->getJson('/api/session/check')->assertOk()->assertJsonPath('authenticated', false);
});

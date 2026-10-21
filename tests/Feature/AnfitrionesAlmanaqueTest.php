<?php

use App\Models\AlmanacEntry;
use App\Models\Anfitrion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function socio(): User
{
    return User::factory()->create(['role' => 'socio', 'is_active' => true]);
}

test('un socio no puede editar el anfitrión de otro socio', function () {
    $owner = socio();
    $host = Anfitrion::create(['user_id' => $owner->id, 'name' => 'Juana']);

    Sanctum::actingAs(socio());

    $this->putJson("/api/anfitriones/{$host->id}", ['name' => 'Otra'])->assertForbidden();
    expect($host->fresh()->name)->toBe('Juana');
});

test('un socio no puede asignar a su experiencia el anfitrión de otro socio', function () {
    $host = Anfitrion::create(['user_id' => socio()->id, 'name' => 'Juana']);
    Sanctum::actingAs(socio());

    $this->postJson('/api/experiencias', [
        'title' => 'Paseo',
        'description' => 'Paseo en balsa',
        'difficulty' => 'easy',
        'anfitrion_id' => $host->id,
    ])->assertUnprocessable()->assertJsonValidationErrors('anfitrion_id');
});

test('los anfitriones públicos solo muestran los activos y filtran por idioma', function () {
    $owner = socio();
    Anfitrion::create(['user_id' => $owner->id, 'name' => 'Juana', 'languages' => ['aimara', 'espanol']]);
    Anfitrion::create(['user_id' => $owner->id, 'name' => 'Alejandro', 'languages' => ['quechua']]);
    Anfitrion::create(['user_id' => $owner->id, 'name' => 'Oculto', 'languages' => ['aimara'], 'is_active' => false]);

    $this->getJson('/api/anfitriones?language=aimara')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Juana');
});

test('el almanaque devuelve las fechas que caen en el rango, también al cruzar de año', function () {
    AlmanacEntry::create(['title' => 'Año nuevo', 'kind' => 'civica', 'month' => 1, 'day' => 1]);
    AlmanacEntry::create(['title' => 'Candelaria', 'kind' => 'fiesta', 'month' => 2, 'day' => 2]);

    $this->getJson('/api/almanaque?from=2026-12-28&days=7')
        ->assertOk()
        ->assertJsonCount(1, 'data.items')
        ->assertJsonPath('data.items.0.title', 'Año nuevo');
});

test('una fiesta de varios días aparece si el rango empieza a la mitad', function () {
    AlmanacEntry::create(['title' => 'Mes de la Pachamama', 'kind' => 'ritual', 'month' => 8, 'day' => 1, 'duration_days' => 31]);

    $entry = AlmanacEntry::first();
    $occ = $entry->occurrencesBetween(Carbon::parse('2026-08-20'), Carbon::parse('2026-08-21'));

    expect($occ)->toHaveCount(1);
});

test('solo los administradores editan el almanaque', function () {
    Sanctum::actingAs(socio());

    $this->postJson('/api/admin/almanaque', ['title' => 'X', 'kind' => 'fiesta', 'month' => 1, 'day' => 1])->assertForbidden();
});

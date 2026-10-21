<?php

use App\Models\Candelaria;
use App\Models\Evento;
use App\Models\Experiencia;
use App\Models\Locale;
use App\Models\Promocion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/* Puntos de referencia: Plaza de Armas de Puno, a ~1 km el puerto y a ~30 km Juliaca (fuera del radio) */
const PLAZA = [-15.8402, -70.0219];
const PUERTO = [-15.8350, -70.0150];
const JULIACA = [-15.5000, -70.1300];

function contenidoUser(string $role = 'socio'): User
{
    return User::factory()->create(['role' => $role, 'is_active' => true]);
}

function contenidoLocale(User $owner, array $attrs = [], array $point = PLAZA): Locale
{
    $locale = new Locale(array_merge([
        'user_id' => $owner->id,
        'name' => 'Restaurante del lago',
        'description' => 'Trucha frita',
        'address' => 'Jr. Lima 100, Puno',
        'category' => 'restaurant',
        'is_active' => true,
    ], $attrs));
    $locale->setLocationFromCoordinates(...$point);
    $locale->save();

    return $locale;
}

/** Datos válidos para crear cada tipo de contenido por la API */
function contenidoPayload(string $type, User $owner): array
{
    return match ($type) {
        'locales' => [
            'name' => 'Café Titicaca',
            'category' => 'restaurant',
            'description' => 'Café de altura',
            'address' => 'Jr. Lima 200, Puno',
            'latitude' => PLAZA[0],
            'longitude' => PLAZA[1],
        ],
        'eventos' => [
            'title' => 'Concierto en la plaza',
            'description' => 'Música en vivo',
            'category' => 'concert',
            'start_time' => now()->addDay()->toDateTimeString(),
            'end_time' => now()->addDays(2)->toDateTimeString(),
            'latitude' => PLAZA[0],
            'longitude' => PLAZA[1],
        ],
        'experiencias' => [
            'title' => 'Kayak en el lago',
            'description' => 'Salida al amanecer',
            'difficulty' => 'easy',
            'price_pen' => 80,
            'latitude' => PUERTO[0],
            'longitude' => PUERTO[1],
        ],
        'promociones' => [
            'title' => 'Trucha 2x1',
            'description' => 'Solo almuerzos',
            'locale_id' => contenidoLocale($owner)->id,
            'discount_type' => 'percentage',
            'discount_percentage' => 20,
            'start_date' => now()->subDay()->toDateTimeString(),
            'end_date' => now()->addDays(10)->toDateTimeString(),
        ],
        'candelaria' => [
            'title' => 'La diablada',
            'description' => 'Historia de la danza',
            'category' => 'dance',
        ],
    };
}

/** Campo de texto principal de cada tipo (los locales usan name) */
function contenidoTitle(string $type): string
{
    return $type === 'locales' ? 'name' : 'title';
}

const TIPOS = ['locales', 'eventos', 'experiencias', 'promociones', 'candelaria'];

const MODELOS = [
    'locales' => Locale::class,
    'eventos' => Evento::class,
    'experiencias' => Experiencia::class,
    'promociones' => Promocion::class,
    'candelaria' => Candelaria::class,
];

/* ---------- CRUD de cada tipo ---------- */

test('el socio publica, edita y elimina su contenido', function (string $type) {
    $socio = contenidoUser();
    Sanctum::actingAs($socio);
    $field = contenidoTitle($type);

    $created = $this->postJson("/api/{$type}", contenidoPayload($type, $socio))
        ->assertCreated()
        ->assertJsonPath('success', true);
    $id = $created->json('data.id');
    expect(MODELOS[$type]::find($id)->user_id)->toBe($socio->id);

    $this->getJson("/api/{$type}/{$id}")->assertOk()->assertJsonPath("data.{$field}", $created->json("data.{$field}"));

    $this->putJson("/api/{$type}/{$id}", [$field => 'Nombre corregido'])
        ->assertOk()
        ->assertJsonPath("data.{$field}", 'Nombre corregido');

    $this->deleteJson("/api/{$type}/{$id}")->assertOk();
    $this->getJson("/api/{$type}/{$id}")->assertNotFound();
    expect(MODELOS[$type]::withTrashed()->find($id)->trashed())->toBeTrue();
})->with(TIPOS);

test('un socio no edita ni elimina el contenido de otro', function (string $type) {
    $owner = contenidoUser();
    Sanctum::actingAs($owner);
    $id = $this->postJson("/api/{$type}", contenidoPayload($type, $owner))->assertCreated()->json('data.id');

    Sanctum::actingAs(contenidoUser());
    $this->putJson("/api/{$type}/{$id}", [contenidoTitle($type) => 'Ajeno'])->assertForbidden();
    $this->deleteJson("/api/{$type}/{$id}")->assertForbidden();
    expect(MODELOS[$type]::find($id))->not->toBeNull();
})->with(TIPOS);

test('el administrador puede editar el contenido de cualquier socio', function (string $type) {
    $owner = contenidoUser();
    Sanctum::actingAs($owner);
    $id = $this->postJson("/api/{$type}", contenidoPayload($type, $owner))->assertCreated()->json('data.id');

    Sanctum::actingAs(contenidoUser('admin'));
    $this->putJson("/api/{$type}/{$id}", [contenidoTitle($type) => 'Revisado'])->assertOk();
})->with(TIPOS);

test('un viajero no puede publicar contenido', function (string $type) {
    $tourist = contenidoUser('tourist');
    Sanctum::actingAs($tourist);

    $this->postJson("/api/{$type}", contenidoPayload($type, contenidoUser()))->assertForbidden();
})->with(TIPOS);

test('un invitado no puede publicar contenido', function (string $type) {
    $this->postJson("/api/{$type}", contenidoPayload($type, contenidoUser()))->assertUnauthorized();
})->with(TIPOS);

test('crear contenido sin los campos obligatorios da 422', function (string $type) {
    Sanctum::actingAs(contenidoUser());

    $this->postJson("/api/{$type}", [])->assertUnprocessable()->assertJsonValidationErrors(['description']);
})->with(TIPOS);

/* ---------- Listados ---------- */

test('el listado muestra solo el contenido activo', function (string $type) {
    $socio = contenidoUser();
    Sanctum::actingAs($socio);
    $visible = $this->postJson("/api/{$type}", contenidoPayload($type, $socio))->assertCreated()->json('data.id');
    $hidden = $this->postJson("/api/{$type}", [...contenidoPayload($type, $socio), 'is_active' => false])->assertCreated()->json('data.id');

    $ids = collect($this->getJson("/api/{$type}")->assertOk()->json('data.data'))->pluck('id');

    expect($ids)->toContain($visible)->not->toContain($hidden);
})->with(TIPOS);

test('la búsqueda filtra por texto', function () {
    $socio = contenidoUser();
    contenidoLocale($socio, ['name' => 'Pollería El Lago']);
    contenidoLocale($socio, ['name' => 'Hostal Andino', 'category' => 'hotel', 'address' => 'Av. Titicaca 5']);

    $names = collect($this->getJson('/api/locales?search=Pollería')->assertOk()->json('data.data'))->pluck('name');

    expect($names->all())->toBe(['Pollería El Lago']);
});

test('un filtro vacío no deja la lista vacía', function () {
    contenidoLocale(contenidoUser());

    $this->getJson('/api/locales?category=')->assertOk()->assertJsonPath('data.total', 1);
});

test('los eventos terminados no aparecen en el listado', function () {
    $socio = contenidoUser();
    $base = ['user_id' => $socio->id, 'description' => 'x', 'category' => 'concert', 'is_active' => true];
    Evento::create([...$base, 'title' => 'Próximo', 'start_time' => now()->addDay(), 'end_time' => now()->addDays(2)]);
    Evento::create([...$base, 'title' => 'Pasado', 'start_time' => now()->subDays(3), 'end_time' => now()->subDays(2)]);

    $titles = collect($this->getJson('/api/eventos')->assertOk()->json('data.data'))->pluck('title');

    expect($titles->all())->toBe(['Próximo']);
});

test('las promociones con porcentaje exigen el porcentaje', function () {
    $socio = contenidoUser();
    Sanctum::actingAs($socio);
    $payload = contenidoPayload('promociones', $socio);
    unset($payload['discount_percentage']);

    $this->postJson('/api/promociones', $payload)->assertUnprocessable()->assertJsonValidationErrors(['discount_percentage']);
});

test('un socio no puede asociar una promoción a un local ajeno', function () {
    $socio = contenidoUser();
    Sanctum::actingAs($socio);
    $payload = [...contenidoPayload('promociones', $socio), 'locale_id' => contenidoLocale(contenidoUser())->id];

    $this->postJson('/api/promociones', $payload)->assertUnprocessable()->assertJsonValidationErrors(['locale_id']);
});

/* ---------- Búsqueda por cercanía ---------- */

test('la cercanía devuelve lo que está dentro del radio, del más cercano al más lejano', function () {
    $socio = contenidoUser();
    contenidoLocale($socio, ['name' => 'Puerto'], PUERTO);
    contenidoLocale($socio, ['name' => 'Plaza'], PLAZA);
    contenidoLocale($socio, ['name' => 'Juliaca'], JULIACA);

    $url = sprintf('/api/locales?lat=%s&lng=%s&radius=5000', ...PLAZA);
    $names = collect($this->getJson($url)->assertOk()->json('data.data'))->pluck('name');

    expect($names->all())->toBe(['Plaza', 'Puerto']);
});

test('la cercanía respeta el filtro de activos y los demás filtros', function () {
    $socio = contenidoUser();
    contenidoLocale($socio, ['name' => 'Restaurante abierto']);
    contenidoLocale($socio, ['name' => 'Restaurante cerrado', 'is_active' => false]);
    contenidoLocale($socio, ['name' => 'Hotel cercano', 'category' => 'hotel']);

    $url = sprintf('/api/locales?lat=%s&lng=%s&category=restaurant', ...PLAZA);
    $names = collect($this->getJson($url)->assertOk()->json('data.data'))->pluck('name');

    expect($names->all())->toBe(['Restaurante abierto']);
});

test('la cercanía en experiencias mantiene los datos del anfitrión', function () {
    $socio = contenidoUser();
    Sanctum::actingAs($socio);
    $this->postJson('/api/experiencias', contenidoPayload('experiencias', $socio))->assertCreated();

    $url = sprintf('/api/experiencias?lat=%s&lng=%s', ...PLAZA);
    $first = $this->getJson($url)->assertOk()->json('data.data.0');

    expect($first)->toHaveKey('anfitrion')->and($first['title'])->toBe('Kayak en el lago');
});

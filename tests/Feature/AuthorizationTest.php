<?php

use App\Models\Locale;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function userWithRole(string $role, array $attrs = []): User
{
    return User::factory()->create(array_merge(['role' => $role, 'is_active' => true], $attrs));
}

function makeLocale(User $owner): Locale
{
    $locale = new Locale([
        'user_id' => $owner->id,
        'name' => 'Restaurante de prueba',
        'description' => 'Descripción',
        'address' => 'Jr. Lima 100, Puno',
        'category' => 'restaurant',
        'latitude' => -15.84,
        'longitude' => -70.02,
        'is_active' => true,
    ]);
    $locale->setLocationFromCoordinates(-15.84, -70.02);
    $locale->save();

    return $locale;
}

test('la ruta de prueba que emitía tokens ya no existe', function () {
    userWithRole('socio', ['email' => 'socio@example.com']);

    $response = $this->get('/test-token-creation');

    $response->assertRedirect();
    expect($response->getContent())->not->toContain('plainTextToken');
});

test('un invitado no puede ver el perfil', function () {
    $this->getJson('/api/profile')->assertUnauthorized();
});

test('turistas y socios no acceden a la administración de usuarios', function (string $role) {
    Sanctum::actingAs(userWithRole($role));

    $this->getJson('/api/admin/users')->assertForbidden();
    $this->postJson('/api/admin/users', [])->assertForbidden();
})->with(['tourist', 'socio']);

test('un administrador puede listar usuarios', function () {
    Sanctum::actingAs(userWithRole('admin'));

    $this->getJson('/api/admin/users')->assertOk()->assertJsonPath('success', true);
});

test('un administrador no puede eliminarse a sí mismo', function () {
    $admin = userWithRole('admin');
    Sanctum::actingAs($admin);

    $this->deleteJson("/api/admin/users/{$admin->id}")->assertStatus(422);
    expect(User::find($admin->id))->not->toBeNull();
});

test('un turista no puede subir imágenes', function () {
    Storage::fake('public');
    Sanctum::actingAs(userWithRole('tourist'));

    $this->postJson('/api/uploads', [
        'image' => UploadedFile::fake()->image('foto.jpg'),
        'folder' => 'locales',
    ])->assertForbidden();
});

test('un socio puede subir imágenes solo a carpetas permitidas', function () {
    Storage::fake('public');
    Sanctum::actingAs(userWithRole('socio'));

    $this->postJson('/api/uploads', [
        'image' => UploadedFile::fake()->image('foto.jpg'),
        'folder' => 'locales',
    ])->assertCreated()->assertJsonStructure(['data' => ['url', 'path']]);

    $this->postJson('/api/uploads', [
        'image' => UploadedFile::fake()->image('foto.jpg'),
        'folder' => '../../config',
    ])->assertUnprocessable();
});

test('un socio no puede editar el local de otro socio', function () {
    $owner = userWithRole('socio');
    $other = userWithRole('socio');
    $locale = makeLocale($owner);

    Sanctum::actingAs($other);

    $this->putJson("/api/locales/{$locale->id}", ['name' => 'Cambiado'])->assertForbidden();
    expect($locale->fresh()->name)->toBe('Restaurante de prueba');
});

test('mi contenido solo muestra lo del socio autenticado', function () {
    $owner = userWithRole('socio');
    $other = userWithRole('socio');
    makeLocale($owner);
    makeLocale($other);

    Sanctum::actingAs($owner);

    $this->getJson('/api/my/locales')->assertOk()->assertJsonPath('data.total', 1);
});

test('una cuenta desactivada no puede usar la API', function () {
    Sanctum::actingAs(userWithRole('tourist', ['is_active' => false]));

    $this->getJson('/api/profile')->assertForbidden();
});

test('el registro siempre crea turistas aunque se envíe otro rol', function () {
    $this->postJson('/api/register', [
        'name' => 'Intruso',
        'email' => 'intruso@example.com',
        'password' => 'Secreta#2026',
        'password_confirmation' => 'Secreta#2026',
        'role' => 'admin',
    ])->assertCreated();

    expect(User::where('email', 'intruso@example.com')->value('role'))->toBe('tourist');
});

test('las reseñas públicas no exponen el correo de los usuarios', function () {
    $author = userWithRole('tourist', ['email' => 'privado@example.com']);
    $locale = makeLocale(userWithRole('socio'));
    Review::create([
        'user_id' => $author->id,
        'reviewable_type' => 'locale',
        'reviewable_id' => $locale->id,
        'rating' => 5,
        'comment' => 'Muy bueno',
    ]);

    $response = $this->getJson("/api/reviews?reviewable_type=locale&reviewable_id={$locale->id}");

    $response->assertOk();
    expect($response->getContent())->not->toContain('privado@example.com');
});

test('los listados públicos no hacen una consulta por cada coordenada', function () {
    $owner = userWithRole('socio');
    foreach (range(1, 5) as $_) {
        makeLocale($owner);
    }

    DB::enableQueryLog();
    $this->getJson('/api/locales')->assertOk();
    $queries = count(DB::getQueryLog());

    // Paginación + usuarios + caché: un número fijo, no 2 por local
    expect($queries)->toBeLessThan(8);
});

test('un socio no puede asociar su evento al local de otro socio', function () {
    $ajeno = makeLocale(userWithRole('socio'));
    Sanctum::actingAs(userWithRole('socio'));

    $this->postJson('/api/eventos', [
        'title' => 'Concierto',
        'description' => 'En la plaza',
        'category' => 'concert',
        'start_time' => now()->addDay()->toIso8601String(),
        'end_time' => now()->addDay()->addHours(3)->toIso8601String(),
        'locale_id' => $ajeno->id,
    ])->assertUnprocessable()->assertJsonValidationErrors('locale_id');
});

test('editar contenido ajeno responde 403 aunque los datos sean inválidos', function () {
    $locale = makeLocale(userWithRole('socio'));
    Sanctum::actingAs(userWithRole('socio'));

    $this->putJson("/api/locales/{$locale->id}", ['email' => 'no-es-correo'])
        ->assertForbidden()
        ->assertJsonPath('success', false);
});

test('los favoritos aceptan el alias y el nombre de clase antiguo', function () {
    $locale = makeLocale(userWithRole('socio'));
    Sanctum::actingAs(userWithRole('tourist'));

    $this->postJson('/api/favorites/toggle', ['favoritable_type' => 'locale', 'favoritable_id' => $locale->id])
        ->assertOk()->assertJsonPath('is_favorited', true);

    $this->getJson('/api/favorites/check?favoritable_type='.urlencode(Locale::class)."&favoritable_id={$locale->id}")
        ->assertOk()->assertJsonPath('is_favorited', true);

    $this->assertDatabaseHas('favorites', ['favoritable_type' => 'locale', 'favoritable_id' => $locale->id]);
});

test('no se puede guardar en favoritos algo que no existe', function () {
    Sanctum::actingAs(userWithRole('tourist'));

    $this->postJson('/api/favorites/toggle', ['favoritable_type' => 'evento', 'favoritable_id' => 999999])
        ->assertNotFound();
});

test('los errores de validación vienen en español y con el formato de la API', function () {
    $this->postJson('/api/register', [])
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('errors.email.0', 'Correo electrónico es obligatorio.');
});

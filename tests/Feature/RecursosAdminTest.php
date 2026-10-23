<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
});

/** Wikimedia responde 403 a las peticiones sin User-Agent identificado */
function wikimediaFake(array $responses): void
{
    Http::fake(fn (Request $request) => str_starts_with($request->header('User-Agent')[0] ?? '', 'NanPuno/')
        ? $responses[parse_url($request->url(), PHP_URL_HOST)] ?? Http::response([], 404)
        : Http::response('Please set a user-agent', 403));
}

test('la búsqueda en Wikipedia envía un User-Agent y devuelve el resumen', function () {
    wikimediaFake(['es.wikipedia.org' => Http::response([
        'title' => 'Diablada',
        'extract' => 'Danza del altiplano',
        'description' => 'danza',
        'content_urls' => ['desktop' => ['page' => 'https://es.wikipedia.org/wiki/Diablada']],
    ])]);

    $this->getJson('/api/admin/resources/search-wikipedia?query=Diablada')
        ->assertOk()
        ->assertJsonPath('data.title', 'Diablada')
        ->assertJsonPath('data.url', 'https://es.wikipedia.org/wiki/Diablada');
});

test('un tema que no está en Wikipedia da 404', function () {
    wikimediaFake([]);

    $this->getJson('/api/admin/resources/search-wikipedia?query=Inexistente')->assertNotFound();
});

test('la búsqueda de imágenes consulta Wikimedia Commons con User-Agent', function () {
    wikimediaFake(['commons.wikimedia.org' => Http::response(['query' => ['search' => [
        ['title' => 'File:Diablada Puno.jpg', 'snippet' => '<span>Diablada</span> en Puno'],
    ]]])]);

    $this->getJson('/api/admin/resources/search-images?query=Diablada')
        ->assertOk()
        ->assertJsonPath('data.wikimedia.0.title', 'File:Diablada Puno.jpg')
        ->assertJsonPath('data.wikimedia.0.snippet', 'Diablada en Puno')
        ->assertJsonMissingPath('data.unsplash');
});

test('solo los administradores buscan recursos', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'socio', 'is_active' => true]));

    $this->getJson('/api/admin/resources/search-images?query=Diablada')->assertForbidden();
});

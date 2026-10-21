<?php

use App\Models\CandelariaProgramItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function parada(array $groups): CandelariaProgramItem
{
    $item = CandelariaProgramItem::create([
        'title' => 'Parada de prueba',
        'type' => 'parada',
        'starts_at' => now()->subHour(),
        'ends_at' => now()->addHours(5),
    ]);
    foreach ($groups as $i => $name) {
        $item->performances()->create(['group_name' => $name, 'order_number' => $i + 1]);
    }

    return $item;
}

test('solo los administradores controlan el desfile', function (string $role) {
    $item = parada(['A']);
    Sanctum::actingAs(User::factory()->create(['role' => $role, 'is_active' => true]));

    $this->postJson("/api/admin/candelaria-program/{$item->id}/advance")->assertForbidden();
})->with(['tourist', 'socio']);

test('"pasó el siguiente" termina el conjunto actual y empieza el próximo', function () {
    $item = parada(['Primero', 'Segundo', 'Tercero']);
    Sanctum::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));

    $this->postJson("/api/admin/candelaria-program/{$item->id}/advance")->assertOk();
    $this->postJson("/api/admin/candelaria-program/{$item->id}/advance")
        ->assertOk()
        ->assertJsonPath('data.current.group_name', 'Segundo')
        ->assertJsonPath('data.done_count', 1)
        ->assertJsonPath('data.upcoming.0.group_name', 'Tercero');

    expect($item->performances()->where('status', 'performing')->count())->toBe(1);
});

test('el estado en vivo es público y estima la hora de los próximos conjuntos', function () {
    $item = parada(['Primero', 'Segundo']);
    $item->performances()->where('order_number', 1)->update(['status' => 'performing', 'started_at' => now()]);

    $this->getJson('/api/candelaria-live')
        ->assertOk()
        ->assertJsonPath('data.live', true)
        ->assertJsonPath('data.current.group_name', 'Primero')
        ->assertJsonStructure(['data' => ['upcoming' => [['eta', 'position']]]]);
});

test('sin jornada en curso, el estado indica la próxima', function () {
    CandelariaProgramItem::create([
        'title' => 'Misa de fiesta',
        'type' => 'misa',
        'starts_at' => now()->addDays(3),
        'ends_at' => now()->addDays(3)->addHours(2),
    ]);

    $this->getJson('/api/candelaria-live')
        ->assertOk()
        ->assertJsonPath('data.live', false)
        ->assertJsonPath('data.next_item.title', 'Misa de fiesta');
});

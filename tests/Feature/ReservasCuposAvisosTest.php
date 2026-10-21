<?php

use App\Models\Booking;
use App\Models\Experiencia;
use App\Models\User;
use App\Notifications\BookingNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function cuposUser(string $role = 'tourist', array $attrs = []): User
{
    return User::factory()->create(array_merge(['role' => $role, 'is_active' => true], $attrs));
}

function cuposExperiencia(User $owner, array $attrs = []): Experiencia
{
    return Experiencia::create(array_merge([
        'user_id' => $owner->id,
        'title' => 'Kayak al amanecer',
        'description' => 'Remamos entre las islas',
        'difficulty' => 'medium',
        'price_pen' => 120,
        'max_participants' => 3,
        'meeting_point' => 'Puerto de Puno',
        'meeting_time' => '05:30',
        'is_active' => true,
    ], $attrs));
}

function cuposReservar($test, Experiencia $exp, int $participants, ?string $date = null)
{
    return $test->postJson('/api/bookings', [
        'experiencia_id' => $exp->id,
        'booking_date' => $date ?? now()->addWeek()->toDateString(),
        'participants' => $participants,
    ]);
}

/* ---------- Cupos ---------- */

test('no se reservan más cupos de los que quedan', function () {
    Notification::fake();
    $exp = cuposExperiencia(cuposUser('socio'));
    Sanctum::actingAs(cuposUser());

    cuposReservar($this, $exp, 2)->assertCreated();
    cuposReservar($this, $exp, 2)->assertUnprocessable()->assertJsonValidationErrors('participants');
    cuposReservar($this, $exp, 1)->assertCreated();
    cuposReservar($this, $exp, 1)
        ->assertUnprocessable()
        ->assertJsonPath('errors.participants.0', 'Ya no quedan cupos para esa fecha. Elige otro día.');

    expect(Booking::where('experiencia_id', $exp->id)->sum('participants'))->toEqual(3);
});

test('los cupos de una reserva cancelada vuelven a estar libres', function () {
    Notification::fake();
    $exp = cuposExperiencia(cuposUser('socio'));
    Sanctum::actingAs(cuposUser());

    $id = cuposReservar($this, $exp, 3)->assertCreated()->json('data.id');
    $this->postJson("/api/bookings/{$id}/cancel")->assertOk();

    cuposReservar($this, $exp, 3)->assertCreated();
});

test('la disponibilidad por día descuenta las reservas activas', function () {
    Notification::fake();
    $exp = cuposExperiencia(cuposUser('socio'));
    Sanctum::actingAs(cuposUser());
    $date = now('America/Lima')->addDays(2)->toDateString();

    cuposReservar($this, $exp, 2, $date)->assertCreated();

    $day = collect($this->getJson("/api/experiencias/{$exp->id}/availability")->assertOk()->json('data'))
        ->firstWhere('date', $date);

    expect($day['available'])->toBe(1)->and($day['departure_time'])->toBe('05:30');
});

test('una experiencia desactivada no recibe reservas', function () {
    Notification::fake();
    $exp = cuposExperiencia(cuposUser('socio'), ['is_active' => false]);
    Sanctum::actingAs(cuposUser());

    cuposReservar($this, $exp, 1)->assertUnprocessable()->assertJsonValidationErrors('experiencia_id');
});

/* ---------- Cambios de estado del socio ---------- */

test('el socio no puede reconfirmar una reserva cancelada', function () {
    Notification::fake();
    $socio = cuposUser('socio');
    $exp = cuposExperiencia($socio);
    $booking = Booking::create([
        'user_id' => cuposUser()->id, 'experiencia_id' => $exp->id, 'booking_date' => now()->addWeek(),
        'participants' => 1, 'total_price' => 120, 'status' => 'cancelled',
    ]);

    Sanctum::actingAs($socio);
    $this->putJson("/api/partner/bookings/{$booking->id}", ['status' => 'confirmed'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('status');

    expect($booking->fresh()->status)->toBe('cancelled');
});

test('solo se completa una reserva confirmada y desde el día de la salida', function () {
    Notification::fake();
    $socio = cuposUser('socio');
    $exp = cuposExperiencia($socio);
    $base = ['user_id' => cuposUser()->id, 'experiencia_id' => $exp->id, 'participants' => 1, 'total_price' => 120];

    $pending = Booking::create($base + ['booking_date' => now()->addWeek(), 'status' => 'pending']);
    $future = Booking::create($base + ['booking_date' => now()->addWeek(), 'status' => 'confirmed']);
    $today = Booking::create($base + ['booking_date' => now('America/Lima')->toDateString(), 'status' => 'confirmed']);

    Sanctum::actingAs($socio);
    $this->putJson("/api/partner/bookings/{$pending->id}", ['status' => 'completed'])->assertUnprocessable();
    $this->putJson("/api/partner/bookings/{$future->id}", ['status' => 'completed'])->assertUnprocessable();
    $this->putJson("/api/partner/bookings/{$today->id}", ['status' => 'completed'])->assertOk();

    expect($today->fresh()->status)->toBe('completed');
});

/* ---------- Avisos en la app ---------- */

test('el socio ve la reserva nueva en sus avisos y la marca como leída', function () {
    $socio = cuposUser('socio');
    $exp = cuposExperiencia($socio);

    Sanctum::actingAs(cuposUser(attrs: ['name' => 'Ana Viajera']));
    cuposReservar($this, $exp, 2)->assertCreated();

    Sanctum::actingAs($socio);
    $res = $this->getJson('/api/notifications')->assertOk();
    $res->assertJsonPath('data.unread', 1)
        ->assertJsonPath('data.items.0.data.event', 'requested')
        ->assertJsonPath('data.items.0.data.url', '/dashboard/reservas');
    expect($res->json('data.items.0.data.message'))->toContain('Ana Viajera')->toContain('Kayak al amanecer');

    $id = $res->json('data.items.0.id');
    $this->postJson("/api/notifications/{$id}/read")->assertOk();
    $this->getJson('/api/notifications')->assertJsonPath('data.unread', 0);
});

test('nadie puede marcar los avisos de otra persona', function () {
    $socio = cuposUser('socio');
    Sanctum::actingAs(cuposUser());
    cuposReservar($this, cuposExperiencia($socio), 1)->assertCreated();
    $id = $socio->notifications()->first()->id;

    Sanctum::actingAs(cuposUser());
    $this->postJson("/api/notifications/{$id}/read")->assertNotFound();
    expect($socio->notifications()->first()->read_at)->toBeNull();
});

test('el aviso en la app llega aunque el correo esté desactivado', function () {
    Notification::fake();
    $socio = cuposUser('socio', ['preferences' => ['notifications' => ['bookings' => false]]]);
    Sanctum::actingAs(cuposUser());
    cuposReservar($this, cuposExperiencia($socio), 1)->assertCreated();

    Notification::assertSentTo($socio, BookingNotification::class, fn ($n, $channels) => $channels === ['database']);
});

/* ---------- Recordatorio del día anterior ---------- */

test('el recordatorio sale una sola vez para las confirmadas de mañana', function () {
    Notification::fake();
    $exp = cuposExperiencia(cuposUser('socio'));
    $tourist = cuposUser();
    $tomorrow = now('America/Lima')->addDay()->toDateString();
    $base = ['user_id' => $tourist->id, 'experiencia_id' => $exp->id, 'participants' => 1, 'total_price' => 120];

    $confirmed = Booking::create($base + ['booking_date' => $tomorrow, 'status' => 'confirmed']);
    Booking::create($base + ['booking_date' => $tomorrow, 'status' => 'pending']);
    Booking::create($base + ['booking_date' => now()->addWeek(), 'status' => 'confirmed']);

    $this->artisan('bookings:remind')->assertSuccessful();
    $this->artisan('bookings:remind')->assertSuccessful();

    Notification::assertSentToTimes($tourist, BookingNotification::class, 1);
    Notification::assertSentTo($tourist, BookingNotification::class, fn ($n) => $n->event === 'reminder' && $n->booking->is($confirmed));
    expect($confirmed->fresh()->reminded_at)->not->toBeNull();
});

test('el recordatorio incluye el punto y la hora de encuentro', function () {
    $exp = cuposExperiencia(cuposUser('socio'));
    $booking = Booking::create([
        'user_id' => cuposUser()->id, 'experiencia_id' => $exp->id, 'booking_date' => now()->addDay(),
        'participants' => 2, 'total_price' => 240, 'status' => 'confirmed',
    ]);

    $data = (new BookingNotification($booking, 'reminder'))->toArray($booking->user);

    expect($data['message'])->toContain('Puerto de Puno')->toContain('05:30 h')
        ->and($data['url'])->toBe('/cuenta/reservas');
});

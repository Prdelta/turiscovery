<?php

use App\Models\Booking;
use App\Models\Experiencia;
use App\Models\Locale;
use App\Models\Promocion;
use App\Models\User;
use App\Models\UserCoupon;
use App\Notifications\BookingNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    Notification::fake();
});

function cuentaUser(string $role = 'tourist', array $attrs = []): User
{
    return User::factory()->create(array_merge(['role' => $role, 'is_active' => true], $attrs));
}

function cuentaExperiencia(User $owner): Experiencia
{
    return Experiencia::create([
        'user_id' => $owner->id,
        'title' => 'Kayak en Uros',
        'description' => 'Paseo por las islas',
        'difficulty' => 'easy',
        'price_pen' => 80,
        'max_participants' => 10,
        'is_active' => true,
    ]);
}

function cuentaCupon(User $socio, User $tourist, array $attrs = []): UserCoupon
{
    $locale = new Locale([
        'user_id' => $socio->id,
        'name' => 'Café del lago',
        'description' => 'Café',
        'address' => 'Jr. Lima 100, Puno',
        'category' => 'restaurant',
        'latitude' => -15.84,
        'longitude' => -70.02,
        'is_active' => true,
    ]);
    $locale->setLocationFromCoordinates(-15.84, -70.02);
    $locale->save();

    $promo = Promocion::create([
        'user_id' => $socio->id,
        'locale_id' => $locale->id,
        'title' => '2x1 en café',
        'description' => 'Promo',
        'discount_type' => '2x1',
        'start_date' => now()->subDay(),
        'end_date' => now()->addMonth(),
        'is_active' => true,
    ]);

    return UserCoupon::create(array_merge([
        'user_id' => $tourist->id,
        'promocion_id' => $promo->id,
        'coupon_code' => 'TURISCO-ABCD1234',
        'status' => 'available',
        'claimed_at' => now(),
    ], $attrs));
}

/* ---------- Contraseña y verificación ---------- */

test('se envía el enlace para recuperar la contraseña sin revelar si el correo existe', function () {
    $user = cuentaUser();

    $this->postJson('/api/forgot-password', ['email' => $user->email])->assertOk();
    $this->postJson('/api/forgot-password', ['email' => 'nadie@example.com'])->assertOk();

    Notification::assertSentTo($user, ResetPassword::class);
});

test('con el token se puede fijar una nueva contraseña', function () {
    $user = cuentaUser();
    $token = Password::createToken($user);

    $this->postJson('/api/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'Nueva-Clave1',
        'password_confirmation' => 'Nueva-Clave1',
    ])->assertOk();

    expect(Hash::check('Nueva-Clave1', $user->fresh()->password))->toBeTrue();
});

test('un token inválido no cambia la contraseña', function () {
    $user = cuentaUser();

    $this->postJson('/api/reset-password', [
        'token' => 'falso',
        'email' => $user->email,
        'password' => 'Nueva-Clave1',
        'password_confirmation' => 'Nueva-Clave1',
    ])->assertStatus(422);
});

test('el enlace firmado verifica el correo y vuelve al frontend', function () {
    $user = cuentaUser('tourist', ['email_verified_at' => null]);
    $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), ['id' => $user->id, 'hash' => sha1($user->email)]);

    $this->get($url)->assertRedirectContains('/cuenta?verificado=1');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('se puede reenviar el correo de verificación', function () {
    $user = cuentaUser('tourist', ['email_verified_at' => null]);
    Sanctum::actingAs($user);

    $this->postJson('/api/email/verification-notification')->assertOk();

    Notification::assertSentTo($user, VerifyEmail::class);
});

/* ---------- Reservas ---------- */

test('el socio recibe un aviso al llegar una reserva y el viajero al confirmarla', function () {
    $socio = cuentaUser('socio');
    $tourist = cuentaUser();
    $exp = cuentaExperiencia($socio);

    Sanctum::actingAs($tourist);
    $id = $this->postJson('/api/bookings', [
        'experiencia_id' => $exp->id,
        'booking_date' => now()->addWeek()->toDateString(),
        'participants' => 2,
    ])->assertCreated()->json('data.id');

    Notification::assertSentTo($socio, BookingNotification::class, fn ($n) => $n->event === 'requested');

    Sanctum::actingAs($socio);
    $this->putJson("/api/partner/bookings/{$id}", ['status' => 'confirmed'])->assertOk();

    Notification::assertSentTo($tourist, BookingNotification::class, fn ($n) => $n->event === 'confirmed');
});

test('quien desactiva los correos de reservas no los recibe', function () {
    $socio = cuentaUser('socio', ['preferences' => ['notifications' => ['bookings' => false]]]);
    $tourist = cuentaUser();
    $exp = cuentaExperiencia($socio);

    Sanctum::actingAs($tourist);
    $this->postJson('/api/bookings', [
        'experiencia_id' => $exp->id,
        'booking_date' => now()->addWeek()->toDateString(),
        'participants' => 1,
    ])->assertCreated();

    Notification::assertSentTo($socio, BookingNotification::class, fn ($n, $channels) => ! in_array('mail', $channels, true));
});

test('la tarea diaria completa reservas pasadas y vence cupones', function () {
    $socio = cuentaUser('socio');
    $tourist = cuentaUser();
    $exp = cuentaExperiencia($socio);

    $done = Booking::create(['user_id' => $tourist->id, 'experiencia_id' => $exp->id, 'booking_date' => now()->subDays(2), 'participants' => 1, 'total_price' => 80, 'status' => 'confirmed']);
    $stale = Booking::create(['user_id' => $tourist->id, 'experiencia_id' => $exp->id, 'booking_date' => now()->subDays(2), 'participants' => 1, 'total_price' => 80, 'status' => 'pending']);
    $coupon = cuentaCupon($socio, $tourist, ['expires_at' => now()->subHour()]);

    $this->artisan('turiscovery:expire')->assertSuccessful();

    expect($done->fresh()->status)->toBe('completed')
        ->and($stale->fresh()->status)->toBe('cancelled')
        ->and($coupon->fresh()->status)->toBe('expired');
});

/* ---------- Cupones ---------- */

test('el socio canjea en su local el cupón que le muestra el viajero', function () {
    $socio = cuentaUser('socio');
    $coupon = cuentaCupon($socio, cuentaUser());
    Sanctum::actingAs($socio);

    $this->getJson('/api/partner/coupons/lookup?code=turisco-abcd1234')->assertOk()->assertJsonPath('data.status', 'available');
    $this->postJson('/api/partner/coupons/redeem', ['code' => 'TURISCO-ABCD1234'])->assertOk();
    $this->postJson('/api/partner/coupons/redeem', ['code' => 'TURISCO-ABCD1234'])->assertStatus(422);

    expect($coupon->fresh()->status)->toBe('used');
});

test('un socio no puede canjear cupones de otro socio', function () {
    $coupon = cuentaCupon(cuentaUser('socio'), cuentaUser());
    Sanctum::actingAs(cuentaUser('socio'));

    $this->postJson('/api/partner/coupons/redeem', ['code' => $coupon->coupon_code])->assertNotFound();
});

test('el viajero ya no puede marcar su propio cupón como usado', function () {
    $tourist = cuentaUser();
    $coupon = cuentaCupon(cuentaUser('socio'), $tourist);
    Sanctum::actingAs($tourist);

    $this->postJson("/api/user-coupons/{$coupon->id}/redeem")->assertForbidden();
});

/* ---------- Perfil ---------- */

test('el viajero guarda sus preferencias y su foto', function () {
    Storage::fake('public');
    Sanctum::actingAs(cuentaUser());

    $this->putJson('/api/profile', [
        'name' => 'Ana',
        'preferences' => ['interests' => ['experiencias', 'eventos'], 'pace' => 'suave', 'notifications' => ['bookings' => false]],
    ])->assertOk()
        ->assertJsonPath('data.preferences.pace', 'suave')
        ->assertJsonPath('data.preferences.notifications.bookings', false);

    $this->putJson('/api/profile', ['name' => 'Ana', 'preferences' => ['interests' => ['otra-cosa']]])->assertStatus(422);

    $this->post('/api/profile/avatar', ['avatar' => UploadedFile::fake()->image('yo.jpg')], ['Accept' => 'application/json'])
        ->assertOk()
        ->assertJsonPath('success', true);
});

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * La verificación del correo no bloquea rutas (no se usa el middleware "verified"):
 * solo se envía el enlace al registrarse y se muestra un aviso hasta confirmarlo.
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** Preferencias por defecto; las guardadas en "preferences" las sobrescriben */
    public const DEFAULT_PREFERENCES = [
        'notifications' => ['bookings' => true, 'promotions' => true],
        'interests' => [],
        'pace' => null,
    ];

    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'email_verified_at',
        'avatar',
        'phone',
        'bio',
        'preferences',
        'is_active',
        'deactivated_at',
        'deactivated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
        ];
    }

    public function locales()
    {
        return $this->hasMany(Locale::class);
    }

    public function candelaria()
    {
        return $this->hasMany(Candelaria::class);
    }

    public function deactivatedBy()
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }

    public function experiencias()
    {
        return $this->hasMany(Experiencia::class);
    }

    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }

    public function promociones()
    {
        return $this->hasMany(Promocion::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function eventAttendances()
    {
        return $this->hasMany(EventAttendee::class);
    }

    public function coupons()
    {
        return $this->hasMany(UserCoupon::class);
    }

    public function isTourist(): bool
    {
        return $this->role === 'tourist';
    }

    public function isSocio(): bool
    {
        return $this->role === 'socio';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Preferencias completas: las guardadas sobre los valores por defecto */
    public function resolvedPreferences(): array
    {
        return array_replace_recursive(self::DEFAULT_PREFERENCES, $this->preferences ?? []);
    }

    /** ¿Quiere recibir correos de este tipo? (bookings, promotions) */
    public function wantsEmail(string $type): bool
    {
        return (bool) ($this->resolvedPreferences()['notifications'][$type] ?? true);
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return $this->role === $roles;
    }
}

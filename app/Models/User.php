<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
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

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return $this->role === $roles;
    }
}

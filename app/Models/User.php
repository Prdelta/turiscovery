<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
        ];
    }

    // ========== Relationships ==========

    public function locales()
    {
        return $this->hasMany(Locale::class);
    }

    public function candelaria()
    {
        return $this->hasMany(Candelaria::class);
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

    // ========== Role Helper Methods ==========

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

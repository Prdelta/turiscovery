<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOwner;
use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasLocation;
use App\Models\Concerns\Reviewable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Locale extends Model
{
    use BelongsToOwner, HasFactory, HasImages, HasLocation, Reviewable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'address',
        'street',
        'street_number',
        'district',
        'city',
        'region',
        'country',
        'postal_code',
        'address_notes',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'location',
        'category',
        'images',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ========== Relationships ==========

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

    // ========== Query Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}

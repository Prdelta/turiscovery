<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOwner;
use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasLocation;
use App\Models\Concerns\Reviewable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Experiencia extends Model
{
    use BelongsToOwner, HasFactory, HasImages, HasLocation, Reviewable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'locale_id',
        'anfitrion_id',
        'title',
        'description',
        'content',
        'location',
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
        'difficulty',
        'duration_hours',
        'price_pen',
        'max_participants',
        'images',
        'tags',
        'is_active',
    ];

    protected $casts = [
        'tags' => 'array',
        'price_pen' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ========== Relationships ==========

    public function anfitrion()
    {
        return $this->belongsTo(Anfitrion::class);
    }

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // ========== Query Scopes ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeWithinBudget($query, $maxPrice)
    {
        return $query->where('price_pen', '<=', $maxPrice);
    }

    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }
}

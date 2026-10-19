<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Experiencia extends Model
{
    use HasFactory, SoftDeletes;

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
        'latitude' => 'float',
        'longitude' => 'float',
        'images' => 'array',
        'tags' => 'array',
        'price_pen' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url', 'formatted_address', 'short_address', 'coordinates'];

    // ========== Relationships ==========

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function anfitrion()
    {
        return $this->belongsTo(Anfitrion::class);
    }

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
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

    // ========== Geolocation Methods ==========

    public static function nearby(float $latitude, float $longitude, int $radiusMeters = 5000)
    {
        return self::selectRaw("
                *,
                ST_Distance(
                    location,
                    ST_GeogFromText('POINT({$longitude} {$latitude})')
                ) as distance
            ")
            ->whereNotNull('location')
            ->whereRaw("
                ST_DWithin(
                    location,
                    ST_GeogFromText('POINT({$longitude} {$latitude})'),
                    ?
                )
            ", [$radiusMeters])
            ->orderBy('distance');
    }

    public function setLocationFromCoordinates(float $latitude, float $longitude)
    {
        $this->location = DB::raw("ST_GeogFromText('POINT({$longitude} {$latitude})')");
        return $this;
    }

    // ========== Accessors ==========

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    public function getImageUrlAttribute()
    {
        return $this->images[0] ?? null;
    }

    public function getFormattedAddressAttribute()
    {
        $parts = [];
        if ($this->street && $this->street_number) {
            $parts[] = "{$this->street} N° {$this->street_number}";
        } elseif ($this->street) {
            $parts[] = $this->street;
        }
        if ($this->district) $parts[] = $this->district;
        if ($this->city) {
            $cityPart = $this->city;
            if ($this->region && $this->region !== $this->city) $cityPart .= ", {$this->region}";
            $parts[] = $cityPart;
        }
        if ($this->country) $parts[] = $this->country;
        if (empty($parts) && $this->address) return $this->address;
        return implode(', ', $parts) ?: 'Dirección no especificada';
    }

    public function getShortAddressAttribute()
    {
        $parts = [];
        if ($this->street && $this->street_number) $parts[] = "{$this->street} N° {$this->street_number}";
        elseif ($this->street) $parts[] = $this->street;
        if ($this->district) $parts[] = $this->district;
        elseif ($this->city) $parts[] = $this->city;
        if (empty($parts) && $this->address) return $this->address;
        return implode(', ', $parts) ?: 'Sin dirección';
    }

    public function getCoordinatesAttribute()
    {
        return ['lat' => $this->latitude, 'lng' => $this->longitude];
    }
}

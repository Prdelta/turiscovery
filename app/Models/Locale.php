<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Locale extends Model
{
    use HasFactory, SoftDeletes;

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
        'latitude' => 'float',
        'longitude' => 'float',
        'images' => 'array',
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url', 'formatted_address', 'short_address', 'coordinates'];

    // ========== Relationships ==========

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // ========== Geolocation Methods ==========

    /**
     * Find locales near a given point within a radius (in meters)
     */
    public static function nearby(float $latitude, float $longitude, int $radiusMeters = 5000)
    {
        return self::selectRaw("
                *,
                ST_Distance(
                    location,
                    ST_GeogFromText('POINT({$longitude} {$latitude})')
                ) as distance
            ")
            ->whereRaw("
                ST_DWithin(
                    location,
                    ST_GeogFromText('POINT({$longitude} {$latitude})'),
                    ?
                )
            ", [$radiusMeters])
            ->orderBy('distance');
    }

    /**
     * Set location from latitude and longitude
     */
    public function setLocationFromCoordinates(float $latitude, float $longitude)
    {
        $this->location = DB::raw("ST_GeogFromText('POINT({$longitude} {$latitude})')");
        return $this;
    }

    public function getImageUrlAttribute()
    {
        return $this->images[0] ?? null;
    }

    /**
     * Get formatted address (human-readable)
     */
    public function getFormattedAddressAttribute()
    {
        $parts = [];

        // Calle y número
        if ($this->street && $this->street_number) {
            $parts[] = "{$this->street} N° {$this->street_number}";
        } elseif ($this->street) {
            $parts[] = $this->street;
        }

        // Distrito
        if ($this->district) {
            $parts[] = $this->district;
        }

        // Ciudad, Región
        if ($this->city) {
            $cityPart = $this->city;
            if ($this->region && $this->region !== $this->city) {
                $cityPart .= ", {$this->region}";
            }
            $parts[] = $cityPart;
        }

        // País
        if ($this->country) {
            $parts[] = $this->country;
        }

        // Si no hay partes detalladas, usar el campo address legacy
        if (empty($parts) && $this->address) {
            return $this->address;
        }

        return implode(', ', $parts) ?: 'Dirección no especificada';
    }

    /**
     * Get short address (for cards/lists)
     */
    public function getShortAddressAttribute()
    {
        $parts = [];

        if ($this->street && $this->street_number) {
            $parts[] = "{$this->street} N° {$this->street_number}";
        } elseif ($this->street) {
            $parts[] = $this->street;
        }

        if ($this->district) {
            $parts[] = $this->district;
        } elseif ($this->city) {
            $parts[] = $this->city;
        }

        // Si no hay partes detalladas, usar address legacy
        if (empty($parts) && $this->address) {
            return $this->address;
        }

        return implode(', ', $parts) ?: 'Sin dirección';
    }

    /**
     * Get coordinates as array
     */
    public function getCoordinatesAttribute()
    {
        return [
            'lat' => $this->latitude,
            'lng' => $this->longitude
        ];
    }
}

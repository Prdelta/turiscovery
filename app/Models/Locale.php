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
        'phone',
        'email',
        'website',
        'location',
        'category',
        'images',
        'is_active',
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
    ];

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

    /**
     * Get latitude from location
     */
    public function getLatitudeAttribute()
    {
        if ($this->location) {
            $result = DB::selectOne("SELECT ST_Y(location::geometry) as lat FROM locales WHERE id = ?", [$this->id]);
            return $result ? $result->lat : null;
        }
        return null;
    }

    /**
     * Get longitude from location
     */
    public function getLongitudeAttribute()
    {
        if ($this->location) {
            $result = DB::selectOne("SELECT ST_X(location::geometry) as lng FROM locales WHERE id = ?", [$this->id]);
            return $result ? $result->lng : null;
        }
        return null;
    }
}

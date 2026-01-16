<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Evento extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'locale_id',
        'title',
        'description',
        'content',
        'start_time',
        'end_time',
        'location',
        'address',
        'ticket_price',
        'category',
        'images',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'images' => 'array',
        'ticket_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ========== Relationships ==========

    public function user()
    {
        return $this->belongsTo(User::class);
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

    // ========== Query Scopes (Time-Sensitive Filtering) ==========

    /**
     * Filter only active events (is_active = true AND not expired)
     * This is the PRIMARY method for filtering time-sensitive content
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('end_time', '>', now());
    }

    /**
     * Filter expired events (for analytics/historical data)
     */
    public function scopeExpired($query)
    {
        return $query->where('end_time', '<', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())
            ->where('is_active', true);
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_time', '<=', now())
            ->where('end_time', '>', now())
            ->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeFree($query)
    {
        return $query->whereNull('ticket_price')
            ->orWhere('ticket_price', 0);
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

    public function getIsExpiredAttribute()
    {
        return $this->end_time < now();
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }
}

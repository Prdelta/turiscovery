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
        'title',
        'description',
        'content',
        'location',
        'address',
        'difficulty',
        'duration_hours',
        'price_pen',
        'max_participants',
        'images',
        'tags',
        'is_active',
    ];

    protected $casts = [
        'images' => 'array',
        'tags' => 'array',
        'price_pen' => 'decimal:2',
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
}

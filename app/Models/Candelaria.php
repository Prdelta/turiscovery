<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candelaria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'candelaria';

    protected $fillable = [
        'user_id',
        'locale_id',
        'title',
        'description',
        'content',
        'event_date',
        'category',
        'images',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'images' => 'array',
        'event_date' => 'datetime',
        'is_featured' => 'boolean',
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

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now());
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

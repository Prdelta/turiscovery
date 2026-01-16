<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promocion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'promociones';

    protected $fillable = [
        'user_id',
        'locale_id',
        'title',
        'description',
        'discount_type',
        'discount_percentage',
        'discount_amount',
        'original_price',
        'final_price',
        'start_date',
        'end_date',
        'terms_conditions',
        'redemption_code',
        'images',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'discount_amount' => 'decimal:2',
        'original_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'images' => 'array',
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
     * Filter only active promotions (is_active = true AND not expired)
     * This is the PRIMARY method for filtering time-sensitive content
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('end_date', '>', now())
            ->where('start_date', '<=', now());
    }

    /**
     * Filter expired promotions (for analytics/historical data)
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now())
            ->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('discount_type', $type);
    }

    public function scopeByLocale($query, $localeId)
    {
        return $query->where('locale_id', $localeId);
    }

    // ========== Accessors ==========

    public function getIsExpiredAttribute()
    {
        return $this->end_date < now();
    }

    public function getIsActiveNowAttribute()
    {
        return $this->start_date <= now() && $this->end_date > now() && $this->is_active;
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

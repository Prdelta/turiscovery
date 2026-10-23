<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOwner;
use App\Models\Concerns\HasImages;
use App\Models\Concerns\Reviewable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promocion extends Model
{
    use BelongsToOwner, HasFactory, HasImages, Reviewable, SoftDeletes;

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
        'is_active' => 'boolean',
    ];

    // ========== Relationships ==========

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }

    // ========== Query Scopes (Time-Sensitive Filtering) ==========

    /**
     * Promociones vigentes: publicadas, ya empezaron y no vencieron
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('end_date', '>', now())
            ->where('start_date', '<=', now());
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
}

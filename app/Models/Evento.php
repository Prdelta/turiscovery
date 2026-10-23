<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOwner;
use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasLocation;
use App\Models\Concerns\Reviewable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evento extends Model
{
    use BelongsToOwner, HasFactory, HasImages, HasLocation, Reviewable, SoftDeletes;

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
        'ticket_price',
        'category',
        'images',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'ticket_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ========== Relationships ==========

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }

    // ========== Query Scopes (Time-Sensitive Filtering) ==========

    /**
     * Eventos visibles: publicados y que todavía no terminan
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('end_time', '>', now());
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
        // Agrupado: sin paréntesis, el orWhere devolvía también eventos de otros filtros
        return $query->where(fn ($q) => $q->whereNull('ticket_price')->orWhere('ticket_price', 0));
    }

    // ========== Accessors ==========

    public function getIsExpiredAttribute()
    {
        return $this->end_time < now();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandelariaDanza extends Model
{
    use HasFactory;

    protected $table = 'candelaria_danzas';

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'description',
        'history',
        'image_url',
        'video_url',
        'region',
        'characteristics',
        'order',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con el usuario que agregó la danza
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para danzas activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para danzas destacadas
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para danzas mestizas
     */
    public function scopeMestizas($query)
    {
        return $query->where('type', 'mestiza');
    }

    /**
     * Scope para danzas autóctonas
     */
    public function scopeAutoctonas($query)
    {
        return $query->where('type', 'autoctona');
    }

    /**
     * Scope para ordenar
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandelariaProgramItem extends Model
{
    public const TYPES = ['misa', 'procesion', 'concurso', 'entrada', 'parada', 'otro'];

    protected $fillable = [
        'title',
        'description',
        'type',
        'starts_at',
        'ends_at',
        'location_name',
        'latitude',
        'longitude',
        'route',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'route' => 'array',
        'is_active' => 'boolean',
    ];

    public function performances(): HasMany
    {
        return $this->hasMany(CandelariaPerformance::class, 'program_item_id')->orderBy('order_number');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Jornadas que están ocurriendo ahora según su horario */
    public function scopeHappeningNow($query)
    {
        return $query->where('starts_at', '<=', now())->where('ends_at', '>=', now());
    }
}

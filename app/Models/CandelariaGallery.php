<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandelariaGallery extends Model
{
    use HasFactory;

    protected $table = 'candelaria_gallery';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_url',
        'year',
        'order',
        'is_active',
    ];

    protected $casts = [
        'year' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con el usuario que agregó la foto
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para fotos activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para ordenar por año descendente
     */
    public function scopeByYearDesc($query)
    {
        return $query->orderBy('year', 'desc')->orderBy('order', 'asc');
    }

    /**
     * Scope para filtrar por año
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }
}

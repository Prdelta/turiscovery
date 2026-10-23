<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOwner;
use App\Models\Concerns\HasImages;
use App\Models\Concerns\Reviewable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candelaria extends Model
{
    use BelongsToOwner, HasFactory, HasImages, Reviewable, SoftDeletes;

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
        'event_date' => 'datetime',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ========== Relationships ==========

    public function locale()
    {
        return $this->belongsTo(Locale::class);
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
}

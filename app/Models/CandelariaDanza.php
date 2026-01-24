<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandelariaDanza extends Model
{
    protected $table = 'candelaria_danzas';

    protected $fillable = [
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
}

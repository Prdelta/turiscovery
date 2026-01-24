<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandelariaGallery extends Model
{
    protected $table = 'candelaria_gallery';

    protected $fillable = [
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
}

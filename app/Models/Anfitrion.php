<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anfitrion extends Model
{
    public const LANGUAGES = ['quechua', 'aimara', 'espanol', 'ingles'];

    protected $table = 'anfitriones';

    protected $fillable = [
        'user_id',
        'name',
        'community',
        'role_title',
        'languages',
        'photo_url',
        'story',
        'greeting',
        'greeting_translation',
        'audio_url',
        'is_active',
    ];

    protected $casts = [
        'languages' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function experiencias(): HasMany
    {
        return $this->hasMany(Experiencia::class, 'anfitrion_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

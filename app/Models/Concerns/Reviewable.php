<?php

namespace App\Models\Concerns;

use App\Models\Favorite;
use App\Models\Review;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Contenido que se puede reseñar y guardar en favoritos.
 * Si la consulta trae withAvg('reviews', 'rating') o withCount('reviews'),
 * los accesores usan esos valores y no hacen una consulta por elemento.
 */
trait Reviewable
{
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function getAverageRatingAttribute(): float
    {
        if (array_key_exists('reviews_avg_rating', $this->attributes)) {
            return round((float) $this->attributes['reviews_avg_rating'], 1);
        }

        return round((float) ($this->reviews()->avg('rating') ?? 0), 1);
    }

    public function getReviewsCountAttribute($value): int
    {
        return $value !== null ? (int) $value : $this->reviews()->count();
    }
}

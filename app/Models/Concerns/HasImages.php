<?php

namespace App\Models\Concerns;

/**
 * Lista de URLs de imágenes (columna JSON images); la primera es la portada.
 */
trait HasImages
{
    public function initializeHasImages(): void
    {
        $this->mergeCasts(['images' => 'array']);
        $this->append('image_url');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->images[0] ?? null;
    }
}

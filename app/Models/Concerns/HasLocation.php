<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Ubicación de un contenido: columna PostGIS `location` (geography) para las búsquedas
 * por cercanía, y latitude/longitude numéricas para leer sin consultar PostGIS.
 * También arma la dirección legible a partir de sus partes.
 */
trait HasLocation
{
    public function initializeHasLocation(): void
    {
        $this->mergeCasts(['latitude' => 'float', 'longitude' => 'float']);
        $this->append(['formatted_address', 'short_address', 'coordinates']);
    }

    /**
     * Contenido a menos de $radiusMeters de un punto, ordenado por distancia.
     * Las coordenadas van como parámetros enlazados, nunca dentro del SQL.
     */
    public static function nearby(float $latitude, float $longitude, int $radiusMeters = 5000): Builder
    {
        $model = new static();
        $point = 'ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography';

        return static::query()
            ->select($model->getTable() . '.*')
            ->selectRaw("ST_Distance(location, {$point}) as distance", [$longitude, $latitude])
            ->whereNotNull('location')
            ->whereRaw("ST_DWithin(location, {$point}, ?)", [$longitude, $latitude, $radiusMeters])
            ->orderBy('distance');
    }

    /** Guarda el punto en PostGIS y en las columnas numéricas */
    public function setLocationFromCoordinates(float $latitude, float $longitude): static
    {
        // %F formatea el número sin depender del idioma del sistema: no se puede inyectar SQL
        $this->location = DB::raw(sprintf('ST_SetSRID(ST_MakePoint(%F, %F), 4326)::geography', $longitude, $latitude));
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        return $this;
    }

    public function getFormattedAddressAttribute(): string
    {
        $parts = [];
        if ($this->street && $this->street_number) {
            $parts[] = "{$this->street} N° {$this->street_number}";
        } elseif ($this->street) {
            $parts[] = $this->street;
        }
        if ($this->district) {
            $parts[] = $this->district;
        }
        if ($this->city) {
            $parts[] = $this->region && $this->region !== $this->city ? "{$this->city}, {$this->region}" : $this->city;
        }
        if ($this->country) {
            $parts[] = $this->country;
        }
        if (empty($parts) && $this->address) {
            return $this->address;
        }

        return implode(', ', $parts) ?: 'Dirección no especificada';
    }

    public function getShortAddressAttribute(): string
    {
        $parts = [];
        if ($this->street && $this->street_number) {
            $parts[] = "{$this->street} N° {$this->street_number}";
        } elseif ($this->street) {
            $parts[] = $this->street;
        }
        if ($this->district) {
            $parts[] = $this->district;
        } elseif ($this->city) {
            $parts[] = $this->city;
        }
        if (empty($parts) && $this->address) {
            return $this->address;
        }

        return implode(', ', $parts) ?: 'Sin dirección';
    }

    public function getCoordinatesAttribute(): array
    {
        return ['lat' => $this->latitude, 'lng' => $this->longitude];
    }
}

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
    public function scopeNearby(Builder $query, float $latitude, float $longitude, int $radiusMeters = 5000): Builder
    {
        if (DB::getDriverName() !== 'pgsql') {
            return $this->scopeNearbyWithoutPostgis($query, $latitude, $longitude, $radiusMeters);
        }

        $point = 'ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography';

        return $query
            ->select($this->getTable().'.*')
            ->selectRaw("ST_Distance(location, {$point}) as distance", [$longitude, $latitude])
            ->whereNotNull('location')
            ->whereRaw("ST_DWithin(location, {$point}, ?)", [$longitude, $latitude, $radiusMeters])
            ->orderBy('distance');
    }

    /**
     * Cercanía sin PostGIS (SQLite de los tests): recuadro aproximado sobre latitude/longitude
     * ordenado por la distancia plana al cuadrado (SQLite no siempre trae sqrt). Solo para pruebas
     * y desarrollo sin PostGIS; en producción se usa PostGIS.
     */
    private function scopeNearbyWithoutPostgis(Builder $query, float $latitude, float $longitude, int $radiusMeters): Builder
    {
        $dLat = $radiusMeters / 111_320;
        $dLng = $radiusMeters / (111_320 * max(cos(deg2rad($latitude)), 0.01));
        $kx = 111_320 * cos(deg2rad($latitude));

        return $query
            ->select($this->getTable().'.*')
            ->selectRaw('((latitude - ?) * (latitude - ?) * ? + (longitude - ?) * (longitude - ?) * ?) as distance', [
                $latitude, $latitude, 111_320 ** 2, $longitude, $longitude, $kx ** 2,
            ])
            ->whereBetween('latitude', [$latitude - $dLat, $latitude + $dLat])
            ->whereBetween('longitude', [$longitude - $dLng, $longitude + $dLng])
            ->orderBy('distance');
    }

    /** Guarda el punto en PostGIS y en las columnas numéricas */
    public function setLocationFromCoordinates(float $latitude, float $longitude): static
    {
        // %F formatea el número sin depender del idioma del sistema: no se puede inyectar SQL
        $this->location = DB::getDriverName() === 'pgsql'
            ? DB::raw(sprintf('ST_SetSRID(ST_MakePoint(%F, %F), 4326)::geography', $longitude, $latitude))
            : sprintf('POINT(%F %F)', $longitude, $latitude);
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

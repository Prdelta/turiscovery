<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

/**
 * Limita el parámetro per_page: un ?per_page=999999 cargaría de más la base de datos.
 */
trait ValidatesPaginationTrait
{
    /**
     * per_page del request dentro de los límites de config/pagination.php.
     * Un valor no numérico o menor al mínimo usa el default del recurso; uno mayor al máximo, el máximo.
     */
    protected function getValidatedPerPage(Request $request, ?string $resourceType = null): int
    {
        $default = $this->getDefaultPerPage($resourceType);
        $perPage = $request->get('per_page', $default);

        if (! is_numeric($perPage) || (int) $perPage < config('pagination.min_per_page', 1)) {
            return $default;
        }

        return min((int) $perPage, config('pagination.max_per_page', 100));
    }

    protected function getDefaultPerPage(?string $resourceType): int
    {
        return config("pagination.defaults.{$resourceType}", config('pagination.default', 15));
    }
}

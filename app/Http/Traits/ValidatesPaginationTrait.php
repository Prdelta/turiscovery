<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

/**
 * Trait para validar y sanitizar parámetros de paginación
 *
 * Previene ataques de DOS mediante solicitudes con per_page extremadamente alto
 * (ej: ?per_page=999999 que causaría carga excesiva en la base de datos)
 */
trait ValidatesPaginationTrait
{
    /**
     * Valida y sanitiza el parámetro per_page
     *
     * @param Request $request Request actual
     * @param string|null $resourceType Tipo de recurso (eventos, experiencias, etc.) para obtener default desde config
     * @param int|null $defaultOverride Override manual del default (opcional)
     * @param int|null $maxOverride Override manual del máximo (opcional)
     * @return int Valor de per_page validado y sanitizado
     *
     * @example
     * // Usando defaults de configuración
     * $perPage = $this->getValidatedPerPage($request, 'eventos');
     *
     * // Usando valores personalizados
     * $perPage = $this->getValidatedPerPage($request, null, 20, 50);
     */
    protected function getValidatedPerPage(
        Request $request,
        ?string $resourceType = null,
        ?int $defaultOverride = null,
        ?int $maxOverride = null
    ): int {
        // Obtener defaults desde configuración o usar valores proporcionados
        $default = $defaultOverride ?? $this->getDefaultPerPage($resourceType);
        $max = $maxOverride ?? config('pagination.max_per_page', 100);
        $min = config('pagination.min_per_page', 1);

        // Obtener valor del request
        $perPage = $request->get('per_page', $default);

        // Validar que sea numérico
        if (!is_numeric($perPage)) {
            return $default;
        }

        // Convertir a entero
        $perPage = (int) $perPage;

        // Aplicar límites
        if ($perPage < $min) {
            return $default;
        }

        if ($perPage > $max) {
            return $max;
        }

        return $perPage;
    }

    /**
     * Obtiene el valor default de per_page desde configuración
     *
     * @param string|null $resourceType Tipo de recurso
     * @return int Valor default de per_page
     */
    protected function getDefaultPerPage(?string $resourceType): int
    {
        if ($resourceType && config()->has("pagination.defaults.{$resourceType}")) {
            return config("pagination.defaults.{$resourceType}");
        }

        return config('pagination.default', 15);
    }

    /**
     * Valida parámetros de ordenamiento para prevenir inyección SQL
     *
     * @param Request $request
     * @param array $allowedColumns Columnas permitidas para ordenar
     * @param string $defaultColumn Columna default
     * @param string $defaultDirection Dirección default (asc/desc)
     * @return array ['column' => string, 'direction' => string]
     *
     * @example
     * $sort = $this->getValidatedSort($request, ['name', 'created_at', 'price'], 'created_at');
     * $query->orderBy($sort['column'], $sort['direction']);
     */
    protected function getValidatedSort(
        Request $request,
        array $allowedColumns,
        string $defaultColumn = 'created_at',
        string $defaultDirection = 'desc'
    ): array {
        $column = $request->get('sort_by', $defaultColumn);
        $direction = $request->get('sort_direction', $defaultDirection);

        // Validar columna
        if (!in_array($column, $allowedColumns)) {
            $column = $defaultColumn;
        }

        // Validar dirección
        if (!in_array(strtolower($direction), ['asc', 'desc'])) {
            $direction = $defaultDirection;
        }

        return [
            'column' => $column,
            'direction' => strtolower($direction),
        ];
    }

    /**
     * Obtiene número de página validado
     *
     * @param Request $request
     * @return int Número de página válido (mínimo 1)
     */
    protected function getValidatedPage(Request $request): int
    {
        $page = $request->get('page', 1);

        if (!is_numeric($page)) {
            return 1;
        }

        $page = (int) $page;

        return max(1, $page);
    }
}

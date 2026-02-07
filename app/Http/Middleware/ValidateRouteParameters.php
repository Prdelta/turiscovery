<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para validar parámetros de ruta
 *
 * Previene que IDs no numéricos o malformados causen errores 500.
 * En su lugar, retorna 400 Bad Request con un mensaje claro.
 *
 * Esto mejora la seguridad al:
 * 1. Prevenir errores reveladores en producción
 * 2. Validar entrada antes de llegar a la base de datos
 * 3. Dar respuestas apropiadas (400 en lugar de 500)
 */
class ValidateRouteParameters
{
    /**
     * Parámetros que deben ser numéricos
     *
     * @var array<string>
     */
    protected array $numericParams = [
        'id',
        'locale_id',
        'evento_id',
        'experiencia_id',
        'promocion_id',
        'booking_id',
        'review_id',
        'user_id',
        'local_id',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Validar cada parámetro numérico
        foreach ($this->numericParams as $param) {
            $value = $request->route($param);

            // Si el parámetro existe y no es numérico, rechazar
            if ($value !== null && !$this->isValidNumeric($value)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de parámetro inválido. Se esperaba un ID numérico.',
                    'errors' => [
                        $param => ["El parámetro '{$param}' debe ser un número entero positivo."]
                    ]
                ], 400);
            }

            // Validar que sea positivo si existe
            if ($value !== null && (int) $value <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID inválido',
                    'errors' => [
                        $param => ["El parámetro '{$param}' debe ser mayor que 0."]
                    ]
                ], 400);
            }
        }

        return $next($request);
    }

    /**
     * Verifica si un valor es numérico válido
     *
     * @param mixed $value
     * @return bool
     */
    protected function isValidNumeric($value): bool
    {
        // Rechazar strings que no sean dígitos
        if (is_string($value) && !ctype_digit($value)) {
            return false;
        }

        // Verificar que sea numérico
        if (!is_numeric($value)) {
            return false;
        }

        return true;
    }
}

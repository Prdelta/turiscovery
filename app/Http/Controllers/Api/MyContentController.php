<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ValidatesPaginationTrait;
use App\Models\Candelaria;
use App\Models\Evento;
use App\Models\Experiencia;
use App\Models\Locale;
use App\Models\Promocion;
use Illuminate\Http\Request;

/**
 * Contenido gestionado por el socio (incluye inactivos).
 * Los administradores ven el contenido de todos los socios.
 */
class MyContentController extends Controller
{
    use ValidatesPaginationTrait;

    protected array $models = [
        'locales' => Locale::class,
        'eventos' => Evento::class,
        'experiencias' => Experiencia::class,
        'promociones' => Promocion::class,
        'candelaria' => Candelaria::class,
    ];

    public function index(Request $request, string $type)
    {
        abort_unless(isset($this->models[$type]), 404);

        $query = $this->models[$type]::query()
            ->with('user:id,name,email')
            ->managedBy($request->user());

        if ($request->filled('search')) {
            $column = $type === 'locales' ? 'name' : 'title';
            $query->where($column, 'ILIKE', '%'.$request->search.'%');
        }

        $items = $query->latest()->paginate($this->getValidatedPerPage($request, $type));

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }
}

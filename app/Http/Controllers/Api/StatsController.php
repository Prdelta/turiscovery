<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Experiencia;
use App\Models\Locale;
use App\Models\Promocion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    /**
     * Get user statistics (counts only, super fast)
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $userRole = $request->user()->role;

        // Cache key único por usuario
        $cacheKey = "user_stats_{$userId}";

        // Cachear por 5 minutos (300 segundos)
        $stats = Cache::remember($cacheKey, 300, function () use ($userId, $userRole) {
            if ($userRole === 'admin') {
                // Admin ve estadísticas globales
                return [
                    'locales' => Locale::count(),
                    'eventos' => Evento::count(),
                    'experiencias' => Experiencia::count(),
                    'promociones' => Promocion::count(),
                ];
            } else {
                // Usuario normal ve solo su contenido
                return [
                    'locales' => Locale::where('user_id', $userId)->count(),
                    'eventos' => Evento::where('user_id', $userId)->count(),
                    'experiencias' => Experiencia::where('user_id', $userId)->count(),
                    'promociones' => Promocion::where('user_id', $userId)->count(),
                ];
            }
        });

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get recent content (optimized with pagination and eager loading)
     */
    public function recentContent(Request $request)
    {
        $userId = $request->user()->id;
        $userRole = $request->user()->role;
        $filter = $request->get('filter', 'all'); // all, locales, eventos, etc.
        $limit = $request->get('limit', 6); // Default 6 items

        $items = [];

        // Locales
        if ($filter === 'all' || $filter === 'locales') {
            $query = Locale::with('user:id,name');

            if ($userRole !== 'admin') {
                $query->where('user_id', $userId);
            }

            $locales = $query->latest()->limit($limit)->get()->map(function ($item) {
                $item->contentType = 'locales';

                return $item;
            });

            $items = array_merge($items, $locales->toArray());
        }

        // Eventos
        if ($filter === 'all' || $filter === 'eventos') {
            $query = Evento::with('user:id,name');

            if ($userRole !== 'admin') {
                $query->where('user_id', $userId);
            }

            $eventos = $query->latest()->limit($limit)->get()->map(function ($item) {
                $item->contentType = 'eventos';

                return $item;
            });

            $items = array_merge($items, $eventos->toArray());
        }

        // Experiencias
        if ($filter === 'all' || $filter === 'experiencias') {
            $query = Experiencia::with('user:id,name');

            if ($userRole !== 'admin') {
                $query->where('user_id', $userId);
            }

            $experiencias = $query->latest()->limit($limit)->get()->map(function ($item) {
                $item->contentType = 'experiencias';

                return $item;
            });

            $items = array_merge($items, $experiencias->toArray());
        }

        // Promociones
        if ($filter === 'all' || $filter === 'promociones') {
            $query = Promocion::with('user:id,name');

            if ($userRole !== 'admin') {
                $query->where('user_id', $userId);
            }

            $promociones = $query->latest()->limit($limit)->get()->map(function ($item) {
                $item->contentType = 'promociones';

                return $item;
            });

            $items = array_merge($items, $promociones->toArray());
        }

        // Ordenar por fecha de creación (más reciente primero)
        usort($items, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        // Limitar al número solicitado
        $items = array_slice($items, 0, $limit);

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }
}

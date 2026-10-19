<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CandelariaProgramItem;
use App\Services\CandelariaLive;
use Illuminate\Http\Request;

/**
 * Programa y seguimiento en vivo de la Festividad de la Candelaria (público).
 * No pasa por el caché de la API: los datos cambian minuto a minuto.
 */
class CandelariaLiveController extends Controller
{
    /** Programa de la edición: la del año pedido, la en curso o próxima, o la última */
    public function program(Request $request)
    {
        $year = $request->integer('year') ?: $this->defaultYear();

        $items = CandelariaProgramItem::active()
            ->whereYear('starts_at', $year)
            ->withCount([
                'performances',
                'performances as done_count' => fn ($q) => $q->whereIn('status', ['done', 'skipped']),
            ])
            ->orderBy('starts_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['year' => $year, 'items' => $items],
        ]);
    }

    public function show($id)
    {
        $item = CandelariaProgramItem::active()
            ->with(['performances.danza:id,name,type,image_url'])
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function live(CandelariaLive $live)
    {
        return response()->json(['success' => true, 'data' => $live->state()]);
    }

    private function defaultYear(): int
    {
        $upcoming = CandelariaProgramItem::active()->where('ends_at', '>=', now())->orderBy('starts_at')->first();
        $latest = CandelariaProgramItem::active()->orderByDesc('starts_at')->first();
        $item = $upcoming ?? $latest;

        return $item ? $item->starts_at->year : now()->year;
    }
}

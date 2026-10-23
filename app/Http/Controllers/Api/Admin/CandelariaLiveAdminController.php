<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandelariaPerformance;
use App\Models\CandelariaProgramItem;
use App\Services\CandelariaLive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Gestión del programa y control en vivo de los conjuntos (organizadores = admins)
 */
class CandelariaLiveAdminController extends Controller
{
    /* ---------- Jornadas del programa ---------- */

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => CandelariaProgramItem::withCount('performances')->orderByDesc('starts_at')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $item = CandelariaProgramItem::create($this->validateItem($request));

        return response()->json(['success' => true, 'message' => 'Jornada creada', 'data' => $item], 201);
    }

    public function update(Request $request, CandelariaProgramItem $item)
    {
        $item->update($this->validateItem($request, true));

        return response()->json(['success' => true, 'message' => 'Jornada actualizada', 'data' => $item]);
    }

    public function destroy(CandelariaProgramItem $item)
    {
        $item->delete();

        return response()->json(['success' => true, 'message' => 'Jornada eliminada']);
    }

    private function validateItem(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes|required' : 'required';

        return $request->validate([
            'title' => "{$required}|string|max:255",
            'description' => 'nullable|string',
            'type' => [$partial ? 'sometimes' : 'required', Rule::in(CandelariaProgramItem::TYPES)],
            'starts_at' => "{$required}|date",
            'ends_at' => "{$required}|date|after:starts_at",
            'location_name' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'route' => 'nullable|array|max:500',
            'route.*' => 'array|size:2',
            'route.*.*' => 'numeric',
            'is_active' => 'boolean',
        ]);
    }

    /* ---------- Conjuntos de una jornada ---------- */

    public function performances(CandelariaProgramItem $item, CandelariaLive $live)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'item' => $item,
                'performances' => $item->performances()->with('danza:id,name,type')->get(),
                'state' => $live->state($item, 3),
            ],
        ]);
    }

    /** Agrega conjuntos al final del orden (uno o varios a la vez) */
    public function addPerformances(Request $request, CandelariaProgramItem $item)
    {
        $validated = $request->validate([
            'performances' => 'required|array|min:1|max:300',
            'performances.*.group_name' => 'required|string|max:255',
            'performances.*.candelaria_danza_id' => 'nullable|exists:candelaria_danzas,id',
        ]);

        $next = (int) $item->performances()->max('order_number');
        foreach ($validated['performances'] as $row) {
            $item->performances()->create([
                'group_name' => $row['group_name'],
                'candelaria_danza_id' => $row['candelaria_danza_id'] ?? null,
                'order_number' => ++$next,
            ]);
        }

        $count = count($validated['performances']);

        return response()->json(['success' => true, 'message' => "{$count} conjuntos agregados"], 201);
    }

    public function updatePerformance(Request $request, CandelariaPerformance $performance)
    {
        $validated = $request->validate([
            'group_name' => 'sometimes|required|string|max:255',
            'candelaria_danza_id' => 'nullable|exists:candelaria_danzas,id',
            'order_number' => 'sometimes|integer|min:1',
            'status' => ['sometimes', Rule::in(CandelariaPerformance::STATUSES)],
        ]);

        if (isset($validated['status'])) {
            if ($validated['status'] === 'performing') {
                // Solo un conjunto pasando a la vez por jornada
                CandelariaPerformance::where('program_item_id', $performance->program_item_id)
                    ->where('status', 'performing')
                    ->where('id', '!=', $performance->id)
                    ->update(['status' => 'done', 'finished_at' => now()]);
                $validated['started_at'] = now();
                $validated['finished_at'] = null;
            } elseif (in_array($validated['status'], ['done', 'skipped'], true)) {
                $validated['finished_at'] = now();
            } else {
                $validated['started_at'] = null;
                $validated['finished_at'] = null;
            }
        }

        $performance->update($validated);

        return response()->json(['success' => true, 'data' => $performance]);
    }

    public function destroyPerformance(CandelariaPerformance $performance)
    {
        $performance->delete();

        return response()->json(['success' => true, 'message' => 'Conjunto eliminado']);
    }

    /**
     * "Pasó el siguiente": termina el conjunto actual y empieza el próximo pendiente.
     * Es el botón que usa el organizador en la calle durante el desfile.
     */
    public function advance(CandelariaProgramItem $item, CandelariaLive $live)
    {
        DB::transaction(function () use ($item) {
            $item->performances()->reorder()->where('status', 'performing')->update(['status' => 'done', 'finished_at' => now()]);

            $next = $item->performances()->where('status', 'pending')->reorder('order_number')->lockForUpdate()->first();
            $next?->update(['status' => 'performing', 'started_at' => now(), 'finished_at' => null]);
        });

        return response()->json(['success' => true, 'data' => $live->state($item->fresh(), 3)]);
    }
}

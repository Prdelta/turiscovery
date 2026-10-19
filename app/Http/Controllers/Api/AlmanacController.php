<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlmanacEntry;
use App\Models\CandelariaProgramItem;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Almanaque vivo: qué pasa en Puno entre dos fechas. Junta las fiestas que se
 * repiten cada año, los eventos publicados por socios y el programa de la Candelaria.
 */
class AlmanacController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'days' => 'nullable|integer|min:1|max:62',
        ]);

        $tz = 'America/Lima';
        $from = $request->filled('from') ? Carbon::parse($request->from, $tz)->startOfDay() : Carbon::now($tz)->startOfDay();
        $to = $from->copy()->addDays($request->integer('days', 7) - 1)->endOfDay();

        // Las fechas de la base están en la zona horaria de la aplicación
        $qFrom = $from->copy()->setTimezone(config('app.timezone'));
        $qTo = $to->copy()->setTimezone(config('app.timezone'));

        $items = collect();

        foreach (AlmanacEntry::active()->get() as $entry) {
            foreach ($entry->occurrencesBetween($from, $to) as $occ) {
                $items->push([
                    'source' => 'almanaque',
                    'id' => $entry->id,
                    'title' => $entry->title,
                    'description' => $entry->description,
                    'kind' => $entry->kind,
                    'place_name' => $entry->place_name,
                    'province' => $entry->province,
                    'latitude' => $entry->latitude,
                    'longitude' => $entry->longitude,
                    'starts_at' => $occ['starts']->toIso8601String(),
                    'ends_at' => $occ['ends']->toIso8601String(),
                    'all_day' => true,
                ]);
            }
        }

        Evento::where('is_active', true)
            ->where('start_time', '<=', $qTo)
            ->where('end_time', '>=', $qFrom)
            ->orderBy('start_time')
            ->limit(100)
            ->get(['id', 'title', 'category', 'start_time', 'end_time', 'address', 'latitude', 'longitude'])
            ->each(fn ($e) => $items->push([
                'source' => 'evento',
                'id' => $e->id,
                'title' => $e->title,
                'kind' => 'evento',
                'place_name' => $e->address,
                'latitude' => $e->latitude,
                'longitude' => $e->longitude,
                'starts_at' => $e->start_time->toIso8601String(),
                'ends_at' => $e->end_time->toIso8601String(),
                'all_day' => false,
            ]));

        CandelariaProgramItem::active()
            ->where('starts_at', '<=', $qTo)
            ->where('ends_at', '>=', $qFrom)
            ->get()
            ->each(fn ($p) => $items->push([
                'source' => 'candelaria',
                'id' => $p->id,
                'title' => $p->title,
                'kind' => 'candelaria',
                'place_name' => $p->location_name,
                'latitude' => $p->latitude,
                'longitude' => $p->longitude,
                'starts_at' => $p->starts_at->toIso8601String(),
                'ends_at' => $p->ends_at->toIso8601String(),
                'all_day' => false,
            ]));

        return response()->json([
            'success' => true,
            'data' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'items' => $items->sortBy('starts_at')->values(),
            ],
        ]);
    }

    /* ---------- Administración ---------- */

    public function adminIndex()
    {
        return response()->json([
            'success' => true,
            'data' => AlmanacEntry::orderBy('month')->orderBy('day')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $entry = AlmanacEntry::create($this->validated($request));

        return response()->json(['success' => true, 'message' => 'Fecha agregada al almanaque', 'data' => $entry], 201);
    }

    public function update(Request $request, AlmanacEntry $entry)
    {
        $entry->update($this->validated($request));

        return response()->json(['success' => true, 'message' => 'Fecha actualizada', 'data' => $entry]);
    }

    public function destroy(AlmanacEntry $entry)
    {
        $entry->delete();

        return response()->json(['success' => true, 'message' => 'Fecha eliminada']);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'kind' => ['required', Rule::in(AlmanacEntry::KINDS)],
            'place_name' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'month' => 'required|integer|between:1,12',
            'day' => 'required|integer|between:1,31',
            'duration_days' => 'nullable|integer|min:1|max:60',
            'is_active' => 'boolean',
        ]);

        // El día debe existir en ese mes (se acepta el 29 de febrero)
        $maxDay = Carbon::create(2024, $data['month'], 1)->daysInMonth;
        abort_if($data['day'] > $maxDay, 422, 'Ese día no existe en el mes elegido.');

        return $data;
    }
}

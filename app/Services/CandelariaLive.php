<?php

namespace App\Services;

use App\Models\CandelariaPerformance;
use App\Models\CandelariaProgramItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Calcula el estado en vivo de una jornada de la Candelaria:
 * conjunto que está pasando, próximos conjuntos y hora estimada de cada uno.
 */
class CandelariaLive
{
    /** Minutos por conjunto cuando aún no hay datos de la jornada */
    public const DEFAULT_MINUTES = 8;

    /** Jornada en vivo: la que tiene un conjunto pasando o la que ocurre ahora según su horario */
    public function currentItem(): ?CandelariaProgramItem
    {
        $withPerforming = CandelariaProgramItem::active()
            ->whereHas('performances', fn ($q) => $q->where('status', 'performing'))
            ->orderByDesc('starts_at')
            ->first();

        return $withPerforming ?? CandelariaProgramItem::active()->happeningNow()->orderBy('starts_at')->first();
    }

    public function state(?CandelariaProgramItem $item = null, int $upcomingLimit = 10): array
    {
        $item ??= $this->currentItem();

        if (! $item) {
            return [
                'live' => false,
                'item' => null,
                'next_item' => CandelariaProgramItem::active()
                    ->where('starts_at', '>', now())
                    ->orderBy('starts_at')
                    ->first(),
            ];
        }

        $performances = $item->performances()->with('danza:id,name,type,image_url')->get();
        $current = $performances->firstWhere('status', 'performing');
        $finished = $performances->whereIn('status', ['done', 'skipped']);
        $pending = $performances->where('status', 'pending')->values();

        $minutes = $this->averageMinutes($finished);
        $base = $current?->started_at
            ? $current->started_at->copy()->addMinutes($minutes)
            : Carbon::now()->max($item->starts_at);
        // Si el conjunto actual ya superó el promedio, los siguientes se calculan desde ahora
        if ($base->isPast()) {
            $base = Carbon::now();
        }

        $upcoming = $pending->take($upcomingLimit)->map(function (CandelariaPerformance $p, int $i) use ($base, $minutes) {
            return array_merge($p->toArray(), [
                'position' => $i + 1,
                'eta' => $base->copy()->addMinutes($i * $minutes)->toIso8601String(),
            ]);
        });

        $onSchedule = $item->starts_at->isPast() && $item->ends_at->isFuture();

        return [
            'live' => $current !== null || $onSchedule,
            'item' => $item,
            'current' => $current,
            'upcoming' => $upcoming,
            'done_count' => $finished->count(),
            'total' => $performances->count(),
            'minutes_per_group' => $minutes,
            'updated_at' => now()->toIso8601String(),
        ];
    }

    /** Promedio de los últimos 10 conjuntos con hora de inicio y fin registradas */
    private function averageMinutes(Collection $finished): int
    {
        $durations = $finished
            ->filter(fn ($p) => $p->started_at && $p->finished_at)
            ->sortByDesc('finished_at')
            ->take(10)
            ->map(fn ($p) => $p->started_at->diffInMinutes($p->finished_at));

        $avg = $durations->avg();

        return $avg ? max(1, (int) round($avg)) : self::DEFAULT_MINUTES;
    }
}

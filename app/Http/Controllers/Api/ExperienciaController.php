<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\ExperienciaRequest;
use App\Models\Experiencia;
use App\Services\BookingService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ExperienciaController extends ContentController
{
    protected function model(): string
    {
        return Experiencia::class;
    }

    protected function resource(): string
    {
        return 'experiencias';
    }

    protected function messages(): array
    {
        return ['store' => 'Experiencia publicada', 'update' => 'Experiencia actualizada', 'destroy' => 'Experiencia eliminada'];
    }

    protected function listRelations(): array
    {
        return [
            'user:id,name,avatar',
            'locale',
            'anfitrion:id,name,community,role_title,photo_url,audio_url,greeting,greeting_translation,languages',
        ];
    }

    protected function detailRelations(): array
    {
        return [...parent::detailRelations(), 'anfitrion'];
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('difficulty')) {
            $query->byDifficulty($request->difficulty);
        }

        if ($request->filled('max_price')) {
            $query->withinBudget($request->max_price);
        }

        if ($request->filled('tag')) {
            $query->byTag($request->tag);
        }
    }

    public function store(ExperienciaRequest $request)
    {
        return $this->storeFrom($request);
    }

    public function update(ExperienciaRequest $request, $id)
    {
        return $this->updateFrom($request, $id);
    }

    /**
     * Cupos libres por fecha para los próximos días (salidas de la experiencia).
     * Cuenta las reservas pendientes y confirmadas; sin cupo máximo, todas las fechas están libres.
     */
    public function availability(Request $request, $id)
    {
        $experiencia = Experiencia::active()->findOrFail($id);
        $days = min(max($request->integer('days', 14), 1), 60);
        $from = now(BookingService::TIMEZONE)->addDay()->startOfDay();
        $to = $from->copy()->addDays($days - 1);

        $booked = $experiencia->bookings()
            ->whereIn('status', BookingService::ACTIVE)
            ->whereBetween('booking_date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('booking_date, SUM(participants) as total')
            ->groupBy('booking_date')
            ->pluck('total', 'booking_date')
            ->mapWithKeys(fn ($total, $date) => [substr((string) $date, 0, 10) => (int) $total]);

        $capacity = $experiencia->max_participants;
        $departure = $experiencia->meeting_time ? substr($experiencia->meeting_time, 0, 5) : null;
        $dates = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();
            $dates[] = [
                'date' => $key,
                'departure_time' => $departure,
                'capacity' => $capacity,
                'available' => $capacity ? max(0, $capacity - ($booked[$key] ?? 0)) : null,
            ];
        }

        return response()->json(['success' => true, 'data' => $dates]);
    }
}

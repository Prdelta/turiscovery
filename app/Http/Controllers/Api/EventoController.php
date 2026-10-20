<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\EventoRequest;
use App\Models\Evento;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/** Eventos vigentes: el listado oculta los que ya terminaron */
class EventoController extends ContentController
{
    protected function model(): string
    {
        return Evento::class;
    }

    protected function resource(): string
    {
        return 'eventos';
    }

    protected function messages(): array
    {
        return ['store' => 'Evento publicado', 'update' => 'Evento actualizado', 'destroy' => 'Evento eliminado'];
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        match ($request->status) {
            'upcoming' => $query->upcoming(),
            'ongoing' => $query->ongoing(),
            default => null,
        };
    }

    protected function orderList(Builder $query): Builder
    {
        return $query->latest('start_time');
    }

    public function store(EventoRequest $request)
    {
        return $this->storeFrom($request);
    }

    public function update(EventoRequest $request, $id)
    {
        return $this->updateFrom($request, $id);
    }
}

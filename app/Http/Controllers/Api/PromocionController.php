<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\PromocionRequest;
use App\Models\Promocion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/** Promociones vigentes: el listado oculta las vencidas y las que aún no empiezan */
class PromocionController extends ContentController
{
    protected function model(): string
    {
        return Promocion::class;
    }

    protected function resource(): string
    {
        return 'promociones';
    }

    protected function messages(): array
    {
        return ['store' => 'Promoción publicada', 'update' => 'Promoción actualizada', 'destroy' => 'Promoción eliminada'];
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('locale_id')) {
            $query->byLocale($request->locale_id);
        }
    }

    public function store(PromocionRequest $request)
    {
        return $this->storeFrom($request);
    }

    public function update(PromocionRequest $request, $id)
    {
        return $this->updateFrom($request, $id);
    }
}

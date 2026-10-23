<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\LocaleRequest;
use App\Models\Locale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LocaleController extends ContentController
{
    protected function model(): string
    {
        return Locale::class;
    }

    protected function resource(): string
    {
        return 'locales';
    }

    protected function messages(): array
    {
        return ['store' => 'Local publicado', 'update' => 'Local actualizado', 'destroy' => 'Local eliminado'];
    }

    protected function listRelations(): array
    {
        return ['user:id,name,avatar'];
    }

    /** El detalle de un local muestra lo que tiene vigente */
    protected function detailRelations(): array
    {
        return [
            'user:id,name,avatar',
            'reviews.user:id,name,avatar',
            'promociones' => fn ($q) => $q->active(),
            'experiencias' => fn ($q) => $q->active(),
            'eventos' => fn ($q) => $q->active(),
        ];
    }

    protected function searchColumns(): array
    {
        return ['name', 'description', 'address'];
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }
    }

    public function store(LocaleRequest $request)
    {
        return $this->storeFrom($request);
    }

    public function update(LocaleRequest $request, $id)
    {
        return $this->updateFrom($request, $id);
    }
}

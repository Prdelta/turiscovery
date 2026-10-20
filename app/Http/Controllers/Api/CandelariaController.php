<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\CandelariaRequest;
use App\Models\Candelaria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CandelariaController extends ContentController
{
    protected function model(): string
    {
        return Candelaria::class;
    }

    protected function resource(): string
    {
        return 'candelaria';
    }

    protected function messages(): array
    {
        return ['store' => 'Publicación creada', 'update' => 'Publicación actualizada', 'destroy' => 'Publicación eliminada'];
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }
    }

    public function store(CandelariaRequest $request)
    {
        return $this->storeFrom($request);
    }

    public function update(CandelariaRequest $request, $id)
    {
        return $this->updateFrom($request, $id);
    }
}

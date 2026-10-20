<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Content\ContentRequest;
use App\Http\Traits\ValidatesPaginationTrait;
use App\Models\Concerns\HasLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * CRUD común del contenido publicado por socios (locales, eventos, experiencias,
 * promociones y Candelaria). Cada controlador indica su modelo, sus relaciones,
 * sus filtros y sus mensajes; store/update los declara cada uno para recibir su FormRequest.
 */
abstract class ContentController extends Controller
{
    use ValidatesPaginationTrait;

    /** @return class-string<Model> */
    abstract protected function model(): string;

    /** Clave en config/pagination.php */
    abstract protected function resource(): string;

    /** @return array{store: string, update: string, destroy: string} */
    abstract protected function messages(): array;

    /** Relaciones del listado */
    protected function listRelations(): array
    {
        return ['user:id,name,avatar', 'locale'];
    }

    /** Relaciones del detalle */
    protected function detailRelations(): array
    {
        return ['user:id,name,avatar', 'locale', 'reviews.user:id,name,avatar'];
    }

    /** Columnas donde busca el parámetro ?search */
    protected function searchColumns(): array
    {
        return ['title', 'description'];
    }

    /** Filtros propios del recurso */
    protected function applyFilters(Builder $query, Request $request): void {}

    protected function orderList(Builder $query): Builder
    {
        return $query->latest();
    }

    public function index(Request $request): JsonResponse
    {
        $query = $this->model()::query()->with($this->listRelations())->active();

        $this->applyFilters($query, $request);

        if ($this->hasLocation() && $request->filled(['lat', 'lng'])) {
            $query->nearby($request->float('lat'), $request->float('lng'), $request->integer('radius', 5000));
        }

        if ($request->filled('search')) {
            $search = "%{$request->search}%";
            $query->where(function (Builder $q) use ($search) {
                foreach ($this->searchColumns() as $column) {
                    $q->orWhere($column, 'LIKE', $search);
                }
            });
        }

        $items = $this->orderList($query)->paginate($this->getValidatedPerPage($request, $this->resource()));

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function show($id): JsonResponse
    {
        $item = $this->model()::with($this->detailRelations())->findOrFail($id);

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function destroy($id): JsonResponse
    {
        $item = $this->model()::findOrFail($id);

        Gate::authorize('delete', $item);

        $item->delete();

        return response()->json(['success' => true, 'message' => $this->messages()['destroy']]);
    }

    protected function storeFrom(ContentRequest $request): JsonResponse
    {
        $item = $this->model()::make();
        $item->user_id = $request->user()->id;
        $this->save($item, $request);

        return response()->json([
            'success' => true,
            'message' => $this->messages()['store'],
            'data' => $item,
        ], 201);
    }

    protected function updateFrom(ContentRequest $request, $id): JsonResponse
    {
        $item = $this->model()::findOrFail($id);

        Gate::authorize('update', $item);

        $this->save($item, $request);

        return response()->json([
            'success' => true,
            'message' => $this->messages()['update'],
            'data' => $item,
        ]);
    }

    private function save(Model $item, ContentRequest $request): void
    {
        $item->fill($request->validated());

        if ($this->hasLocation() && $request->hasCoordinates()) {
            $item->setLocationFromCoordinates($request->float('latitude'), $request->float('longitude'));
        }

        $item->save();
    }

    private function hasLocation(): bool
    {
        return in_array(HasLocation::class, class_uses_recursive($this->model()), true);
    }
}

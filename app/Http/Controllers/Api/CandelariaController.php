<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ValidatesPaginationTrait;
use App\Models\Candelaria;
use App\Http\Requests\Content\CandelariaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CandelariaController extends Controller
{
    use ValidatesPaginationTrait;
    /**
     * Display a listing of active Candelaria content
     */
    public function index(Request $request)
    {
        $query = Candelaria::with(['user:id,name,avatar', 'locale'])
            ->active();

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by featured
        if ($request->has('featured') && $request->featured == 'true') {
            $query->featured();
        }

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Validar y limitar per_page para prevenir ataques DOS
        $perPage = $this->getValidatedPerPage($request, 'candelaria');
        $candelaria = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $candelaria,
        ], 200);
    }

    /**
     * Display the specified Candelaria item
     */
    public function show($id)
    {
        $candelaria = Candelaria::with(['user:id,name,avatar', 'locale', 'reviews.user:id,name,avatar'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $candelaria,
        ], 200);
    }

    /**
     * Store a newly created Candelaria item
     */
    public function store(CandelariaRequest $request)
    {
        $candelaria = Candelaria::create(array_merge(
            $request->validated(),
            ['user_id' => $request->user()->id]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Publicación creada',
            'data' => $candelaria,
        ], 201);
    }

    /**
     * Update the specified Candelaria item
     */
    public function update(CandelariaRequest $request, $id)
    {
        $candelaria = Candelaria::findOrFail($id);

        // Authorization: Only owner or admin can update
        Gate::authorize('update', $candelaria);

        $candelaria->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Publicación actualizada',
            'data' => $candelaria,
        ], 200);
    }

    /**
     * Remove the specified Candelaria item (soft delete)
     */
    public function destroy(Request $request, $id)
    {
        $candelaria = Candelaria::findOrFail($id);

        // Authorization: Only owner or admin can delete
        Gate::authorize('delete', $candelaria);

        $candelaria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Publicación eliminada',
        ], 200);
    }
}

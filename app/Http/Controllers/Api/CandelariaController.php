<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ValidatesPaginationTrait;
use App\Models\Candelaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CandelariaController extends Controller
{
    use ValidatesPaginationTrait;
    /**
     * Display a listing of active Candelaria content
     */
    public function index(Request $request)
    {
        $query = Candelaria::with(['user', 'locale'])
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
        $candelaria = Candelaria::with(['user', 'locale', 'reviews.user'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $candelaria,
        ], 200);
    }

    /**
     * Store a newly created Candelaria item
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'locale_id' => 'nullable|exists:locales,id',
            'event_date' => 'nullable|date',
            'category' => 'required|in:dance,history,costume,music,tradition,procession,other',
            'images' => 'nullable|array',
            'images.*' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $candelaria = Candelaria::create(array_merge(
            $validator->validated(),
            ['user_id' => $request->user()->id]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Candelaria content created successfully',
            'data' => $candelaria,
        ], 201);
    }

    /**
     * Update the specified Candelaria item
     */
    public function update(Request $request, $id)
    {
        $candelaria = Candelaria::findOrFail($id);

        // Authorization: Only owner or admin can update
        if ($candelaria->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this content.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'content' => 'nullable|string',
            'locale_id' => 'nullable|exists:locales,id',
            'event_date' => 'nullable|date',
            'category' => 'sometimes|required|in:dance,history,costume,music,tradition,procession,other',
            'images' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $candelaria->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Candelaria content updated successfully',
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
        if ($candelaria->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this content.',
            ], 403);
        }

        $candelaria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Candelaria content deleted successfully',
        ], 200);
    }
}

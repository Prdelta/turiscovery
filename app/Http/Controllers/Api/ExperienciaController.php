<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experiencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExperienciaController extends Controller
{
    /**
     * Display a listing of active Experiencias
     */
    public function index(Request $request)
    {
        $query = Experiencia::with(['user', 'locale'])
            ->active();

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->byDifficulty($request->difficulty);
        }

        // Filter by budget
        if ($request->has('max_price')) {
            $query->withinBudget($request->max_price);
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->byTag($request->tag);
        }

        // Geolocation filter
        if ($request->has('lat') && $request->has('lng')) {
            $radius = $request->get('radius', 5000);
            $query = Experiencia::nearby($request->lat, $request->lng, $radius)
                ->with(['user', 'locale']);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $experiencias = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $experiencias,
        ], 200);
    }

    /**
     * Display the specified Experiencia
     */
    public function show($id)
    {
        $experiencia = Experiencia::with(['user', 'locale', 'reviews.user'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $experiencia,
        ], 200);
    }

    /**
     * Store a newly created Experiencia
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'locale_id' => 'nullable|exists:locales,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'duration_hours' => 'nullable|integer|min:1',
            'price_pen' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'images' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $request->user()->id;

        $experiencia = new Experiencia($data);

        if ($request->has('latitude') && $request->has('longitude')) {
            $experiencia->setLocationFromCoordinates($request->latitude, $request->longitude);
        }

        $experiencia->save();

        return response()->json([
            'success' => true,
            'message' => 'Experiencia created successfully',
            'data' => $experiencia,
        ], 201);
    }

    /**
     * Update the specified Experiencia
     */
    public function update(Request $request, $id)
    {
        $experiencia = Experiencia::findOrFail($id);

        if ($experiencia->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this experiencia.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'content' => 'nullable|string',
            'locale_id' => 'nullable|exists:locales,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string',
            'difficulty' => 'sometimes|required|in:easy,medium,hard',
            'duration_hours' => 'nullable|integer|min:1',
            'price_pen' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'images' => 'nullable|array',
            'tags' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $experiencia->fill($validator->validated());

        if ($request->has('latitude') && $request->has('longitude')) {
            $experiencia->setLocationFromCoordinates($request->latitude, $request->longitude);
        }

        $experiencia->save();

        return response()->json([
            'success' => true,
            'message' => 'Experiencia updated successfully',
            'data' => $experiencia,
        ], 200);
    }

    /**
     * Remove the specified Experiencia
     */
    public function destroy(Request $request, $id)
    {
        $experiencia = Experiencia::findOrFail($id);

        if ($experiencia->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this experiencia.',
            ], 403);
        }

        $experiencia->delete();

        return response()->json([
            'success' => true,
            'message' => 'Experiencia deleted successfully',
        ], 200);
    }
}

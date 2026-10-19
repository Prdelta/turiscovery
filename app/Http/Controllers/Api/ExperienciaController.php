<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ValidatesPaginationTrait;
use App\Models\Experiencia;
use App\Http\Requests\Content\ExperienciaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExperienciaController extends Controller
{
    use ValidatesPaginationTrait;
    /**
     * Display a listing of active Experiencias
     */
    public function index(Request $request)
    {
        $query = Experiencia::with(['user:id,name,avatar', 'locale', 'anfitrion:id,name,community,role_title,photo_url,audio_url,greeting,greeting_translation,languages'])
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
                ->with(['user:id,name,avatar', 'locale']);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Validar y limitar per_page para prevenir ataques DOS
        $perPage = $this->getValidatedPerPage($request, 'experiencias');
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
        $experiencia = Experiencia::with(['user:id,name,avatar', 'locale', 'reviews.user:id,name,avatar', 'anfitrion'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $experiencia,
        ], 200);
    }

    /**
     * Store a newly created Experiencia
     */
    public function store(ExperienciaRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $experiencia = new Experiencia($data);

        if ($request->hasCoordinates()) {
            $experiencia->setLocationFromCoordinates($request->latitude, $request->longitude);
        }

        $experiencia->save();

        return response()->json([
            'success' => true,
            'message' => 'Experiencia publicada',
            'data' => $experiencia,
        ], 201);
    }

    /**
     * Update the specified Experiencia
     */
    public function update(ExperienciaRequest $request, $id)
    {
        $experiencia = Experiencia::findOrFail($id);

        Gate::authorize('update', $experiencia);

        $experiencia->fill($request->validated());

        if ($request->hasCoordinates()) {
            $experiencia->setLocationFromCoordinates($request->latitude, $request->longitude);
        }

        $experiencia->save();

        return response()->json([
            'success' => true,
            'message' => 'Experiencia actualizada',
            'data' => $experiencia,
        ], 200);
    }

    /**
     * Remove the specified Experiencia
     */
    public function destroy(Request $request, $id)
    {
        $experiencia = Experiencia::findOrFail($id);

        Gate::authorize('delete', $experiencia);

        $experiencia->delete();

        return response()->json([
            'success' => true,
            'message' => 'Experiencia eliminada',
        ], 200);
    }
}

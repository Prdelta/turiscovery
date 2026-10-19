<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ValidatesPaginationTrait;
use App\Models\Evento;
use App\Http\Requests\Content\EventoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventoController extends Controller
{
    use ValidatesPaginationTrait;
    /**
     * Display a listing of active Eventos (automatically filters expired)
     */
    public function index(Request $request)
    {
        $query = Evento::with(['user:id,name,avatar', 'locale'])
            ->active(); // This scope filters end_time > now()

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by upcoming/ongoing
        if ($request->has('status')) {
            if ($request->status === 'upcoming') {
                $query->upcoming();
            } elseif ($request->status === 'ongoing') {
                $query->ongoing();
            }
        }

        // Filter by location (nearby)
        if ($request->has('lat') && $request->has('lng')) {
            $radius = $request->get('radius', 5000); // Default 5km
            $query = Evento::nearby($request->lat, $request->lng, $radius)
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
        $perPage = $this->getValidatedPerPage($request, 'eventos');
        $eventos = $query->latest('start_time')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $eventos,
        ], 200);
    }

    /**
     * Display the specified Evento
     */
    public function show($id)
    {
        $evento = Evento::with(['user:id,name,avatar', 'locale', 'reviews.user:id,name,avatar'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $evento,
        ], 200);
    }

    /**
     * Store a newly created Evento
     */
    public function store(EventoRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $evento = new Evento($data);

        // Set geolocation if provided
        if ($request->hasCoordinates()) {
            $evento->setLocationFromCoordinates($request->latitude, $request->longitude);
        }

        $evento->save();

        return response()->json([
            'success' => true,
            'message' => 'Evento publicado',
            'data' => $evento,
        ], 201);
    }

    /**
     * Update the specified Evento
     */
    public function update(EventoRequest $request, $id)
    {
        $evento = Evento::findOrFail($id);

        Gate::authorize('update', $evento);

        $evento->fill($request->validated());

        if ($request->hasCoordinates()) {
            $evento->setLocationFromCoordinates($request->latitude, $request->longitude);
        }

        $evento->save();

        return response()->json([
            'success' => true,
            'message' => 'Evento actualizado',
            'data' => $evento,
        ], 200);
    }

    /**
     * Remove the specified Evento
     */
    public function destroy(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);

        Gate::authorize('delete', $evento);

        $evento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evento eliminado',
        ], 200);
    }
}

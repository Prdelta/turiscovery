<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ValidatesPaginationTrait;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventoController extends Controller
{
    use ValidatesPaginationTrait;
    /**
     * Display a listing of active Eventos (automatically filters expired)
     */
    public function index(Request $request)
    {
        $query = Evento::with(['user', 'locale'])
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
        $evento = Evento::with(['user', 'locale', 'reviews.user'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $evento,
        ], 200);
    }

    /**
     * Store a newly created Evento
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'locale_id' => 'nullable|exists:locales,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string',
            'ticket_price' => 'nullable|numeric|min:0',
            'category' => 'required|in:concert,festival,nightlife,cultural,sports,exhibition,other',
            'images' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $request->user()->id;

        $evento = new Evento($data);

        // Set geolocation if provided
        if ($request->has('latitude') && $request->has('longitude')) {
            $evento->setLocationFromCoordinates($request->latitude, $request->longitude);
        }

        $evento->save();

        return response()->json([
            'success' => true,
            'message' => 'Evento created successfully',
            'data' => $evento,
        ], 201);
    }

    /**
     * Update the specified Evento
     */
    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);

        if ($evento->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this evento.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'content' => 'nullable|string',
            'locale_id' => 'nullable|exists:locales,id',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string',
            'ticket_price' => 'nullable|numeric|min:0',
            'category' => 'sometimes|required|in:concert,festival,nightlife,cultural,sports,exhibition,other',
            'images' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $evento->fill($validator->validated());

        if ($request->has('latitude') && $request->has('longitude')) {
            $evento->setLocationFromCoordinates($request->latitude, $request->longitude);
        }

        $evento->save();

        return response()->json([
            'success' => true,
            'message' => 'Evento updated successfully',
            'data' => $evento,
        ], 200);
    }

    /**
     * Remove the specified Evento
     */
    public function destroy(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);

        if ($evento->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this evento.',
            ], 403);
        }

        $evento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evento deleted successfully',
        ], 200);
    }
}

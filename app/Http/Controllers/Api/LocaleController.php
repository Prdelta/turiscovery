<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ValidatesPaginationTrait;
use App\Models\Locale;
use App\Http\Requests\Content\LocaleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LocaleController extends Controller
{
    use ValidatesPaginationTrait;
    /**
     * Display a listing of active Locales
     */
    public function index(Request $request)
    {
        $query = Locale::with('user:id,name,avatar')->active();

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Geolocation filter
        if ($request->has('lat') && $request->has('lng')) {
            $radius = $request->get('radius', 5000);
            $query = Locale::nearby($request->lat, $request->lng, $radius)->with('user:id,name,avatar');
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        // Validar y limitar per_page para prevenir ataques DOS
        $perPage = $this->getValidatedPerPage($request, 'locales');
        $locales = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $locales,
        ], 200);
    }

    /**
     * Display the specified Locale
     */
    public function show($id)
    {
        $locale = Locale::with([
            'user:id,name,avatar',
            'reviews.user:id,name,avatar',
            'promociones' => fn ($q) => $q->active(),
            'experiencias' => fn ($q) => $q->active(),
            'eventos' => fn ($q) => $q->active(),
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $locale,
        ], 200);
    }

    /**
     * Store a newly created Locale (Partner Dashboard)
     */
    public function store(LocaleRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $locale = new Locale($data);
        $locale->setLocationFromCoordinates($request->latitude, $request->longitude);
        $locale->save();

        return response()->json([
            'success' => true,
            'message' => 'Local publicado',
            'data' => $locale,
        ], 201);
    }

    /**
     * Update the specified Locale
     */
    public function update(LocaleRequest $request, $id)
    {
        $locale = Locale::findOrFail($id);

        Gate::authorize('update', $locale);

        $locale->fill($request->validated());

        if ($request->hasCoordinates()) {
            $locale->setLocationFromCoordinates($request->latitude, $request->longitude);
        }

        $locale->save();

        return response()->json([
            'success' => true,
            'message' => 'Local actualizado',
            'data' => $locale,
        ], 200);
    }

    /**
     * Remove the specified Locale
     */
    public function destroy(Request $request, $id)
    {
        $locale = Locale::findOrFail($id);

        Gate::authorize('delete', $locale);

        $locale->delete();

        return response()->json([
            'success' => true,
            'message' => 'Local eliminado',
        ], 200);
    }
}

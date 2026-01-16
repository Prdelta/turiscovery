<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocaleController extends Controller
{
    /**
     * Display a listing of active Locales
     */
    public function index(Request $request)
    {
        $query = Locale::with('user')->active();

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Geolocation filter
        if ($request->has('lat') && $request->has('lng')) {
            $radius = $request->get('radius', 5000);
            $query = Locale::nearby($request->lat, $request->lng, $radius)->with('user');
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

        $perPage = $request->get('per_page', 15);
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
        $locale = Locale::with(['user', 'reviews.user', 'promociones'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $locale,
        ], 200);
    }

    /**
     * Store a newly created Locale (Partner Dashboard)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'latitude' => 'required|numeric|between:-16.5,-14.5', // Puno region roughly
            'longitude' => 'required|numeric|between:-71,-69',    // Puno region roughly
            'category' => 'required|in:restaurant,hotel,tour_agency,craft_shop,museum,cultural_center,other',
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

        $locale = new Locale($data);
        $locale->setLocationFromCoordinates($request->latitude, $request->longitude);
        $locale->save();

        return response()->json([
            'success' => true,
            'message' => 'Locale created successfully',
            'data' => $locale,
        ], 201);
    }

    /**
     * Update the specified Locale
     */
    public function update(Request $request, $id)
    {
        $locale = Locale::findOrFail($id);

        if ($locale->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this locale.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'latitude' => 'nullable|numeric|between:-16.5,-14.5',
            'longitude' => 'nullable|numeric|between:-71,-69',
            'category' => 'sometimes|required|in:restaurant,hotel,tour_agency,craft_shop,museum,cultural_center,other',
            'images' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $locale->fill($validator->validated());

        if ($request->has('latitude') && $request->has('longitude')) {
            $locale->setLocationFromCoordinates($request->latitude, $request->longitude);
        }

        $locale->save();

        return response()->json([
            'success' => true,
            'message' => 'Locale updated successfully',
            'data' => $locale,
        ], 200);
    }

    /**
     * Remove the specified Locale
     */
    public function destroy(Request $request, $id)
    {
        $locale = Locale::findOrFail($id);

        if ($locale->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this locale.',
            ], 403);
        }

        $locale->delete();

        return response()->json([
            'success' => true,
            'message' => 'Locale deleted successfully',
        ], 200);
    }
}

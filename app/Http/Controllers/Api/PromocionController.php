<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ValidatesPaginationTrait;
use App\Models\Promocion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromocionController extends Controller
{
    use ValidatesPaginationTrait;
    /**
     * Display a listing of active Promociones (automatically filters expired)
     */
    public function index(Request $request)
    {
        $query = Promocion::with(['user', 'locale'])
            ->active(); // Filters: is_active AND end_date > now AND start_date <= now

        // Filter by discount type
        if ($request->has('type')) {
            $query->byType($request->type);
        }

        // Filter by locale
        if ($request->has('locale_id')) {
            $query->byLocale($request->locale_id);
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
        $perPage = $this->getValidatedPerPage($request, 'promociones');
        $promociones = $query->latest('created_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $promociones,
        ], 200);
    }

    /**
     * Display the specified Promocion
     */
    public function show($id)
    {
        $promocion = Promocion::with(['user', 'locale', 'reviews.user'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $promocion,
        ], 200);
    }

    /**
     * Store a newly created Promocion
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'locale_id' => 'required|exists:locales,id',
            'discount_type' => 'required|in:2x1,percentage,fixed',
            'discount_percentage' => 'nullable|integer|min:1|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'final_price' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'terms_conditions' => 'nullable|string',
            'redemption_code' => 'nullable|string|max:50',
            'images' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate discount logic
        $data = $validator->validated();

        if ($data['discount_type'] === 'percentage' && empty($data['discount_percentage'])) {
            return response()->json([
                'success' => false,
                'message' => 'Discount percentage is required for percentage type.',
            ], 422);
        }

        if ($data['discount_type'] === 'fixed' && empty($data['discount_amount'])) {
            return response()->json([
                'success' => false,
                'message' => 'Discount amount is required for fixed type.',
            ], 422);
        }

        $promocion = Promocion::create(array_merge(
            $data,
            ['user_id' => $request->user()->id]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Promocion created successfully',
            'data' => $promocion,
        ], 201);
    }

    /**
     * Update the specified Promocion
     */
    public function update(Request $request, $id)
    {
        $promocion = Promocion::findOrFail($id);

        if ($promocion->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this promocion.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'locale_id' => 'sometimes|required|exists:locales,id',
            'discount_type' => 'sometimes|required|in:2x1,percentage,fixed',
            'discount_percentage' => 'nullable|integer|min:1|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'final_price' => 'nullable|numeric|min:0',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'terms_conditions' => 'nullable|string',
            'redemption_code' => 'nullable|string|max:50',
            'images' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $promocion->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Promocion updated successfully',
            'data' => $promocion,
        ], 200);
    }

    /**
     * Remove the specified Promocion
     */
    public function destroy(Request $request, $id)
    {
        $promocion = Promocion::findOrFail($id);

        if ($promocion->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this promocion.',
            ], 403);
        }

        $promocion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Promocion deleted successfully',
        ], 200);
    }
}

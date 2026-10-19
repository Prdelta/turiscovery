<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ValidatesPaginationTrait;
use App\Models\Promocion;
use App\Http\Requests\Content\PromocionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PromocionController extends Controller
{
    use ValidatesPaginationTrait;
    /**
     * Display a listing of active Promociones (automatically filters expired)
     */
    public function index(Request $request)
    {
        $query = Promocion::with(['user:id,name,avatar', 'locale'])
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
        $promocion = Promocion::with(['user:id,name,avatar', 'locale', 'reviews.user:id,name,avatar'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $promocion,
        ], 200);
    }

    /**
     * Store a newly created Promocion
     */
    public function store(PromocionRequest $request)
    {
        // Validate discount logic
        $data = $request->validated();

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
            'message' => 'Promoción publicada',
            'data' => $promocion,
        ], 201);
    }

    /**
     * Update the specified Promocion
     */
    public function update(PromocionRequest $request, $id)
    {
        $promocion = Promocion::findOrFail($id);

        Gate::authorize('update', $promocion);

        $promocion->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Promoción actualizada',
            'data' => $promocion,
        ], 200);
    }

    /**
     * Remove the specified Promocion
     */
    public function destroy(Request $request, $id)
    {
        $promocion = Promocion::findOrFail($id);

        Gate::authorize('delete', $promocion);

        $promocion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Promoción eliminada',
        ], 200);
    }
}

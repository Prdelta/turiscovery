<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    /**
     * Get user's favorites
     */
    public function index(Request $request)
    {
        $query = Favorite::with('favoritable')
            ->where('user_id', $request->user()->id);

        // Filter by type
        if ($request->has('type')) {
            $query->where('favoritable_type', $request->type);
        }

        $favorites = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $favorites,
        ], 200);
    }

    /**
     * Toggle favorite (add or remove)
     */
    public function toggle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'favoritable_type' => 'required|string|in:App\Models\Locale,App\Models\Candelaria,App\Models\Experiencia,App\Models\Evento,App\Models\Promocion',
            'favoritable_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $isFavorited = Favorite::toggle(
            $request->user()->id,
            $request->favoritable_type,
            $request->favoritable_id
        );

        return response()->json([
            'success' => true,
            'message' => $isFavorited ? 'Added to favorites' : 'Removed from favorites',
            'is_favorited' => $isFavorited,
        ], 200);
    }

    /**
     * Check if item is favorited
     */
    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'favoritable_type' => 'required|string',
            'favoritable_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $isFavorited = Favorite::isFavorited(
            $request->user()->id,
            $request->favoritable_type,
            $request->favoritable_id
        );

        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited,
        ], 200);
    }

    /**
     * Remove favorite
     */
    public function destroy(Request $request, $id)
    {
        $favorite = Favorite::findOrFail($id);

        if ($favorite->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to remove this favorite.',
            ], 403);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from favorites',
        ], 200);
    }
}

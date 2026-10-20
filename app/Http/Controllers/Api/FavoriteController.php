<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Support\MorphTypes;
use Illuminate\Http\Request;

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
        if ($type = MorphTypes::normalize($request->input('type'))) {
            $query->where('favoritable_type', $type);
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
        $request->merge(['favoritable_type' => MorphTypes::normalize($request->input('favoritable_type'))]);
        $request->validate([
            'favoritable_type' => 'required|string|in:'.implode(',', MorphTypes::FAVORITABLE),
            'favoritable_id' => 'required|integer|min:1',
        ], ['favoritable_type.*' => 'Ese tipo de contenido no se puede guardar en favoritos.']);

        abort_unless(
            MorphTypes::exists($request->favoritable_type, (int) $request->favoritable_id),
            404,
            'Ese contenido ya no existe.',
        );

        $isFavorited = Favorite::toggle(
            $request->user()->id,
            $request->favoritable_type,
            $request->favoritable_id
        );

        return response()->json([
            'success' => true,
            'message' => $isFavorited ? 'Guardado en favoritos' : 'Quitado de favoritos',
            'is_favorited' => $isFavorited,
        ], 200);
    }

    /**
     * Check if item is favorited
     */
    public function check(Request $request)
    {
        $request->merge(['favoritable_type' => MorphTypes::normalize($request->input('favoritable_type'))]);
        $request->validate([
            'favoritable_type' => 'required|string|in:'.implode(',', MorphTypes::FAVORITABLE),
            'favoritable_id' => 'required|integer|min:1',
        ]);

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

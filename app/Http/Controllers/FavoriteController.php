<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Toggle favorite status for an item
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'id' => 'required|integer',
        ]);

        $userId = Auth::id();
        $type = $request->type;
        $id = $request->id;

        // Map short type names to full model names
        $typeMap = [
            'Locale' => 'App\\Models\\Locale',
            'Evento' => 'App\\Models\\Evento',
            'Experiencia' => 'App\\Models\\Experiencia',
            'Candelaria' => 'App\\Models\\Candelaria',
            'Promocion' => 'App\\Models\\Promocion',
        ];

        $fullType = $typeMap[$type] ?? $type;

        // Check if already favorited
        $favorite = Favorite::where('user_id', $userId)
            ->where('favoritable_type', $fullType)
            ->where('favoritable_id', $id)
            ->first();

        if ($favorite) {
            // Remove from favorites
            $favorite->delete();
            return response()->json([
                'success' => true,
                'favorited' => false,
                'message' => 'Eliminado de favoritos'
            ]);
        } else {
            // Add to favorites
            Favorite::create([
                'user_id' => $userId,
                'favoritable_type' => $fullType,
                'favoritable_id' => $id,
            ]);

            return response()->json([
                'success' => true,
                'favorited' => true,
                'message' => 'Agregado a favoritos'
            ]);
        }
    }

    /**
     * Check if an item is favorited
     */
    public function check(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'id' => 'required|integer',
        ]);

        $userId = Auth::id();
        $type = $request->type;
        $id = $request->id;

        $typeMap = [
            'Locale' => 'App\\Models\\Locale',
            'Evento' => 'App\\Models\\Evento',
            'Experiencia' => 'App\\Models\\Experiencia',
            'Candelaria' => 'App\\Models\\Candelaria',
            'Promocion' => 'App\\Models\\Promocion',
        ];

        $fullType = $typeMap[$type] ?? $type;

        $isFavorited = Favorite::where('user_id', $userId)
            ->where('favoritable_type', $fullType)
            ->where('favoritable_id', $id)
            ->exists();

        return response()->json([
            'success' => true,
            'favorited' => $isFavorited
        ]);
    }
}

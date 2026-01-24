<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CandelariaGallery;
use Illuminate\Http\Request;

class CandelariaGalleryController extends Controller
{
    public function index()
    {
        $photos = CandelariaGallery::where('is_active', true)
            ->orderBy('year', 'desc')
            ->orderBy('order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $photos,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'required|url',
            'year' => 'required|integer|min:1583|max:' . (date('Y') + 1),
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $photo = CandelariaGallery::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Foto agregada a la galería exitosamente.',
            'data' => $photo,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $photo = CandelariaGallery::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'sometimes|url',
            'year' => 'sometimes|integer|min:1583|max:' . (date('Y') + 1),
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $photo->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Foto actualizada exitosamente.',
            'data' => $photo,
        ]);
    }

    public function destroy($id)
    {
        $photo = CandelariaGallery::findOrFail($id);
        $photo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto eliminada de la galería.',
        ]);
    }
}

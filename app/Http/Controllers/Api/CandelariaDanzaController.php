<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CandelariaDanza;
use Illuminate\Http\Request;

class CandelariaDanzaController extends Controller
{
    public function index()
    {
        $danzas = CandelariaDanza::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $danzas,
        ]);
    }

    public function show($id)
    {
        $danza = CandelariaDanza::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $danza,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:autoctona,mestiza',
            'description' => 'required|string',
            'history' => 'nullable|string',
            'image_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'region' => 'nullable|string|max:255',
            'characteristics' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $danza = CandelariaDanza::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Danza creada exitosamente.',
            'data' => $danza,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $danza = CandelariaDanza::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:autoctona,mestiza',
            'description' => 'sometimes|string',
            'history' => 'nullable|string',
            'image_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'region' => 'nullable|string|max:255',
            'characteristics' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $danza->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Danza actualizada exitosamente.',
            'data' => $danza,
        ]);
    }

    public function destroy($id)
    {
        $danza = CandelariaDanza::findOrFail($id);
        $danza->delete();

        return response()->json([
            'success' => true,
            'message' => 'Danza eliminada exitosamente.',
        ]);
    }

    public function featured()
    {
        $danzas = CandelariaDanza::where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $danzas,
        ]);
    }
}

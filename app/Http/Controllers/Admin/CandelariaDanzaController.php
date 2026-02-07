<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandelariaDanza;
use Illuminate\Http\Request;

class CandelariaDanzaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CandelariaDanza::with('user');

        // Filtro por tipo
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filtro por destacadas
        if ($request->filled('featured')) {
            $query->featured();
        }

        $danzas = $query->ordered()->paginate(15);

        // Contar por tipos
        $stats = [
            'total' => CandelariaDanza::count(),
            'mestizas' => CandelariaDanza::mestizas()->count(),
            'autoctonas' => CandelariaDanza::autoctonas()->count(),
            'destacadas' => CandelariaDanza::featured()->count(),
        ];

        return view('admin.candelaria.danzas.index', compact('danzas', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.candelaria.danzas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:mestiza,autoctona',
            'description' => 'required|string',
            'history' => 'nullable|string',
            'image_url' => 'nullable|url|max:500',
            'video_url' => 'nullable|url|max:500',
            'region' => 'nullable|string|max:255',
            'characteristics' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

        CandelariaDanza::create($validated);

        return redirect()
            ->route('admin.candelaria.danzas.index')
            ->with('success', 'Danza agregada exitosamente');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CandelariaDanza $danza)
    {
        return view('admin.candelaria.danzas.edit', compact('danza'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CandelariaDanza $danza)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:mestiza,autoctona',
            'description' => 'required|string',
            'history' => 'nullable|string',
            'image_url' => 'nullable|url|max:500',
            'video_url' => 'nullable|url|max:500',
            'region' => 'nullable|string|max:255',
            'characteristics' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

        $danza->update($validated);

        return redirect()
            ->route('admin.candelaria.danzas.index')
            ->with('success', 'Danza actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CandelariaDanza $danza)
    {
        $danza->delete();

        return redirect()
            ->route('admin.candelaria.danzas.index')
            ->with('success', 'Danza eliminada exitosamente');
    }
}

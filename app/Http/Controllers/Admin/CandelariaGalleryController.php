<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandelariaGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandelariaGalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $galleries = CandelariaGallery::with('user')
            ->byYearDesc()
            ->paginate(12);

        return view('admin.candelaria.gallery.index', compact('galleries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.candelaria.gallery.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'required|url|max:500',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        CandelariaGallery::create($validated);

        return redirect()
            ->route('admin.candelaria.gallery.index')
            ->with('success', 'Fotografía agregada exitosamente');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CandelariaGallery $gallery)
    {
        return view('admin.candelaria.gallery.edit', compact('gallery'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CandelariaGallery $gallery)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'required|url|max:500',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        $gallery->update($validated);

        return redirect()
            ->route('admin.candelaria.gallery.index')
            ->with('success', 'Fotografía actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CandelariaGallery $gallery)
    {
        $gallery->delete();

        return redirect()
            ->route('admin.candelaria.gallery.index')
            ->with('success', 'Fotografía eliminada exitosamente');
    }
}

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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB - REQUERIDO
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Procesar imagen subida (ahora siempre existe)
        $path = $request->file('image')->store('candelaria/gallery', 'public');
        $validated['image_url'] = Storage::url($path);

        $validated['user_id'] = auth()->id();
        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        // Remover el campo 'image' antes de guardar
        unset($validated['image']);

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB - Opcional en edición
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Procesar nueva imagen si se subió
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($gallery->image_url && strpos($gallery->image_url, '/storage/') !== false) {
                $oldPath = str_replace('/storage/', '', parse_url($gallery->image_url, PHP_URL_PATH));
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('candelaria/gallery', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        // Remover el campo 'image' antes de guardar
        unset($validated['image']);

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

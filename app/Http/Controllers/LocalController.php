<?php

namespace App\Http\Controllers;

use App\Models\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LocalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locales = Locale::where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('dashboard.locales.index', compact('locales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.locales.create-new');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'category' => 'required|in:restaurant,hotel,tour_agency,craft_shop,museum,cultural_center,other',

            // Campos de dirección
            'street' => 'required|string|max:255',
            'street_number' => 'nullable|string|max:50',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'address_notes' => 'nullable|string|max:500',

            // Coordenadas
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',

            // Contacto
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',

            // Imagen
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB

            'is_active' => 'boolean',
        ]);

        // Construir dirección completa para el campo legacy
        $addressParts = [];
        if ($validated['street']) $addressParts[] = $validated['street'];
        if ($validated['street_number']) $addressParts[count($addressParts)-1] .= ' N° ' . $validated['street_number'];
        if ($validated['district']) $addressParts[] = $validated['district'];
        if ($validated['city']) $addressParts[] = $validated['city'];
        if ($validated['region']) $addressParts[] = $validated['region'];
        if ($validated['country']) $addressParts[] = $validated['country'];

        $validated['address'] = implode(', ', $addressParts);

        // Procesar imagen si existe
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('locales', 'public');
            $validated['images'] = [Storage::url($path)];
        }

        // Asignar usuario actual
        $validated['user_id'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        // Crear el local
        $locale = new Locale($validated);

        // Guardar coordenadas en PostGIS
        $locale->setLocationFromCoordinates(
            $validated['latitude'],
            $validated['longitude']
        );

        $locale->save();

        return redirect()
            ->route('locales.index')
            ->with('success', '¡Local creado exitosamente! 🎉 Tu negocio ya está visible en Turiscovery.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $locale = Locale::findOrFail($id);

        // Verificar que el usuario es dueño o admin
        if ($locale->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'No tienes permiso para ver este local.');
        }

        return view('dashboard.locales.show', compact('locale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $locale = Locale::findOrFail($id);

        // Verificar que el usuario es dueño o admin
        if ($locale->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'No tienes permiso para editar este local.');
        }

        return view('dashboard.locales.edit', compact('locale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $locale = Locale::findOrFail($id);

        // Verificar que el usuario es dueño o admin
        if ($locale->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'No tienes permiso para actualizar este local.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'category' => 'required|in:restaurant,hotel,tour_agency,craft_shop,museum,cultural_center,other',

            // Campos de dirección
            'street' => 'required|string|max:255',
            'street_number' => 'nullable|string|max:50',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'address_notes' => 'nullable|string|max:500',

            // Coordenadas
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',

            // Contacto
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',

            // Imagen
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',

            'is_active' => 'boolean',
        ]);

        // Construir dirección completa
        $addressParts = [];
        if ($validated['street']) $addressParts[] = $validated['street'];
        if ($validated['street_number']) $addressParts[count($addressParts)-1] .= ' N° ' . $validated['street_number'];
        if ($validated['district']) $addressParts[] = $validated['district'];
        if ($validated['city']) $addressParts[] = $validated['city'];
        if ($validated['region']) $addressParts[] = $validated['region'];
        if ($validated['country']) $addressParts[] = $validated['country'];

        $validated['address'] = implode(', ', $addressParts);

        // Procesar nueva imagen si existe
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('locales', 'public');
            $validated['images'] = [Storage::url($path)];
        }

        $validated['is_active'] = $request->has('is_active');

        // Actualizar datos
        $locale->fill($validated);

        // Actualizar coordenadas en PostGIS
        $locale->setLocationFromCoordinates(
            $validated['latitude'],
            $validated['longitude']
        );

        $locale->save();

        return redirect()
            ->route('locales.index')
            ->with('success', '¡Local actualizado exitosamente! ✨');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $locale = Locale::findOrFail($id);

        // Verificar que el usuario es dueño o admin
        if ($locale->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'No tienes permiso para eliminar este local.');
        }

        $locale->delete();

        return redirect()
            ->route('locales.index')
            ->with('success', 'Local eliminado correctamente.');
    }
}

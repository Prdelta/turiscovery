<?php

namespace App\Http\Controllers;

use App\Models\Experiencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExperienciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Admin sees all, Partner sees only theirs
        if (auth()->user()->role === 'admin') {
            $experiencias = Experiencia::latest()->paginate(10);
        } else {
            $experiencias = auth()->user()->experiencias()->latest()->paginate(10);
        }
        return view('dashboard.experiencias.index', compact('experiencias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.experiencias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|string|max:100', // e.g., "3 horas", "Full Day"
            'location' => 'required|string|max:255',
            'image_url' => 'nullable|url', // Assuming simple URL input for now, or file upload
        ]);

        // If file upload handling is needed later, add here.
        // For now, we'll assume the model has 'user_id'

        $experiencia = new Experiencia($validated);
        $experiencia->user_id = auth()->id();
        $experiencia->save();

        return redirect()->route('experiencias.index')->with('success', 'Experiencia creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $experiencia = Experiencia::findOrFail($id);
        return view('dashboard.experiencias.show', compact('experiencia'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $experiencia = Experiencia::findOrFail($id);

        // Authorization check
        if (auth()->user()->role !== 'admin' && $experiencia->user_id !== auth()->id()) {
            abort(403);
        }

        return view('dashboard.experiencias.create', compact('experiencia')); // Reusing create view for edit
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $experiencia = Experiencia::findOrFail($id);

        if (auth()->user()->role !== 'admin' && $experiencia->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'image_url' => 'nullable|url',
        ]);

        $experiencia->update($validated);

        return redirect()->route('experiencias.index')->with('success', 'Experiencia actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $experiencia = Experiencia::findOrFail($id);

        if (auth()->user()->role !== 'admin' && $experiencia->user_id !== auth()->id()) {
            abort(403);
        }

        $experiencia->delete();

        return redirect()->route('experiencias.index')->with('success', 'Experiencia eliminada correctamente.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SocioController extends Controller
{
    /**
     * Display a listing of socios.
     */
    public function index()
    {
        $socios = User::where('role', 'socio')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.socios.index', compact('socios'));
    }

    /**
     * Show the form for creating a new socio.
     */
    public function create()
    {
        return view('admin.socios.create');
    }

    /**
     * Store a newly created socio in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'socio';

        $socio = User::create($validated);

        return redirect()->route('admin.socios.index')
            ->with('success', '✅ Cuenta de socio creada exitosamente para ' . $socio->name);
    }

    /**
     * Show the form for editing the specified socio.
     */
    public function edit(User $socio)
    {
        // Verificar que el usuario sea socio
        if ($socio->role !== 'socio') {
            return redirect()->route('admin.socios.index')
                ->with('error', 'Solo puedes editar cuentas de socios');
        }

        return view('admin.socios.edit', compact('socio'));
    }

    /**
     * Update the specified socio in storage.
     */
    public function update(Request $request, User $socio)
    {
        // Verificar que el usuario sea socio
        if ($socio->role !== 'socio') {
            return redirect()->route('admin.socios.index')
                ->with('error', 'Solo puedes editar cuentas de socios');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($socio->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
        ]);

        // Solo actualizar password si se proporcionó uno nuevo
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $socio->update($validated);

        return redirect()->route('admin.socios.index')
            ->with('success', '✅ Cuenta de socio actualizada exitosamente');
    }

    /**
     * Remove the specified socio from storage.
     */
    public function destroy(User $socio)
    {
        // Verificar que el usuario sea socio
        if ($socio->role !== 'socio') {
            return redirect()->route('admin.socios.index')
                ->with('error', 'Solo puedes eliminar cuentas de socios');
        }

        $name = $socio->name;
        $socio->delete();

        return redirect()->route('admin.socios.index')
            ->with('success', "✅ Cuenta de socio eliminada: {$name}");
    }

    /**
     * Toggle socio status (activo/inactivo)
     */
    public function toggleStatus(User $socio)
    {
        if ($socio->role !== 'socio') {
            return response()->json([
                'success' => false,
                'message' => 'Solo puedes cambiar el estado de socios'
            ], 403);
        }

        $newStatus = !$socio->is_active;

        $socio->update([
            'is_active' => $newStatus,
            'deactivated_at' => $newStatus ? null : now(),
            'deactivated_by' => $newStatus ? null : auth()->id(),
        ]);

        $statusText = $newStatus ? 'activada' : 'desactivada';

        return response()->json([
            'success' => true,
            'message' => "Cuenta {$statusText} exitosamente",
            'is_active' => $newStatus
        ]);
    }

    /**
     * Vista de supervisión general
     */
    public function supervision()
    {
        $stats = [
            'total_socios' => User::where('role', 'socio')->count(),
            'activos' => User::where('role', 'socio')->where('is_active', true)->count(),
            'inactivos' => User::where('role', 'socio')->where('is_active', false)->count(),
            'nuevos_mes' => User::where('role', 'socio')->whereMonth('created_at', now()->month)->count(),
            'total_locales' => \App\Models\Locale::count(),
            'total_eventos' => \App\Models\Evento::count(),
            'total_promociones' => \App\Models\Promocion::count(),
            'total_experiencias' => \App\Models\Experiencia::count(),
        ];

        // Socios con más actividad
        $sociosMasActivos = User::where('role', 'socio')
            ->withCount('locales')
            ->orderBy('locales_count', 'desc')
            ->limit(10)
            ->get();

        // Actividad reciente
        $actividadReciente = User::where('role', 'socio')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Socios inactivos
        $sociosInactivos = User::where('role', 'socio')
            ->where('is_active', false)
            ->with('deactivatedBy')
            ->orderBy('deactivated_at', 'desc')
            ->get();

        return view('admin.socios.supervision', compact(
            'stats',
            'sociosMasActivos',
            'actividadReciente',
            'sociosInactivos'
        ));
    }

    /**
     * Relación con el admin que desactivó
     */
    public function deactivatedBy()
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Experiencia;
use App\Models\Locale;
use App\Models\Promocion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    protected array $sortable = ['created_at', 'name', 'email', 'role'];

    public function index(Request $request)
    {
        $query = User::query()->withCount(['locales', 'eventos', 'experiencias', 'promociones']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $sort = in_array($request->get('sort'), $this->sortable, true) ? $request->get('sort') : 'created_at';
        $order = $request->get('order') === 'asc' ? 'asc' : 'desc';

        return response()->json([
            'success' => true,
            'data' => $query->orderBy($sort, $order)->paginate(15),
        ]);
    }

    public function show(User $user)
    {
        $user->loadCount(['locales', 'eventos', 'experiencias', 'promociones', 'reviews', 'favorites']);

        return response()->json(['success' => true, 'data' => $user]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'role' => ['required', 'in:tourist,socio,admin'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Usuario {$user->name} creado exitosamente",
            'data' => $user,
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:tourist,socio,admin'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        // Un admin no puede quitarse a sí mismo el rol de admin
        if ($user->id === $request->user()->id && $validated['role'] !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'No puedes quitarte tu propio rol de administrador.',
            ], 422);
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente',
            'data' => $user,
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminar tu propia cuenta',
            ], 422);
        }

        $user->delete();

        return response()->json(['success' => true, 'message' => 'Usuario eliminado exitosamente']);
    }

    public function toggleStatus(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes desactivar tu propia cuenta',
            ], 422);
        }

        $active = ! $user->is_active;

        $user->update([
            'is_active' => $active,
            'deactivated_at' => $active ? null : now(),
            'deactivated_by' => $active ? null : $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => $active ? 'Cuenta activada exitosamente' : 'Cuenta desactivada exitosamente',
            'data' => $user,
        ]);
    }

    /**
     * Resumen de supervisión de socios y contenido
     */
    public function supervision()
    {
        $socios = User::where('role', 'socio');

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total_usuarios' => User::count(),
                    'turistas' => User::where('role', 'tourist')->count(),
                    'total_socios' => (clone $socios)->count(),
                    'socios_activos' => (clone $socios)->where('is_active', true)->count(),
                    'socios_inactivos' => (clone $socios)->where('is_active', false)->count(),
                    'socios_nuevos_mes' => (clone $socios)->where('created_at', '>=', now()->startOfMonth())->count(),
                    'total_locales' => Locale::count(),
                    'total_eventos' => Evento::count(),
                    'total_promociones' => Promocion::count(),
                    'total_experiencias' => Experiencia::count(),
                ],
                'socios_mas_activos' => (clone $socios)
                    ->withCount(['locales', 'eventos', 'experiencias', 'promociones'])
                    ->orderByDesc('locales_count')
                    ->limit(10)
                    ->get(),
                'socios_inactivos' => (clone $socios)
                    ->where('is_active', false)
                    ->with('deactivatedBy:id,name')
                    ->orderByDesc('deactivated_at')
                    ->get(),
            ],
        ]);
    }
}

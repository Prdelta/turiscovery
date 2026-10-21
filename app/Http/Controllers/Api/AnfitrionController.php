<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anfitrion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class AnfitrionController extends Controller
{
    /** Anfitriones públicos con cuántas experiencias ofrecen */
    public function index(Request $request)
    {
        $query = Anfitrion::active()
            ->withCount(['experiencias' => fn ($q) => $q->active()])
            ->orderBy('name');

        if ($request->filled('language')) {
            $query->whereJsonContains('languages', $request->language);
        }

        if ($request->filled('community')) {
            $query->whereLike('community', '%'.$request->community.'%');
        }

        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    public function show($id)
    {
        $anfitrion = Anfitrion::active()
            ->with(['experiencias' => fn ($q) => $q->active()->latest()])
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $anfitrion]);
    }

    /** Anfitriones del socio (todos, si es administrador) */
    public function mine(Request $request)
    {
        $query = Anfitrion::query()->withCount('experiencias')->orderBy('name');

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['user_id'] = $request->user()->id;

        $anfitrion = Anfitrion::create($data);

        return response()->json(['success' => true, 'message' => 'Anfitrión creado', 'data' => $anfitrion], 201);
    }

    public function update(Request $request, Anfitrion $anfitrion)
    {
        Gate::authorize('update', $anfitrion);
        $anfitrion->update($this->validated($request, true));

        return response()->json(['success' => true, 'message' => 'Anfitrión actualizado', 'data' => $anfitrion]);
    }

    public function destroy(Anfitrion $anfitrion)
    {
        Gate::authorize('delete', $anfitrion);
        $anfitrion->delete();

        return response()->json(['success' => true, 'message' => 'Anfitrión eliminado']);
    }

    private function validated(Request $request, bool $partial = false): array
    {
        return $request->validate([
            'name' => ($partial ? 'sometimes|' : '').'required|string|max:255',
            'community' => 'nullable|string|max:255',
            'role_title' => 'nullable|string|max:255',
            'languages' => 'nullable|array',
            'languages.*' => [Rule::in(Anfitrion::LANGUAGES)],
            'photo_url' => 'nullable|url|max:500',
            'story' => 'nullable|string|max:3000',
            'greeting' => 'nullable|string|max:500',
            'greeting_translation' => 'nullable|string|max:500',
            'audio_url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
        ]);
    }
}

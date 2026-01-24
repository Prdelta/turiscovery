<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventAttendee;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventAttendeeController extends Controller
{
    public function index()
    {
        $attendances = Auth::user()->eventAttendances()
            ->with('evento')
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $attendances,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'evento_id' => 'required|exists:eventos,id',
            'status' => 'nullable|in:confirmed,maybe',
            'guests' => 'nullable|integer|min:1|max:10',
        ]);

        $evento = Evento::findOrFail($validated['evento_id']);

        if ($evento->end_time && $evento->end_time->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes confirmar asistencia a un evento que ya finalizó.',
            ], 422);
        }

        $existing = EventAttendee::where('user_id', Auth::id())
            ->where('evento_id', $evento->id)
            ->first();

        if ($existing) {
            $existing->update([
                'status' => $validated['status'] ?? 'confirmed',
                'guests' => $validated['guests'] ?? 1,
            ]);

            $existing->load('evento');

            return response()->json([
                'success' => true,
                'message' => 'Tu confirmación de asistencia fue actualizada.',
                'data' => $existing,
            ]);
        }

        $attendance = EventAttendee::create([
            'user_id' => Auth::id(),
            'evento_id' => $evento->id,
            'status' => $validated['status'] ?? 'confirmed',
            'guests' => $validated['guests'] ?? 1,
        ]);

        $attendance->load('evento');

        return response()->json([
            'success' => true,
            'message' => '¡Asistencia confirmada! Nos vemos en el evento.',
            'data' => $attendance,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $attendance = EventAttendee::findOrFail($id);

        if ($attendance->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para actualizar esta confirmación.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:confirmed,maybe,cancelled',
            'guests' => 'nullable|integer|min:1|max:10',
        ]);

        $attendance->update($validated);
        $attendance->load('evento');

        return response()->json([
            'success' => true,
            'message' => 'Confirmación actualizada.',
            'data' => $attendance,
        ]);
    }

    public function destroy($eventoId)
    {
        $attendance = EventAttendee::where('user_id', Auth::id())
            ->where('evento_id', $eventoId)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No has confirmado asistencia a este evento.',
            ], 404);
        }

        $attendance->cancel();

        return response()->json([
            'success' => true,
            'message' => 'Tu asistencia fue cancelada.',
        ]);
    }

    public function check($eventoId)
    {
        $attendance = EventAttendee::where('user_id', Auth::id())
            ->where('evento_id', $eventoId)
            ->where('status', '!=', 'cancelled')
            ->first();

        return response()->json([
            'success' => true,
            'attending' => (bool) $attendance,
            'data' => $attendance,
        ]);
    }

    public function count($eventoId)
    {
        $confirmed = EventAttendee::where('evento_id', $eventoId)
            ->confirmed()
            ->sum('guests');

        $maybe = EventAttendee::where('evento_id', $eventoId)
            ->maybe()
            ->sum('guests');

        return response()->json([
            'success' => true,
            'data' => [
                'confirmed' => $confirmed,
                'maybe' => $maybe,
                'total' => $confirmed + $maybe,
            ],
        ]);
    }
}

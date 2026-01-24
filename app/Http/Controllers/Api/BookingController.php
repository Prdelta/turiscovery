<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Experiencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Listar las reservas del usuario autenticado
     */
    public function index(Request $request)
    {
        $bookings = Auth::user()->bookings()
            ->with('experiencia')
            ->orderBy('booking_date', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    /**
     * Crear una nueva reserva
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'experiencia_id' => 'required|exists:experiencias,id',
            'booking_date' => 'required|date|after:today',
            'participants' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
        ]);

        $experiencia = Experiencia::findOrFail($validated['experiencia_id']);

        // Verificar disponibilidad
        if ($experiencia->max_participants) {
            $bookedParticipants = Booking::where('experiencia_id', $experiencia->id)
                ->where('booking_date', $validated['booking_date'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->sum('participants');

            $availableSlots = $experiencia->max_participants - $bookedParticipants;

            if ($validated['participants'] > $availableSlots) {
                return response()->json([
                    'success' => false,
                    'message' => "Solo hay {$availableSlots} cupos disponibles para esa fecha.",
                ], 422);
            }
        }

        // Calcular precio total
        $totalPrice = $experiencia->price * $validated['participants'];

        // Crear la reserva
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'experiencia_id' => $validated['experiencia_id'],
            'booking_date' => $validated['booking_date'],
            'participants' => $validated['participants'],
            'total_price' => $totalPrice,
            'notes' => $validated['notes'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? Auth::user()->phone,
            'contact_email' => $validated['contact_email'] ?? Auth::user()->email,
            'status' => 'pending',
        ]);

        $booking->load('experiencia');

        return response()->json([
            'success' => true,
            'message' => '¡Reserva creada exitosamente! Te contactaremos pronto para confirmar.',
            'data' => $booking,
        ], 201);
    }

    /**
     * Ver detalles de una reserva
     */
    public function show($id)
    {
        $booking = Booking::with('experiencia')->findOrFail($id);

        // Verificar que la reserva pertenece al usuario
        if ($booking->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver esta reserva.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $booking,
        ]);
    }

    /**
     * Cancelar una reserva
     */
    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);

        // Verificar que la reserva pertenece al usuario
        if ($booking->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para cancelar esta reserva.',
            ], 403);
        }

        if ($booking->cancel()) {
            return response()->json([
                'success' => true,
                'message' => 'Reserva cancelada exitosamente.',
                'data' => $booking,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Esta reserva no puede ser cancelada.',
        ], 422);
    }

    /**
     * Verificar disponibilidad para una experiencia en una fecha
     */
    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'experiencia_id' => 'required|exists:experiencias,id',
            'booking_date' => 'required|date|after:today',
            'participants' => 'required|integer|min:1',
        ]);

        $experiencia = Experiencia::findOrFail($validated['experiencia_id']);

        if (!$experiencia->max_participants) {
            return response()->json([
                'success' => true,
                'available' => true,
                'message' => 'Esta experiencia no tiene límite de participantes.',
            ]);
        }

        $bookedParticipants = Booking::where('experiencia_id', $experiencia->id)
            ->where('booking_date', $validated['booking_date'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->sum('participants');

        $availableSlots = $experiencia->max_participants - $bookedParticipants;
        $isAvailable = $validated['participants'] <= $availableSlots;

        return response()->json([
            'success' => true,
            'available' => $isAvailable,
            'available_slots' => $availableSlots,
            'max_participants' => $experiencia->max_participants,
            'message' => $isAvailable
                ? "Hay {$availableSlots} cupos disponibles."
                : "Solo hay {$availableSlots} cupos disponibles.",
        ]);
    }
}

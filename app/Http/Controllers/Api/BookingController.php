<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Experiencia;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

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
     * Crear una nueva reserva (sin sobreventa: ver BookingService::create)
     */
    public function store(Request $request, BookingService $bookings)
    {
        $validated = $request->validate([
            'experiencia_id' => 'required|exists:experiencias,id',
            'booking_date' => 'required|date|after:today',
            'participants' => 'required|integer|min:1|max:50',
            'notes' => 'nullable|string|max:500',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
        ]);

        $booking = $bookings->create($request->user(), $validated);

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

        Gate::authorize('view', $booking);

        return response()->json([
            'success' => true,
            'data' => $booking,
        ]);
    }

    /**
     * Cancelar una reserva (la hace el viajero)
     */
    public function cancel($id, BookingService $bookings)
    {
        $booking = Booking::findOrFail($id);

        Gate::authorize('cancel', $booking);

        if ($bookings->cancelByTourist($booking)) {
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
    public function checkAvailability(Request $request, BookingService $bookings)
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

        $availableSlots = $bookings->availableSeats($experiencia, $validated['booking_date']);
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

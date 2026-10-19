<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

/**
 * Reservas recibidas por las experiencias de un socio
 * (los administradores ven todas)
 */
class PartnerBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['experiencia:id,title,user_id', 'user:id,name,email,phone']);

        if (!$request->user()->isAdmin()) {
            $query->whereHas('experiencia', fn ($q) => $q->where('user_id', $request->user()->id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('booking_date')->paginate(20),
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmed,cancelled,completed',
        ]);

        $booking = Booking::with('experiencia:id,user_id')->findOrFail($id);

        if (!$request->user()->isAdmin() && $booking->experiencia?->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Esta reserva no pertenece a tus experiencias.',
            ], 403);
        }

        $booking->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Reserva actualizada',
            'data' => $booking,
        ]);
    }
}

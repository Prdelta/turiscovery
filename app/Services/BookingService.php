<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Experiencia;
use App\Models\User;
use App\Notifications\BookingNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Reglas de las reservas: cupos, creación sin sobreventa y cambios de estado.
 *
 * Los cupos se cuentan con las reservas pendientes y confirmadas. Para que dos pedidos
 * simultáneos no tomen los mismos cupos, la creación bloquea la fila de la experiencia
 * (SELECT … FOR UPDATE) y vuelve a contar dentro de la transacción.
 */
class BookingService
{
    /** Estados que ocupan cupo */
    public const ACTIVE = ['pending', 'confirmed'];

    /** Cambios que puede hacer el socio (o un admin) sobre una reserva */
    public const TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['completed', 'cancelled'],
        'cancelled' => [],
        'completed' => [],
    ];

    /** Zona horaria de Puno: "hoy" y "mañana" se cuentan en hora local */
    public const TIMEZONE = 'America/Lima';

    public function bookedSeats(Experiencia $experiencia, string $date): int
    {
        return (int) Booking::where('experiencia_id', $experiencia->id)
            ->whereDate('booking_date', $date)
            ->whereIn('status', self::ACTIVE)
            ->sum('participants');
    }

    /** Cupos libres en esa fecha, o null si la experiencia no tiene límite */
    public function availableSeats(Experiencia $experiencia, string $date): ?int
    {
        if (! $experiencia->max_participants) {
            return null;
        }

        return max(0, $experiencia->max_participants - $this->bookedSeats($experiencia, $date));
    }

    /**
     * Crea una reserva pendiente y avisa al socio.
     *
     * @param  array{experiencia_id:int, booking_date:string, participants:int, notes?:?string, contact_phone?:?string, contact_email?:?string}  $data
     *
     * @throws ValidationException si la experiencia no recibe reservas o no hay cupos
     */
    public function create(User $user, array $data): Booking
    {
        $booking = DB::transaction(function () use ($user, $data) {
            // Bloquea la experiencia: las reservas de la misma experiencia se procesan de a una
            $experiencia = Experiencia::whereKey($data['experiencia_id'])->lockForUpdate()->firstOrFail();

            if (! $experiencia->is_active) {
                throw ValidationException::withMessages([
                    'experiencia_id' => 'Esta experiencia ya no recibe reservas.',
                ]);
            }

            $available = $this->availableSeats($experiencia, $data['booking_date']);
            if ($available !== null && $data['participants'] > $available) {
                throw ValidationException::withMessages([
                    'participants' => $available === 0
                        ? 'Ya no quedan cupos para esa fecha. Elige otro día.'
                        : "Solo quedan {$available} cupos para esa fecha.",
                ]);
            }

            return Booking::create([
                'user_id' => $user->id,
                'experiencia_id' => $experiencia->id,
                'booking_date' => $data['booking_date'],
                'participants' => $data['participants'],
                'total_price' => ($experiencia->price_pen ?? 0) * $data['participants'],
                'notes' => $data['notes'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? $user->phone,
                'contact_email' => $data['contact_email'] ?? $user->email,
                'status' => 'pending',
            ]);
        });

        $booking->load('experiencia');

        // Fuera de la transacción: un fallo al avisar no anula la reserva
        rescue(fn () => $booking->experiencia?->user?->notify(new BookingNotification($booking, 'requested')));

        return $booking;
    }

    /**
     * Cambio de estado hecho por el socio dueño (o un admin). Avisa al viajero.
     *
     * @throws ValidationException si el cambio no está permitido
     */
    public function changeStatus(Booking $booking, string $status): Booking
    {
        if ($booking->status === $status) {
            return $booking;
        }

        $allowed = self::TRANSITIONS[$booking->status] ?? [];
        if (! in_array($status, $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => match ($booking->status) {
                    'cancelled' => 'La reserva está cancelada: el viajero debe hacer una nueva.',
                    'completed' => 'La reserva ya está completada.',
                    default => 'Ese cambio de estado no está permitido.',
                },
            ]);
        }

        if ($status === 'completed' && $booking->booking_date->toDateString() > now(self::TIMEZONE)->toDateString()) {
            throw ValidationException::withMessages([
                'status' => 'Solo puedes marcarla como completada desde el día de la salida.',
            ]);
        }

        $booking->update(['status' => $status]);

        rescue(fn () => $booking->user?->notify(new BookingNotification($booking, $status)));

        return $booking;
    }

    /** Cancelación hecha por el viajero. Avisa al socio. */
    public function cancelByTourist(Booking $booking): bool
    {
        if (! $booking->cancel()) {
            return false;
        }

        rescue(fn () => $booking->experiencia?->user?->notify(new BookingNotification($booking, 'cancelled_by_tourist')));

        return true;
    }
}

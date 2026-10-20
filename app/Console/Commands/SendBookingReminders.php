<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Notifications\BookingNotification;
use App\Services\BookingService;
use Illuminate\Console\Command;

/**
 * Recordatorio del día anterior: avisa al viajero de cada reserva confirmada para mañana
 * (hora de Puno), con el punto y la hora de encuentro. Cada reserva se recuerda una sola vez.
 *
 * Se ejecuta a diario desde routes/console.php. No cambia estados: de eso se encarga turiscovery:expire.
 */
class SendBookingReminders extends Command
{
    protected $signature = 'bookings:remind {--dry-run : Solo contar, sin enviar}';

    protected $description = 'Recuerda a los viajeros las salidas confirmadas de mañana';

    public function handle(): int
    {
        $tomorrow = now(BookingService::TIMEZONE)->addDay()->toDateString();

        $due = Booking::with(['user', 'experiencia:id,title,meeting_point,meeting_time'])
            ->where('status', 'confirmed')
            ->whereDate('booking_date', $tomorrow)
            ->whereNull('reminded_at');

        if ($this->option('dry-run')) {
            $this->info("Recordatorios por enviar para el {$tomorrow}: {$due->count()}");

            return self::SUCCESS;
        }

        $sent = 0;
        $due->chunkById(100, function ($bookings) use (&$sent) {
            foreach ($bookings as $booking) {
                // Se marca aunque falle el correo: el aviso en la app ya quedó guardado
                rescue(fn () => $booking->user?->notify(new BookingNotification($booking, 'reminder')));
                $booking->update(['reminded_at' => now()]);
                $sent++;
            }
        });

        $this->info("Recordatorios enviados para el {$tomorrow}: {$sent}");

        return self::SUCCESS;
    }
}

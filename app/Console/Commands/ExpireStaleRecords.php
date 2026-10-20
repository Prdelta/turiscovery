<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\UserCoupon;
use App\Notifications\BookingNotification;
use Illuminate\Console\Command;

/**
 * Pone al día los estados que dependen de la fecha:
 * - cupones disponibles cuya fecha de vencimiento pasó → expired
 * - reservas confirmadas de días pasados → completed (y se pide la reseña)
 * - reservas pendientes de días pasados que nadie confirmó → cancelled
 *
 * Se ejecuta a diario desde routes/console.php (requiere `php artisan schedule:work` o el cron de Laravel).
 */
class ExpireStaleRecords extends Command
{
    protected $signature = 'turiscovery:expire {--dry-run : Solo contar, sin cambiar nada}';

    protected $description = 'Vence cupones y cierra reservas de fechas pasadas';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $today = now()->startOfDay();

        $coupons = UserCoupon::where('status', 'available')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());

        $confirmed = Booking::with(['user', 'experiencia:id,title'])
            ->where('status', 'confirmed')
            ->where('booking_date', '<', $today);

        $pending = Booking::where('status', 'pending')
            ->where('booking_date', '<', $today);

        if ($dry) {
            $this->info("Cupones por vencer: {$coupons->count()}");
            $this->info("Reservas por completar: {$confirmed->count()}");
            $this->info("Reservas pendientes por cancelar: {$pending->count()}");

            return self::SUCCESS;
        }

        $expired = $coupons->update(['status' => 'expired']);

        $completed = 0;
        $confirmed->chunkById(100, function ($bookings) use (&$completed) {
            foreach ($bookings as $booking) {
                $booking->update(['status' => 'completed']);
                rescue(fn () => $booking->user?->notify(new BookingNotification($booking, 'completed')));
                $completed++;
            }
        });

        $cancelled = $pending->update(['status' => 'cancelled']);

        $this->info("Cupones vencidos: {$expired} · Reservas completadas: {$completed} · Pendientes canceladas: {$cancelled}");

        return self::SUCCESS;
    }
}

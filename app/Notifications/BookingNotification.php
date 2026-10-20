<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Avisos del ciclo de una reserva, en la app y por correo.
 *
 * - requested: llega una reserva nueva (al socio)
 * - cancelled_by_tourist: el viajero la canceló (al socio)
 * - confirmed / cancelled / completed: el socio cambió el estado (al viajero)
 * - reminder: la salida es mañana (al viajero)
 *
 * El aviso en la app se guarda siempre (canal database); el correo respeta la
 * preferencia "notifications.bookings". Se envía de forma síncrona (no hay worker
 * de colas en todos los entornos).
 */
class BookingNotification extends Notification
{
    use Queueable;

    public const EVENTS = ['requested', 'cancelled_by_tourist', 'confirmed', 'cancelled', 'completed', 'reminder'];

    public function __construct(public Booking $booking, public string $event)
    {
    }

    public function via(object $notifiable): array
    {
        return $notifiable->wantsEmail('bookings') ? ['database', 'mail'] : ['database'];
    }

    /** Aviso en la app: texto corto y adónde lleva */
    public function toArray(object $notifiable): array
    {
        $booking = $this->booking->loadMissing(['experiencia:id,title,meeting_point,meeting_time', 'user:id,name']);
        $title = $booking->experiencia?->title ?? 'tu experiencia';
        $date = $this->shortDate();
        $people = $this->people();

        [$message, $url] = match ($this->event) {
            'requested' => ["{$booking->user?->name} reservó {$title} para el {$date} ({$people}).", '/dashboard/reservas'],
            'cancelled_by_tourist' => ["{$booking->user?->name} canceló {$title} del {$date}. Los cupos vuelven a estar libres.", '/dashboard/reservas'],
            'confirmed' => ["Tu reserva de {$title} para el {$date} está confirmada.", '/cuenta/reservas'],
            'cancelled' => ["El anfitrión no puede recibirte en {$title} el {$date}. Elige otra fecha.", '/cuenta/reservas'],
            'completed' => ["¿Qué tal {$title}? Cuéntanos cómo te fue.", "/experiencias/{$booking->experiencia_id}#resenas"],
            'reminder' => ["Mañana es {$title}" . $this->meetingText(' — ') . '.', '/cuenta/reservas'],
            default => ["Tu reserva de {$title} cambió de estado.", '/cuenta/reservas'],
        };

        return [
            'kind' => 'booking',
            'event' => $this->event,
            'booking_id' => $booking->id,
            'experiencia_id' => $booking->experiencia_id,
            'title' => $title,
            'message' => $message,
            'url' => $url,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking->loadMissing(['experiencia:id,title,meeting_point,meeting_time', 'user:id,name,email,phone']);
        $title = $booking->experiencia?->title ?? 'tu experiencia';
        $date = $booking->booking_date?->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');
        $people = $this->people();
        $front = rtrim(config('app.frontend_url'), '/');

        $mail = (new MailMessage)->greeting("Hola, {$notifiable->name}");

        return match ($this->event) {
            'requested' => $mail
                ->subject("Nueva reserva: {$title}")
                ->line("{$booking->user?->name} reservó **{$title}** para el {$date} ({$people}).")
                ->line('Contacto: ' . collect([$booking->contact_email, $booking->contact_phone])->filter()->implode(' · '))
                ->when($booking->notes, fn (MailMessage $m) => $m->line("Nota del viajero: “{$booking->notes}”"))
                ->action('Confirmar o rechazar', "{$front}/dashboard/reservas")
                ->line('El viajero espera tu confirmación.'),

            'cancelled_by_tourist' => $mail
                ->subject("Reserva cancelada: {$title}")
                ->line("{$booking->user?->name} canceló su reserva de **{$title}** para el {$date} ({$people}).")
                ->line('Esos cupos vuelven a estar disponibles.')
                ->action('Ver reservas', "{$front}/dashboard/reservas"),

            'confirmed' => $mail
                ->subject("Reserva confirmada: {$title}")
                ->line("Tu reserva de **{$title}** para el {$date} ({$people}) está confirmada.")
                ->when($this->meetingText(), fn (MailMessage $m) => $m->line('Punto de encuentro: ' . $this->meetingText() . '.'))
                ->line('Recuerda llegar con tiempo y abrigarte: en el altiplano el clima cambia rápido.')
                ->action('Ver mi reserva', "{$front}/cuenta/reservas"),

            'cancelled' => $mail
                ->subject("Reserva no disponible: {$title}")
                ->line("El anfitrión no puede recibirte en **{$title}** el {$date}.")
                ->line('Puedes elegir otra fecha o buscar otra experiencia.')
                ->action('Ver experiencias', "{$front}/experiencias"),

            'completed' => $mail
                ->subject("¿Qué tal {$title}?")
                ->line("Esperamos que hayas disfrutado **{$title}**.")
                ->line('Tu reseña ayuda a otros viajeros y al anfitrión.')
                ->action('Escribir una reseña', "{$front}/experiencias/{$booking->experiencia_id}"),

            'reminder' => $mail
                ->subject("Mañana: {$title}")
                ->line("Te esperamos mañana en **{$title}** ({$people}).")
                ->when($this->meetingText(), fn (MailMessage $m) => $m->line('Punto de encuentro: ' . $this->meetingText() . '.'))
                ->line('Lleva abrigo, protector solar y agua. Si estás recién llegado a Puno, desayuna liviano.')
                ->line('Guarda tu reserva para verla sin conexión: en el lago casi no hay señal.')
                ->action('Ver mi reserva', "{$front}/cuenta/reservas"),

            default => $mail->subject("Reserva actualizada: {$title}")->line('Tu reserva cambió de estado.'),
        };
    }

    private function people(): string
    {
        $n = $this->booking->participants;

        return $n . ' ' . ($n == 1 ? 'persona' : 'personas');
    }

    private function shortDate(): string
    {
        return (string) $this->booking->booking_date?->locale('es')->isoFormat('D [de] MMMM');
    }

    /** "Puerto de Puno, 05:30 h" (o vacío si el socio no lo cargó) */
    private function meetingText(string $prefix = ''): string
    {
        $e = $this->booking->experiencia;
        $text = collect([$e?->meeting_point, $e?->meeting_time ? substr($e->meeting_time, 0, 5) . ' h' : null])->filter()->implode(', ');

        return $text ? $prefix . $text : '';
    }
}

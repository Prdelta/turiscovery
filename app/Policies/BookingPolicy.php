<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    public function view(User $user, Booking $booking): Response
    {
        return $this->owner($user, $booking, 'ver');
    }

    public function cancel(User $user, Booking $booking): Response
    {
        return $this->owner($user, $booking, 'cancelar');
    }

    private function owner(User $user, Booking $booking, string $action): Response
    {
        return (int) $booking->user_id === (int) $user->id
            ? Response::allow()
            : Response::deny("No tienes permiso para {$action} esta reserva.");
    }
}

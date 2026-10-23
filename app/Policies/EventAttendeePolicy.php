<?php

namespace App\Policies;

use App\Models\EventAttendee;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventAttendeePolicy
{
    public function update(User $user, EventAttendee $attendance): Response
    {
        return (int) $attendance->user_id === (int) $user->id
            ? Response::allow()
            : Response::deny('No tienes permiso para actualizar esta confirmación.');
    }
}

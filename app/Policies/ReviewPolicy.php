<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    /** Solo el autor edita su reseña */
    public function update(User $user, Review $review): Response
    {
        return (int) $review->user_id === (int) $user->id
            ? Response::allow()
            : Response::deny('Solo puedes editar tus propias reseñas.');
    }

    /** El autor o un administrador (moderación) pueden eliminarla */
    public function delete(User $user, Review $review): Response
    {
        return $user->isAdmin() || (int) $review->user_id === (int) $user->id
            ? Response::allow()
            : Response::deny('Solo puedes eliminar tus propias reseñas.');
    }
}

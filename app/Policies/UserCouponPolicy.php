<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserCoupon;
use Illuminate\Auth\Access\Response;

class UserCouponPolicy
{
    public function view(User $user, UserCoupon $coupon): Response
    {
        return (int) $coupon->user_id === (int) $user->id
            ? Response::allow()
            : Response::deny('No tienes permiso para ver este cupón.');
    }

    /**
     * Canjear lo hace el local (dueño de la promoción) o un administrador,
     * no el viajero: así el código se valida al cobrar.
     */
    public function redeem(User $user, UserCoupon $coupon): Response
    {
        $coupon->loadMissing('promocion:id,user_id');

        return $user->isAdmin() || (int) $coupon->promocion?->user_id === (int) $user->id
            ? Response::allow()
            : Response::deny('El cupón lo canjea el local al cobrar: muéstrale el código.');
    }
}

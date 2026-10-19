<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserCoupon;
use Illuminate\Auth\Access\Response;

class UserCouponPolicy
{
    public function view(User $user, UserCoupon $coupon): Response
    {
        return $this->owner($user, $coupon, 'ver');
    }

    public function redeem(User $user, UserCoupon $coupon): Response
    {
        return $this->owner($user, $coupon, 'usar');
    }

    private function owner(User $user, UserCoupon $coupon, string $action): Response
    {
        return (int) $coupon->user_id === (int) $user->id
            ? Response::allow()
            : Response::deny("No tienes permiso para {$action} este cupón.");
    }
}

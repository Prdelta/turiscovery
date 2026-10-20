<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserCoupon;
use Illuminate\Http\Request;

/**
 * Canje de cupones en el local: el socio busca el código que le muestra el viajero
 * y lo marca como usado. Solo ve los cupones de sus propias promociones
 * (los administradores, todos).
 */
class PartnerCouponController extends Controller
{
    /**
     * Ver un cupón por su código antes de canjearlo
     */
    public function lookup(Request $request)
    {
        $coupon = $this->findForPartner($request);

        return response()->json([
            'success' => true,
            'data' => $this->present($coupon),
        ]);
    }

    /**
     * Marcar el cupón como usado
     */
    public function redeem(Request $request)
    {
        $coupon = $this->findForPartner($request);

        if ($coupon->expires_at?->isPast() && $coupon->status === 'available') {
            $coupon->markAsExpired();
        }

        if ($coupon->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => $coupon->status === 'used'
                    ? 'Este cupón ya se usó el '.$coupon->used_at?->locale('es')->isoFormat('D [de] MMMM [a las] HH:mm').'.'
                    : 'Este cupón ya venció.',
                'data' => $this->present($coupon),
            ], 422);
        }

        $coupon->markAsUsed();

        return response()->json([
            'success' => true,
            'message' => 'Cupón canjeado. Aplica el descuento al cobrar.',
            'data' => $this->present($coupon->fresh(['promocion.locale:id,name', 'user:id,name'])),
        ]);
    }

    /** Recientes canjeados en las promociones del socio */
    public function index(Request $request)
    {
        $query = UserCoupon::with(['promocion:id,title,user_id', 'user:id,name'])
            ->where('status', 'used')
            ->latest('used_at');

        if (! $request->user()->isAdmin()) {
            $query->whereHas('promocion', fn ($q) => $q->where('user_id', $request->user()->id));
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate(20),
        ]);
    }

    private function findForPartner(Request $request): UserCoupon
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:40'],
        ]);

        $code = strtoupper(trim($validated['code']));
        $coupon = UserCoupon::with(['promocion.locale:id,name', 'user:id,name'])
            ->where('coupon_code', $code)
            ->first();

        // Mismo mensaje si no existe o si es de otro socio: no se revela qué códigos existen
        if (! $coupon || (! $request->user()->isAdmin() && (int) $coupon->promocion?->user_id !== (int) $request->user()->id)) {
            abort(404, 'No encontramos ese código entre los cupones de tus promociones.');
        }

        return $coupon;
    }

    private function present(UserCoupon $coupon): array
    {
        $promo = $coupon->promocion;
        $expired = $coupon->status === 'expired'
            || ($coupon->status === 'available' && $coupon->expires_at?->isPast());

        return [
            'id' => $coupon->id,
            'coupon_code' => $coupon->coupon_code,
            'status' => $expired ? 'expired' : $coupon->status,
            'claimed_at' => $coupon->claimed_at,
            'used_at' => $coupon->used_at,
            'expires_at' => $coupon->expires_at,
            'user' => $coupon->user?->only(['id', 'name']),
            'promocion' => $promo ? [
                'id' => $promo->id,
                'title' => $promo->title,
                'discount_type' => $promo->discount_type,
                'discount_percentage' => $promo->discount_percentage,
                'discount_amount' => $promo->discount_amount,
                'original_price' => $promo->original_price,
                'final_price' => $promo->final_price,
                'terms_conditions' => $promo->terms_conditions,
                'locale' => $promo->locale?->only(['id', 'name']),
            ] : null,
        ];
    }
}

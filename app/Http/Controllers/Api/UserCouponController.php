<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserCoupon;
use App\Models\Promocion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserCouponController extends Controller
{
    public function index()
    {
        $coupons = Auth::user()->coupons()
            ->with('promocion')
            ->where('status', '!=', 'expired')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $coupons,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'promocion_id' => 'required|exists:promociones,id',
        ]);

        $promocion = Promocion::findOrFail($validated['promocion_id']);

        if ($promocion->end_date && $promocion->end_date->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta promoción ya expiró.',
            ], 422);
        }

        if ($promocion->start_date && $promocion->start_date->isFuture()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta promoción aún no está disponible.',
            ], 422);
        }

        $existing = UserCoupon::where('user_id', Auth::id())
            ->where('promocion_id', $promocion->id)
            ->whereIn('status', ['available', 'used'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Ya tienes un cupón para esta promoción.',
                'data' => $existing,
            ]);
        }

        $coupon = UserCoupon::create([
            'user_id' => Auth::id(),
            'promocion_id' => $promocion->id,
            'coupon_code' => UserCoupon::generateCouponCode(),
            'status' => 'available',
            'claimed_at' => now(),
            'expires_at' => $promocion->end_date,
        ]);

        $coupon->load('promocion');

        return response()->json([
            'success' => true,
            'message' => '¡Cupón generado exitosamente! Preséntalo al momento de tu compra.',
            'data' => $coupon,
        ], 201);
    }

    public function redeem(Request $request, $id)
    {
        $coupon = UserCoupon::findOrFail($id);

        if ($coupon->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para usar este cupón.',
            ], 403);
        }

        if ($coupon->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Este cupón ya fue usado o está expirado.',
            ], 422);
        }

        $coupon->markAsUsed();
        $coupon->load('promocion');

        return response()->json([
            'success' => true,
            'message' => 'Cupón usado exitosamente.',
            'data' => $coupon,
        ]);
    }

    public function show($id)
    {
        $coupon = UserCoupon::with('promocion')->findOrFail($id);

        if ($coupon->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver este cupón.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $coupon,
        ]);
    }
}

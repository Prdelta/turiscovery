<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserCoupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'promocion_id',
        'coupon_code',
        'status',
        'claimed_at',
        'used_at',
        'expires_at',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function promocion()
    {
        return $this->belongsTo(Promocion::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function ($q) {
                $q->where('status', 'available')
                    ->where('expires_at', '<', now());
            });
    }

    public function markAsUsed()
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
        ]);
    }

    public function markAsExpired()
    {
        $this->update(['status' => 'expired']);
    }

    public static function generateCouponCode($prefix = 'TURISCO'): string
    {
        do {
            $code = $prefix . '-' . strtoupper(Str::random(8));
        } while (self::where('coupon_code', $code)->exists());

        return $code;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'favoritable_type',
        'favoritable_id',
    ];

    // ========== Relationships ==========

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent favoritable model (Locale, Evento, Experiencia, etc.)
     */
    public function favoritable()
    {
        return $this->morphTo();
    }

    // ========== Helper Methods ==========

    /**
     * Check if a user has favorited a specific item
     */
    public static function isFavorited(int $userId, string $type, int $id): bool
    {
        return self::where('user_id', $userId)
            ->where('favoritable_type', $type)
            ->where('favoritable_id', $id)
            ->exists();
    }

    /**
     * Toggle favorite status
     */
    public static function toggle(int $userId, string $type, int $id)
    {
        $favorite = self::where('user_id', $userId)
            ->where('favoritable_type', $type)
            ->where('favoritable_id', $id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return false; // Unfavorited
        } else {
            self::create([
                'user_id' => $userId,
                'favoritable_type' => $type,
                'favoritable_id' => $id,
            ]);
            return true; // Favorited
        }
    }
}

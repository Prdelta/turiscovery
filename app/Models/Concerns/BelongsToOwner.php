<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Contenido publicado por un socio (columna user_id).
 */
trait BelongsToOwner
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Lo que gestiona un usuario: un administrador ve todo */
    public function scopeManagedBy(Builder $query, User $user): Builder
    {
        return $user->isAdmin() ? $query : $query->where($this->getTable() . '.user_id', $user->id);
    }

    public function isOwnedBy(?User $user): bool
    {
        return $user !== null && (int) $this->user_id === (int) $user->id;
    }
}

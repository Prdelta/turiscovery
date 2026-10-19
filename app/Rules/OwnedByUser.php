<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

/**
 * El registro referenciado (por ejemplo, un local o un anfitrión) debe pertenecer
 * al usuario autenticado. Los administradores pueden usar cualquiera.
 */
class OwnedByUser implements ValidationRule
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(
        private string $model,
        private string $message = 'Ese registro no pertenece a tu cuenta.',
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $user = request()->user();
        if ($user?->isAdmin()) {
            return;
        }

        $owned = $user && $this->model::query()
            ->whereKey($value)
            ->where('user_id', $user->id)
            ->exists();

        if (!$owned) {
            $fail($this->message);
        }
    }
}

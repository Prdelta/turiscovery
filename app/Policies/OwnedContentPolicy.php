<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

/**
 * Contenido publicado por socios: lo crean socios y admins;
 * solo su dueño o un administrador puede editarlo o eliminarlo.
 */
abstract class OwnedContentPolicy
{
    public function create(User $user): Response
    {
        return in_array($user->role, ['socio', 'admin'], true)
            ? Response::allow()
            : Response::deny('Solo los socios pueden publicar contenido.');
    }

    public function update(User $user, Model $model): Response
    {
        return $this->ownerOrAdmin($user, $model, 'editar');
    }

    public function delete(User $user, Model $model): Response
    {
        return $this->ownerOrAdmin($user, $model, 'eliminar');
    }

    protected function ownerOrAdmin(User $user, Model $model, string $action): Response
    {
        return $user->isAdmin() || (int) $model->user_id === (int) $user->id
            ? Response::allow()
            : Response::deny("No puedes {$action} contenido de otro socio.");
    }
}

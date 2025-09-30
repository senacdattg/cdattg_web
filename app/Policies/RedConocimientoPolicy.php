<?php

namespace App\Policies;

use App\Models\RedConocimiento;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RedConocimientoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('VER RED CONOCIMIENTO');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RedConocimiento $redConocimiento): bool
    {
        return $user->can('VER RED CONOCIMIENTO');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR RED CONOCIMIENTO');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RedConocimiento $redConocimiento): bool
    {
        return $user->can('EDITAR RED CONOCIMIENTO');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RedConocimiento $redConocimiento): bool
    {
        return $user->can('ELIMINAR RED CONOCIMIENTO');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RedConocimiento $redConocimiento): bool
    {
        return $user->can('EDITAR RED CONOCIMIENTO');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RedConocimiento $redConocimiento): bool
    {
        return $user->can('ELIMINAR RED CONOCIMIENTO');
    }
}

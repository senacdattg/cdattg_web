<?php

namespace App\Policies;

use App\Models\User;
use App\Models\guia_aprendizaje;
use Illuminate\Auth\Access\Response;

class GuiaAprendizajePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, guia_aprendizaje $guiaAprendizaje): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, guia_aprendizaje $guiaAprendizaje): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, guia_aprendizaje $guiaAprendizaje): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, guia_aprendizaje $guiaAprendizaje): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, guia_aprendizaje $guiaAprendizaje): bool
    {
        return false;
    }
}

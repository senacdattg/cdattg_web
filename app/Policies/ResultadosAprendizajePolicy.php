<?php

namespace App\Policies;

use App\Models\User;
use App\Models\resultados_aprendizaje;
use Illuminate\Auth\Access\Response;

class ResultadosAprendizajePolicy
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
    public function view(User $user, resultados_aprendizaje $resultadosAprendizaje): bool
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
    public function update(User $user, resultados_aprendizaje $resultadosAprendizaje): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, resultados_aprendizaje $resultadosAprendizaje): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, resultados_aprendizaje $resultadosAprendizaje): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, resultados_aprendizaje $resultadosAprendizaje): bool
    {
        return false;
    }
}

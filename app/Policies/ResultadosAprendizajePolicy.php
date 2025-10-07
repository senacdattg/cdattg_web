<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ResultadosAprendizaje;
use Illuminate\Auth\Access\Response;

class ResultadosAprendizajePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if (!$user->can('VER RESULTADO APRENDIZAJE')) {
            return false;
        }

        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        if ($user->hasRole('INSTRUCTOR')) {
            return true;
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ResultadosAprendizaje $resultadosAprendizaje): bool
    {
        if (!$user->can('VER RESULTADO APRENDIZAJE')) {
            return false;
        }

        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        if ($user->hasRole('INSTRUCTOR')) {
            return true;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR RESULTADO APRENDIZAJE') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN', 'INSTRUCTOR']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ResultadosAprendizaje $resultadosAprendizaje): bool
    {
        if (!$user->can('EDITAR RESULTADO APRENDIZAJE')) {
            return false;
        }

        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        if ($user->hasRole('INSTRUCTOR')) {
            return $resultadosAprendizaje->user_create_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ResultadosAprendizaje $resultadosAprendizaje): bool
    {
        if (!$user->can('ELIMINAR RESULTADO APRENDIZAJE')) {
            return false;
        }

        if ($resultadosAprendizaje->guiasAprendizaje()->count() > 0) {
            return false;
        }

        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ResultadosAprendizaje $resultadosAprendizaje): bool
    {
        return $user->can('CREAR RESULTADO APRENDIZAJE') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ResultadosAprendizaje $resultadosAprendizaje): bool
    {
        return $user->can('ELIMINAR RESULTADO APRENDIZAJE') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can change the status.
     */
    public function cambiarEstado(User $user, ResultadosAprendizaje $resultadosAprendizaje): bool
    {
        if (!$user->can('EDITAR RESULTADO APRENDIZAJE')) {
            return false;
        }

        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        if ($user->hasRole('INSTRUCTOR')) {
            return $resultadosAprendizaje->user_create_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can manage competencias.
     */
    public function gestionarCompetencias(User $user, ResultadosAprendizaje $resultadosAprendizaje): bool
    {
        if (!$user->can('EDITAR RESULTADO APRENDIZAJE')) {
            return false;
        }

        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        if ($user->hasRole('INSTRUCTOR')) {
            return $resultadosAprendizaje->user_create_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can associate with guias.
     */
    public function asociarGuia(User $user, ResultadosAprendizaje $resultadosAprendizaje): bool
    {
        return $user->can('EDITAR RESULTADO APRENDIZAJE') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN', 'INSTRUCTOR']);
    }

    /**
     * Determine whether the user can export results.
     */
    public function exportar(User $user): bool
    {
        return $user->can('VER RESULTADO APRENDIZAJE') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }
}

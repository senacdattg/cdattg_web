<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Competencia;
use Illuminate\Auth\Access\Response;

class CompetenciaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if (!$user->can('VER COMPETENCIA')) {
            return false;
        }

        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
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
    public function view(User $user, Competencia $competencia): bool
    {
        if (!$user->can('VER COMPETENCIA')) {
            return false;
        }

        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
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
        return $user->can('CREAR COMPETENCIA') && 
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'INSTRUCTOR']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Competencia $competencia): bool
    {
        if (!$user->can('EDITAR COMPETENCIA')) {
            return false;
        }

        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        if ($user->hasRole('INSTRUCTOR')) {
            return $competencia->user_create_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Competencia $competencia): bool
    {
        if (!$user->can('ELIMINAR COMPETENCIA')) {
            return false;
        }

        if ($competencia->resultadosAprendizaje()->count() > 0) {
            return false;
        }

        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Competencia $competencia): bool
    {
        return $user->can('CREAR COMPETENCIA') && 
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Competencia $competencia): bool
    {
        return $user->can('ELIMINAR COMPETENCIA') && 
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can change the status.
     */
    public function cambiarEstado(User $user, Competencia $competencia): bool
    {
        if (!$user->can('EDITAR COMPETENCIA')) {
            return false;
        }

        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        if ($user->hasRole('INSTRUCTOR')) {
            return $competencia->user_create_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can manage resultados de aprendizaje.
     */
    public function gestionarResultados(User $user, Competencia $competencia): bool
    {
        if (!$user->can('EDITAR COMPETENCIA')) {
            return false;
        }

        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        if ($user->hasRole('INSTRUCTOR')) {
            return $competencia->user_create_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can associate with programas.
     */
    public function asociarPrograma(User $user, Competencia $competencia): bool
    {
        return $user->can('EDITAR COMPETENCIA') && 
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'INSTRUCTOR']);
    }

    /**
     * Determine whether the user can export competencias.
     */
    public function exportar(User $user): bool
    {
        return $user->can('VER COMPETENCIA') && 
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }
}

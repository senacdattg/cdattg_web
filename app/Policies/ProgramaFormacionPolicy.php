<?php

namespace App\Policies;

use App\Models\ProgramaFormacion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProgramaFormacionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('VER PROGRAMAS DE FORMACION');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProgramaFormacion $programaFormacion): bool
    {
        return $user->can('VER PROGRAMA DE FORMACION');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR PROGRAMA DE FORMACION');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProgramaFormacion $programaFormacion): bool
    {
        return $user->can('EDITAR PROGRAMA DE FORMACION');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProgramaFormacion $programaFormacion): bool
    {
        return $user->can('ELIMINAR PROGRAMA DE FORMACION');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProgramaFormacion $programaFormacion): bool
    {
        return $user->can('EDITAR PROGRAMA DE FORMACION');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProgramaFormacion $programaFormacion): bool
    {
        return $user->can('ELIMINAR PROGRAMA DE FORMACION');
    }

    /**
     * Determine whether the user can search programs.
     */
    public function search(User $user): bool
    {
        return $user->can('VER PROGRAMAS DE FORMACION');
    }

    /**
     * Determine whether the user can change program status.
     */
    public function cambiarEstado(User $user, ProgramaFormacion $programaFormacion): bool
    {
        return $user->can('EDITAR PROGRAMA DE FORMACION');
    }

    /**
     * Determine whether the user can view programs by red conocimiento.
     */
    public function getByRedConocimiento(User $user): bool
    {
        return $user->can('VER PROGRAMAS DE FORMACION');
    }

    /**
     * Determine whether the user can view programs by nivel formacion.
     */
    public function getByNivelFormacion(User $user): bool
    {
        return $user->can('VER PROGRAMAS DE FORMACION');
    }

    /**
     * Determine whether the user can view active programs.
     */
    public function getActivos(User $user): bool
    {
        return $user->can('VER PROGRAMAS DE FORMACION');
    }
}

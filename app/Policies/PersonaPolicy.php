<?php

namespace App\Policies;

use App\Models\Persona;
use App\Models\User;

class PersonaPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('SUPER ADMINISTRADOR')) {
            return true;
        }

        return null;
    }

    /**
     * Determina si el usuario puede ver el listado de personas.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('VER PERSONA');
    }

    /**
     * Determina si el usuario puede ver una persona concreta.
     */
    public function view(User $user, Persona $persona): bool
    {
        if ($user->can('VER PERSONA')) {
            return true;
        }

        if ($user->can('VER PERFIL') && $user->persona_id === $persona->id) {
            return true;
        }

        return false;
    }

    /**
     * Determina si el usuario puede crear personas.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR PERSONA');
    }

    /**
     * Determina si el usuario puede actualizar la información de una persona.
     */
    public function update(User $user, Persona $persona): bool
    {
        return $user->can('EDITAR PERSONA');
    }

    /**
     * Determina si el usuario puede eliminar una persona.
     */
    public function delete(User $user, Persona $persona): bool
    {
        return $user->can('ELIMINAR PERSONA');
    }

    /**
     * Determina si el usuario puede restaurar una persona.
     */
    public function restore(User $user, Persona $persona): bool
    {
        return $user->can('ELIMINAR PERSONA');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente una persona.
     */
    public function forceDelete(User $user, Persona $persona): bool
    {
        return $user->can('ELIMINAR PERSONA');
    }
}

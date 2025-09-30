<?php

namespace App\Policies;

use App\Models\Aprendiz;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AprendizPolicy
{
    /**
     * Determina si el usuario puede ver cualquier aprendiz.
     * Los instructores solo ven aprendices de sus fichas asignadas.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('VER APRENDIZ');
    }

    /**
     * Determina si el usuario puede ver un aprendiz específico.
     * Los instructores solo pueden ver aprendices de sus fichas.
     */
    public function view(User $user, Aprendiz $aprendiz): bool
    {
        // Verificar permiso básico
        if (!$user->can('VER APRENDIZ')) {
            return false;
        }

        // Si el usuario es instructor, verificar que el aprendiz esté en una de sus fichas
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->aprendizPerteneceAFichaDeInstructor($user, $aprendiz);
        }

        // Administradores y otros roles con el permiso tienen acceso total
        return true;
    }

    /**
     * Determina si el usuario puede crear aprendices.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR APRENDIZ');
    }

    /**
     * Determina si el usuario puede actualizar un aprendiz.
     * Los instructores solo pueden actualizar aprendices de sus fichas.
     */
    public function update(User $user, Aprendiz $aprendiz): bool
    {
        // Verificar permiso básico
        if (!$user->can('EDITAR APRENDIZ')) {
            return false;
        }

        // Si el usuario es instructor, verificar que el aprendiz esté en una de sus fichas
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->aprendizPerteneceAFichaDeInstructor($user, $aprendiz);
        }

        // Administradores y otros roles con el permiso tienen acceso total
        return true;
    }

    /**
     * Determina si el usuario puede eliminar un aprendiz.
     * Los instructores no pueden eliminar aprendices, solo administradores.
     */
    public function delete(User $user, Aprendiz $aprendiz): bool
    {
        // Verificar permiso básico
        if (!$user->can('ELIMINAR APRENDIZ')) {
            return false;
        }

        // Los instructores no pueden eliminar aprendices
        if ($user->hasRole('INSTRUCTOR')) {
            return false;
        }

        // Solo administradores y roles con el permiso pueden eliminar
        return true;
    }

    /**
     * Determina si el usuario puede restaurar un aprendiz eliminado.
     */
    public function restore(User $user, Aprendiz $aprendiz): bool
    {
        return $user->can('CREAR APRENDIZ') && !$user->hasRole('INSTRUCTOR');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un aprendiz.
     */
    public function forceDelete(User $user, Aprendiz $aprendiz): bool
    {
        return $user->can('ELIMINAR APRENDIZ') && !$user->hasRole('INSTRUCTOR');
    }

    /**
     * Verifica si el aprendiz pertenece a una ficha asignada al instructor.
     *
     * @param User $user
     * @param Aprendiz $aprendiz
     * @return bool
     */
    private function aprendizPerteneceAFichaDeInstructor(User $user, Aprendiz $aprendiz): bool
    {
        // Obtener el instructor asociado al usuario
        $instructor = $user->persona?->instructor;

        if (!$instructor) {
            return false;
        }

        // Verificar si el aprendiz está en la ficha principal del instructor
        if ($aprendiz->ficha_caracterizacion_id) {
            $fichaExiste = $instructor->instructorFichas()
                ->where('ficha_id', $aprendiz->ficha_caracterizacion_id)
                ->exists();

            if ($fichaExiste) {
                return true;
            }
        }

        // Verificar si el aprendiz está en alguna de las fichas adicionales del instructor
        $fichasInstructor = $instructor->instructorFichas()->pluck('ficha_id');
        
        $perteneceAFicha = $aprendiz->fichasCaracterizacion()
            ->whereIn('fichas_caracterizacion.id', $fichasInstructor)
            ->exists();

        return $perteneceAFicha;
    }

    /**
     * Before hook - ejecutado antes de cualquier método de autorización.
     * Los super administradores tienen acceso total.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Super administradores tienen acceso total
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        return null; // Continuar con las verificaciones normales
    }
}


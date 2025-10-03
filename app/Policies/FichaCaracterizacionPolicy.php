<?php

namespace App\Policies;

use App\Models\FichaCaracterizacion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FichaCaracterizacionPolicy
{
    /**
     * Determine whether the user can view any models.
     * Los instructores solo ven fichas asignadas a ellos.
     */
    public function viewAny(User $user): bool
    {
        // Verificar permiso básico
        if (!$user->can('VER PROGRAMA DE CARACTERIZACION')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Los instructores pueden ver fichas si tienen el permiso
        if ($user->hasRole('INSTRUCTOR')) {
            return true; // Se verificará en el controlador qué fichas específicas
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Los instructores solo pueden ver fichas asignadas a ellos.
     */
    public function view(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        // Verificar permiso básico
        if (!$user->can('VER PROGRAMA DE CARACTERIZACION')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Si el usuario es instructor, verificar que la ficha esté asignada a él
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->fichaAsignadaAInstructor($user, $fichaCaracterizacion);
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR PROGRAMA DE CARACTERIZACION');
    }

    /**
     * Determine whether the user can update the model.
     * Los instructores solo pueden actualizar fichas asignadas a ellos.
     */
    public function update(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        // Verificar permiso básico
        if (!$user->can('EDITAR PROGRAMA DE CARACTERIZACION')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Si el usuario es instructor, verificar que la ficha esté asignada a él
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->fichaAsignadaAInstructor($user, $fichaCaracterizacion);
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     * Solo administradores pueden eliminar fichas.
     */
    public function delete(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        // Verificar permiso básico
        if (!$user->can('ELIMINAR PROGRAMA DE CARACTERIZACION')) {
            return false;
        }

        // Los instructores no pueden eliminar fichas
        if ($user->hasRole('INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden eliminar
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $user->can('CREAR PROGRAMA DE CARACTERIZACION') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $user->can('ELIMINAR PROGRAMA DE CARACTERIZACION') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can search fichas.
     */
    public function search(User $user): bool
    {
        return $user->can('VER PROGRAMA DE CARACTERIZACION');
    }

    /**
     * Determine whether the user can change ficha status.
     * Los instructores solo pueden cambiar estado de fichas asignadas.
     */
    public function cambiarEstado(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        // Verificar permiso básico
        if (!$user->can('EDITAR PROGRAMA DE CARACTERIZACION')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Si el usuario es instructor, verificar que la ficha esté asignada a él
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->fichaAsignadaAInstructor($user, $fichaCaracterizacion);
        }

        return true;
    }

    /**
     * Determine whether the user can manage instructors for a ficha.
     * Solo administradores pueden gestionar instructores.
     */
    public function gestionarInstructores(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        // Verificar permiso básico
        if (!$user->can('EDITAR PROGRAMA DE CARACTERIZACION')) {
            return false;
        }

        // Solo super administradores y administradores pueden gestionar instructores
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can assign instructors to a ficha.
     */
    public function asignarInstructores(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->gestionarInstructores($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can unassign an instructor from a ficha.
     */
    public function desasignarInstructor(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->gestionarInstructores($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can manage formation days for a ficha.
     * Los instructores pueden gestionar días de fichas asignadas.
     */
    public function gestionarDiasFormacion(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        // Verificar permiso básico
        if (!$user->can('EDITAR PROGRAMA DE CARACTERIZACION')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Si el usuario es instructor, verificar que la ficha esté asignada a él
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->fichaAsignadaAInstructor($user, $fichaCaracterizacion);
        }

        return true;
    }

    /**
     * Determine whether the user can save formation days for a ficha.
     */
    public function guardarDiasFormacion(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->gestionarDiasFormacion($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can update a specific formation day.
     */
    public function actualizarDiaFormacion(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->gestionarDiasFormacion($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can delete a specific formation day.
     */
    public function eliminarDiaFormacion(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->gestionarDiasFormacion($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can view ficha statistics.
     */
    public function getEstadisticasFichas(User $user): bool
    {
        return $user->can('VER PROGRAMA DE CARACTERIZACION');
    }

    /**
     * Determine whether the user can view fichas by jornada.
     */
    public function getFichasCaracterizacionPorJornada(User $user): bool
    {
        return $user->can('VER PROGRAMA DE CARACTERIZACION');
    }

    /**
     * Determine whether the user can view fichas by programa.
     */
    public function getFichasCaracterizacionPorPrograma(User $user): bool
    {
        return $user->can('VER PROGRAMA DE CARACTERIZACION');
    }

    /**
     * Determine whether the user can view fichas by sede.
     */
    public function getFichasCaracterizacionPorSede(User $user): bool
    {
        return $user->can('VER PROGRAMA DE CARACTERIZACION');
    }

    /**
     * Determine whether the user can view fichas by instructor.
     */
    public function getFichasCaracterizacionPorInstructor(User $user): bool
    {
        return $user->can('VER PROGRAMA DE CARACTERIZACION');
    }

    /**
     * Determine whether the user can view apprentices count for a ficha.
     */
    public function getCantidadAprendicesPorFicha(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->view($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can view apprentices for a ficha.
     */
    public function getAprendicesPorFicha(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->view($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can validate ficha deletion.
     */
    public function validarEliminacionFicha(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->delete($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can generate ficha reports.
     */
    public function generarReporteFicha(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        return $this->view($user, $fichaCaracterizacion);
    }

    /**
     * Determine whether the user can generate general reports.
     */
    public function generarReporteGeneral(User $user): bool
    {
        return $user->can('VER PROGRAMA DE CARACTERIZACION') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can export fichas.
     */
    public function exportarFichas(User $user): bool
    {
        return $user->can('VER PROGRAMA DE CARACTERIZACION') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can download import template.
     */
    public function descargarPlantillaImportacion(User $user): bool
    {
        return $user->can('CREAR PROGRAMA DE CARACTERIZACION') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can import fichas.
     */
    public function importarFichas(User $user): bool
    {
        return $user->can('CREAR PROGRAMA DE CARACTERIZACION') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Verifica si la ficha está asignada al instructor.
     *
     * @param User $user
     * @param FichaCaracterizacion $fichaCaracterizacion
     * @return bool
     */
    private function fichaAsignadaAInstructor(User $user, FichaCaracterizacion $fichaCaracterizacion): bool
    {
        // Obtener el instructor asociado al usuario
        $instructor = $user->persona?->instructor;

        if (!$instructor) {
            return false;
        }

        // Verificar si la ficha está asignada directamente al instructor
        if ($fichaCaracterizacion->instructor_id === $instructor->id) {
            return true;
        }

        // Verificar si el instructor tiene fichas adicionales asignadas
        $fichasInstructor = $instructor->instructorFichas()
            ->where('ficha_id', $fichaCaracterizacion->id)
            ->exists();

        return $fichasInstructor;
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

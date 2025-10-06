<?php

namespace App\Policies;

use App\Models\User;
use App\Models\GuiasAprendizaje;
use App\Models\ResultadosAprendizaje;
use App\Models\Evidencias;
use Illuminate\Auth\Access\Response;

class GuiaAprendizajePolicy
{
    /**
     * Determine whether the user can view any models.
     * Los usuarios pueden ver guías según sus permisos y roles.
     */
    public function viewAny(User $user): bool
    {
        // Verificar permiso básico
        if (!$user->can('VER GUIA APRENDIZAJE')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Los instructores pueden ver guías de aprendizaje
        if ($user->hasRole('INSTRUCTOR')) {
            return true; // Se verificará en el controlador qué guías específicas
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Los usuarios pueden ver guías según sus permisos y contexto.
     */
    public function view(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Verificar permiso básico
        if (!$user->can('VER GUIA APRENDIZAJE')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Si el usuario es instructor, verificar permisos específicos
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->instructorPuedeVerGuia($user, $guiaAprendizaje);
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     * Solo usuarios con permisos específicos pueden crear guías.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR GUIA APRENDIZAJE') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN', 'INSTRUCTOR']);
    }

    /**
     * Determine whether the user can update the model.
     * Los usuarios pueden actualizar guías según sus permisos y contexto.
     */
    public function update(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Verificar permiso básico
        if (!$user->can('EDITAR GUIA APRENDIZAJE')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Si el usuario es instructor, verificar permisos específicos
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->instructorPuedeEditarGuia($user, $guiaAprendizaje);
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     * Solo administradores pueden eliminar guías.
     */
    public function delete(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Verificar permiso básico
        if (!$user->can('ELIMINAR GUIA APRENDIZAJE')) {
            return false;
        }

        // Los instructores no pueden eliminar guías
        if ($user->hasRole('INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden eliminar
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $user->can('CREAR GUIA APRENDIZAJE') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $user->can('ELIMINAR GUIA APRENDIZAJE') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can change guía status.
     * Solo usuarios con permisos específicos pueden cambiar estado.
     */
    public function cambiarEstado(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Verificar permiso específico
        if (!$user->can('EDITAR GUIA APRENDIZAJE')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Si el usuario es instructor, verificar permisos específicos
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->instructorPuedeEditarGuia($user, $guiaAprendizaje);
        }

        return true;
    }

    /**
     * Determine whether the user can manage resultados de aprendizaje.
     * Los usuarios pueden gestionar resultados según sus permisos.
     */
    public function gestionarResultados(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Verificar permiso específico
        if (!$user->can('EDITAR GUIA APRENDIZAJE')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Si el usuario es instructor, verificar permisos específicos
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->instructorPuedeEditarGuia($user, $guiaAprendizaje);
        }

        return true;
    }

    /**
     * Determine whether the user can associate resultado to guía.
     */
    public function asociarResultado(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarResultados($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can remove resultado from guía.
     */
    public function desasociarResultado(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarResultados($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can manage evidencias/actividades.
     * Los usuarios pueden gestionar evidencias según sus permisos.
     */
    public function gestionarEvidencias(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Verificar permiso específico
        if (!$user->can('EDITAR GUIA APRENDIZAJE')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Si el usuario es instructor, verificar permisos específicos
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->instructorPuedeEditarGuia($user, $guiaAprendizaje);
        }

        return true;
    }

    /**
     * Determine whether the user can associate evidencia to guía.
     */
    public function asociarEvidencia(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarEvidencias($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can remove evidencia from guía.
     */
    public function desasociarEvidencia(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarEvidencias($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can view guía progress report.
     */
    public function reporteProgreso(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->view($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can view guías statistics.
     */
    public function estadisticas(User $user): bool
    {
        return $user->can('VER GUIA APRENDIZAJE');
    }

    /**
     * Determine whether the user can export guía to PDF.
     */
    public function exportarPdf(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->view($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can export guías to Excel.
     */
    public function exportarExcel(User $user): bool
    {
        return $user->can('VER GUIA APRENDIZAJE') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can duplicate guía.
     */
    public function duplicar(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->create($user);
    }

    /**
     * Determine whether the user can create guía from template.
     */
    public function crearDesdePlantilla(User $user): bool
    {
        return $this->create($user);
    }

    /**
     * Determine whether the user can store guía from template.
     */
    public function storeDesdePlantilla(User $user): bool
    {
        return $this->create($user);
    }

    /**
     * Determine whether the user can view API guías.
     */
    public function apiIndex(User $user): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can view API guía.
     */
    public function apiShow(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->view($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can manage guía templates.
     * Solo administradores pueden gestionar plantillas.
     */
    public function gestionarPlantillas(User $user): bool
    {
        return $user->can('CREAR GUIA APRENDIZAJE') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can create template.
     */
    public function crearPlantilla(User $user): bool
    {
        return $this->gestionarPlantillas($user);
    }

    /**
     * Determine whether the user can update template.
     */
    public function actualizarPlantilla(User $user): bool
    {
        return $this->gestionarPlantillas($user);
    }

    /**
     * Determine whether the user can delete template.
     */
    public function eliminarPlantilla(User $user): bool
    {
        return $this->gestionarPlantillas($user);
    }

    /**
     * Determine whether the user can view guía analytics.
     */
    public function analiticas(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->view($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can view guía performance.
     */
    public function rendimiento(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->view($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can manage guía comments.
     * Los usuarios pueden gestionar comentarios según sus permisos.
     */
    public function gestionarComentarios(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Verificar permiso específico
        if (!$user->can('EDITAR GUIA APRENDIZAJE')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Si el usuario es instructor, verificar permisos específicos
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->instructorPuedeEditarGuia($user, $guiaAprendizaje);
        }

        return true;
    }

    /**
     * Determine whether the user can add comment.
     */
    public function agregarComentario(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarComentarios($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can edit comment.
     */
    public function editarComentario(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarComentarios($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can delete comment.
     */
    public function eliminarComentario(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarComentarios($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can manage guía versions.
     * Solo administradores pueden gestionar versiones.
     */
    public function gestionarVersiones(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $user->can('EDITAR GUIA APRENDIZAJE') && 
               $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can create version.
     */
    public function crearVersion(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarVersiones($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can restore version.
     */
    public function restaurarVersion(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarVersiones($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can view version history.
     */
    public function historialVersiones(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->view($user, $guiaAprendizaje);
    }

    /**
     * Verifica si el instructor puede ver la guía de aprendizaje.
     * Los instructores pueden ver guías relacionadas con sus competencias.
     *
     * @param User $user
     * @param GuiasAprendizaje $guiaAprendizaje
     * @return bool
     */
    private function instructorPuedeVerGuia(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Obtener el instructor asociado al usuario
        $instructor = $user->persona?->instructor;

        if (!$instructor) {
            return false;
        }

        // Verificar si la guía está activa
        if ($guiaAprendizaje->status != 1) {
            return false;
        }

        // Los instructores pueden ver todas las guías activas
        // En el futuro se puede implementar lógica más específica
        // basada en competencias o regionales
        return true;
    }

    /**
     * Verifica si el instructor puede editar la guía de aprendizaje.
     * Los instructores pueden editar guías que han creado o que están asignadas.
     *
     * @param User $user
     * @param GuiasAprendizaje $guiaAprendizaje
     * @return bool
     */
    private function instructorPuedeEditarGuia(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Obtener el instructor asociado al usuario
        $instructor = $user->persona?->instructor;

        if (!$instructor) {
            return false;
        }

        // Verificar si el instructor creó la guía
        if ($guiaAprendizaje->user_create_id === $user->id) {
            return true;
        }

        // En el futuro se puede implementar lógica para verificar
        // si el instructor está asignado a la guía o tiene competencias relacionadas
        return false;
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

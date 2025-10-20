<?php

namespace App\Policies;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InstructorPolicy
{
    /**
     * Determine whether the user can view any models.
     * Los instructores pueden ver otros instructores de su regional.
     */
    public function viewAny(User $user): bool
    {
        // Verificar permiso básico
        if (!$user->can('VER INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Los instructores pueden ver otros instructores
        if ($user->hasRole('INSTRUCTOR')) {
            return true; // Se verificará en el controlador qué instructores específicos
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Los instructores pueden ver su propio perfil y otros de su regional.
     */
    public function view(User $user, Instructor $instructor): bool
    {
        // Verificar permiso básico
        if (!$user->can('VER INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, verificar permisos específicos
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->instructorPuedeVer($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     * Solo administradores pueden crear instructores.
     */
    public function create(User $user): bool
    {
        return $user->can('CREAR INSTRUCTOR') && 
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can update the model.
     * Los instructores solo pueden actualizar su propio perfil.
     */
    public function update(User $user, Instructor $instructor): bool
    {
        // Verificar permiso básico
        if (!$user->can('EDITAR INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede editar su propio perfil
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     * Solo administradores pueden eliminar instructores.
     */
    public function delete(User $user, Instructor $instructor): bool
    {
        // Verificar permiso básico
        if (!$user->can('ELIMINAR INSTRUCTOR')) {
            return false;
        }

        // Los instructores no pueden eliminar otros instructores
        if ($user->hasRole('INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden eliminar
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Instructor $instructor): bool
    {
        return $user->can('CREAR INSTRUCTOR') && 
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Instructor $instructor): bool
    {
        return $user->can('ELIMINAR INSTRUCTOR') && 
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can change instructor status.
     * Solo administradores pueden cambiar estado de instructores.
     */
    public function cambiarEstado(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('CAMBIAR ESTADO INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden cambiar estado
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can change user status.
     * Solo administradores pueden cambiar estado de usuarios.
     */
    public function cambiarEstadoUsuario(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('CAMBIAR ESTADO USUARIO')) {
            return false;
        }

        // Solo super administradores y administradores pueden cambiar estado
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can manage instructor specialties.
     * Los instructores pueden gestionar sus propias especialidades.
     */
    public function gestionarEspecialidades(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('GESTIONAR ESPECIALIDADES INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede gestionar sus propias especialidades
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can assign specialties to instructor.
     */
    public function asignarEspecialidad(User $user, Instructor $instructor): bool
    {
        return $this->gestionarEspecialidades($user, $instructor);
    }

    /**
     * Determine whether the user can remove specialty from instructor.
     */
    public function removerEspecialidad(User $user, Instructor $instructor): bool
    {
        return $this->gestionarEspecialidades($user, $instructor);
    }

    /**
     * Determine whether the user can view assigned fichas.
     * Los instructores pueden ver sus propias fichas asignadas.
     */
    public function fichasAsignadas(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('VER FICHAS INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede ver sus propias fichas
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can view active fichas.
     */
    public function fichasActivas(User $user, Instructor $instructor): bool
    {
        return $this->fichasAsignadas($user, $instructor);
    }

    /**
     * Determine whether the user can view fichas history.
     */
    public function historialFichas(User $user, Instructor $instructor): bool
    {
        return $this->fichasAsignadas($user, $instructor);
    }

    /**
     * Determine whether the user can assign fichas to instructor.
     * Solo administradores pueden asignar fichas.
     */
    public function asignarFicha(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('ASIGNAR FICHA INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden asignar fichas
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can unassign fichas from instructor.
     */
    public function desasignarFicha(User $user, Instructor $instructor): bool
    {
        return $this->asignarFicha($user, $instructor);
    }

    /**
     * Determine whether the user can import instructors.
     * Solo administradores pueden importar instructores.
     */
    public function importarInstructores(User $user): bool
    {
        return $user->can('CREAR INSTRUCTOR') && 
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can download import template.
     */
    public function descargarPlantillaCSV(User $user): bool
    {
        return $this->importarInstructores($user);
    }

    /**
     * Determine whether the user can view instructor reports.
     */
    public function reportePorRegional(User $user): bool
    {
        return $user->can('VER INSTRUCTOR') && 
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can view instructor reports by status.
     */
    public function reportePorEstado(User $user): bool
    {
        return $this->reportePorRegional($user);
    }

    /**
     * Determine whether the user can view instructor statistics.
     */
    public function estadisticas(User $user): bool
    {
        return $user->can('VER INSTRUCTOR');
    }

    /**
     * Determine whether the user can export instructors.
     */
    public function exportar(User $user): bool
    {
        return $user->can('VER INSTRUCTOR') && 
               $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can view available instructors.
     */
    public function disponibles(User $user): bool
    {
        return $user->can('VER INSTRUCTOR');
    }

    /**
     * Determine whether the user can view busy instructors.
     */
    public function ocupados(User $user): bool
    {
        return $user->can('VER INSTRUCTOR');
    }

    /**
     * Determine whether the user can check instructor availability.
     */
    public function verificarDisponibilidad(User $user, Instructor $instructor): bool
    {
        return $this->view($user, $instructor);
    }

    /**
     * Determine whether the user can manage instructor schedules.
     * Los instructores pueden gestionar sus propios horarios.
     */
    public function gestionarHorarios(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('GESTIONAR HORARIOS INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede gestionar sus propios horarios
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can update instructor schedules.
     */
    public function actualizarHorarios(User $user, Instructor $instructor): bool
    {
        return $this->gestionarHorarios($user, $instructor);
    }

    /**
     * Determine whether the user can manage instructor competencies.
     * Los instructores pueden gestionar sus propias competencias.
     */
    public function gestionarCompetencias(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('GESTIONAR COMPETENCIAS INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede gestionar sus propias competencias
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can assign competencies to instructor.
     */
    public function asignarCompetencia(User $user, Instructor $instructor): bool
    {
        return $this->gestionarCompetencias($user, $instructor);
    }

    /**
     * Determine whether the user can remove competency from instructor.
     */
    public function removerCompetencia(User $user, Instructor $instructor): bool
    {
        return $this->gestionarCompetencias($user, $instructor);
    }

    /**
     * Determine whether the user can view professional profile.
     * Los instructores pueden ver su propio perfil profesional.
     */
    public function perfilProfesional(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('VER PERFIL PROFESIONAL INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede ver su propio perfil
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can edit professional profile.
     */
    public function editarPerfilProfesional(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('EDITAR PERFIL PROFESIONAL INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede editar su propio perfil
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can update professional profile.
     */
    public function actualizarPerfilProfesional(User $user, Instructor $instructor): bool
    {
        return $this->editarPerfilProfesional($user, $instructor);
    }

    /**
     * Determine whether the user can manage instructor documents.
     * Los instructores pueden gestionar sus propios documentos.
     */
    public function gestionarDocumentos(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('GESTIONAR DOCUMENTOS INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede gestionar sus propios documentos
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can upload documents.
     */
    public function subirDocumento(User $user, Instructor $instructor): bool
    {
        return $this->gestionarDocumentos($user, $instructor);
    }

    /**
     * Determine whether the user can download documents.
     */
    public function descargarDocumento(User $user, Instructor $instructor): bool
    {
        return $this->gestionarDocumentos($user, $instructor);
    }

    /**
     * Determine whether the user can view assigned fichas.
     */
    public function verFichasAsignadas(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('VER FICHAS ASIGNADAS')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede ver sus propias fichas
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can delete documents.
     */
    public function eliminarDocumento(User $user, Instructor $instructor): bool
    {
        return $this->gestionarDocumentos($user, $instructor);
    }

    /**
     * Determine whether the user can manage instructor evaluations.
     * Solo administradores pueden gestionar evaluaciones.
     */
    public function gestionarEvaluaciones(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('GESTIONAR EVALUACIONES INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden gestionar evaluaciones
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can create evaluations.
     */
    public function crearEvaluacion(User $user, Instructor $instructor): bool
    {
        return $this->gestionarEvaluaciones($user, $instructor);
    }

    /**
     * Determine whether the user can save evaluations.
     */
    public function guardarEvaluacion(User $user, Instructor $instructor): bool
    {
        return $this->gestionarEvaluaciones($user, $instructor);
    }

    /**
     * Determine whether the user can manage instructor notifications.
     * Los instructores pueden gestionar sus propias notificaciones.
     */
    public function gestionarNotificaciones(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('GESTIONAR NOTIFICACIONES INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede gestionar sus propias notificaciones
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can mark notification as read.
     */
    public function marcarNotificacionLeida(User $user, Instructor $instructor): bool
    {
        return $this->gestionarNotificaciones($user, $instructor);
    }

    /**
     * Determine whether the user can mark all notifications as read.
     */
    public function marcarTodasLeidas(User $user, Instructor $instructor): bool
    {
        return $this->gestionarNotificaciones($user, $instructor);
    }

    /**
     * Determine whether the user can change instructor password.
     * Los instructores pueden cambiar su propia contraseña.
     */
    public function cambiarContraseña(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('CAMBIAR CONTRASEÑA INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede cambiar su propia contraseña
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can update password.
     */
    public function actualizarContraseña(User $user, Instructor $instructor): bool
    {
        return $this->cambiarContraseña($user, $instructor);
    }

    /**
     * Determine whether the user can reset password.
     * Solo administradores pueden resetear contraseñas.
     */
    public function resetearContraseña(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('RESETEAR CONTRASEÑA INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden resetear contraseñas
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can manage instructor sessions.
     * Los instructores pueden gestionar sus propias sesiones.
     */
    public function gestionarSesiones(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('GESTIONAR SESIONES INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede gestionar sus propias sesiones
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can view active sessions.
     */
    public function sesionesActivas(User $user, Instructor $instructor): bool
    {
        return $this->gestionarSesiones($user, $instructor);
    }

    /**
     * Determine whether the user can view session history.
     */
    public function historialSesiones(User $user, Instructor $instructor): bool
    {
        return $this->gestionarSesiones($user, $instructor);
    }

    /**
     * Determine whether the user can close session.
     */
    public function cerrarSesion(User $user, Instructor $instructor): bool
    {
        return $this->gestionarSesiones($user, $instructor);
    }

    /**
     * Determine whether the user can manage instructor backups.
     * Solo administradores pueden gestionar backups.
     */
    public function gestionarBackups(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (!$user->can('GESTIONAR BACKUPS INSTRUCTOR')) {
            return false;
        }

        // Solo super administradores y administradores pueden gestionar backups
        return $user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR']);
    }

    /**
     * Determine whether the user can create backup.
     */
    public function crearBackup(User $user, Instructor $instructor): bool
    {
        return $this->gestionarBackups($user, $instructor);
    }

    /**
     * Determine whether the user can restore backup.
     */
    public function restaurarBackup(User $user, Instructor $instructor): bool
    {
        return $this->gestionarBackups($user, $instructor);
    }

    /**
     * Determine whether the user can view backups.
     */
    public function backups(User $user, Instructor $instructor): bool
    {
        return $this->gestionarBackups($user, $instructor);
    }

    /**
     * Verifica si el usuario es el mismo instructor.
     *
     * @param User $user
     * @param Instructor $instructor
     * @return bool
     */
    private function esElMismoInstructor(User $user, Instructor $instructor): bool
    {
        // Obtener el instructor asociado al usuario
        $instructorUsuario = $user->persona?->instructor;

        if (!$instructorUsuario) {
            return false;
        }

        return $instructorUsuario->id === $instructor->id;
    }

    /**
     * Verifica si el instructor puede ver otro instructor.
     * Los instructores pueden ver otros instructores de su regional.
     *
     * @param User $user
     * @param Instructor $instructor
     * @return bool
     */
    private function instructorPuedeVer(User $user, Instructor $instructor): bool
    {
        // Obtener el instructor asociado al usuario
        $instructorUsuario = $user->persona?->instructor;

        if (!$instructorUsuario) {
            return false;
        }

        // Puede ver su propio perfil
        if ($instructorUsuario->id === $instructor->id) {
            return true;
        }

        // Puede ver otros instructores de su regional
        return $instructorUsuario->regional_id === $instructor->regional_id;
    }

    /**
     * Before hook - ejecutado antes de cualquier método de autorización.
     * Los super administradores tienen acceso total.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Super administradores tienen acceso total
        if ($user->hasRole('SUPER ADMINISTRADOR')) {
            return true;
        }

        return null; // Continuar con las verificaciones normales
    }
}
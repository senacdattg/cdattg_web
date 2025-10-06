<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\PersonaController;

/*
|--------------------------------------------------------------------------
| Rutas para Gestión de Instructores
|--------------------------------------------------------------------------
|
| Este archivo contiene todas las rutas relacionadas con la gestión
| de instructores del sistema SENA. Incluye operaciones CRUD completas
| y funcionalidades específicas para instructores.
|
*/

// Rutas principales del recurso Instructor
Route::resource('instructor', InstructorController::class)->names([
    'index' => 'instructor.index',
    'create' => 'instructor.create',
    'store' => 'instructor.store',
    'show' => 'instructor.show',
    'edit' => 'instructor.edit',
    'update' => 'instructor.update',
    'destroy' => 'instructor.destroy'
]);

// Ruta para búsqueda avanzada con AJAX
Route::get('/instructor/search', [InstructorController::class, 'search'])
    ->name('instructor.search')
    ->middleware(['auth', 'can:VER INSTRUCTOR']);

// Ruta temporal para debuggear create
Route::get('/instructor/create-debug', [InstructorController::class, 'create'])
    ->name('instructor.create.debug')
    ->middleware('auth');

// Ruta temporal para debuggear index
Route::get('/instructor/index-debug', [InstructorController::class, 'index'])
    ->name('instructor.index.debug')
    ->middleware('auth');

// Rutas específicas para instructores con middleware de autenticación
Route::middleware(['auth'])->group(function () {
    
    // Rutas para gestión de estado del instructor
    Route::prefix('instructor')->group(function () {
        
        // Cambiar estado del instructor (activar/desactivar)
        Route::put('/{instructor}/cambiar-estado', [InstructorController::class, 'cambiarEstado'])
            ->name('instructor.cambiarEstado')
            ->middleware('can:CAMBIAR ESTADO INSTRUCTOR');
            
        // Cambiar estado del usuario asociado al instructor
        Route::put('/{instructor}/cambiar-estado-usuario', [InstructorController::class, 'cambiarEstadoUsuario'])
            ->name('instructor.cambiarEstadoUsuario')
            ->middleware('can:EDITAR INSTRUCTOR');
    });
    
    // Rutas para gestión de especialidades y competencias
    Route::prefix('instructor')->group(function () {
        
        // Ver especialidades del instructor
        Route::get('/{instructor}/especialidades', [InstructorController::class, 'especialidades'])
            ->name('instructor.especialidades')
            ->middleware('can:GESTIONAR ESPECIALIDADES INSTRUCTOR');
            
        // Gestionar especialidades del instructor
        Route::get('/{instructor}/gestionar-especialidades', [InstructorController::class, 'gestionarEspecialidades'])
            ->name('instructor.gestionarEspecialidades')
            ->middleware('can:GESTIONAR ESPECIALIDADES INSTRUCTOR');
            
        // Asignar especialidad al instructor
        Route::post('/{instructor}/asignar-especialidad', [InstructorController::class, 'asignarEspecialidad'])
            ->name('instructor.asignarEspecialidad')
            ->middleware('can:GESTIONAR ESPECIALIDADES INSTRUCTOR');
            
        // Remover especialidad del instructor
        Route::delete('/{instructor}/remover-especialidad', [InstructorController::class, 'removerEspecialidad'])
            ->name('instructor.removerEspecialidad')
            ->middleware('can:GESTIONAR ESPECIALIDADES INSTRUCTOR');
    });
    
    // Rutas para gestión de fichas asignadas
    Route::prefix('instructor')->group(function () {
        
        // Ver fichas asignadas al instructor (específico)
        Route::get('/{instructor}/fichas-asignadas', [InstructorController::class, 'fichasAsignadas'])
            ->name('instructor.fichasAsignadas')
            ->middleware('can:VER FICHAS ASIGNADAS');
            
        // Ver fichas asignadas del instructor autenticado
        Route::get('/mis-fichas-asignadas', [InstructorController::class, 'fichasAsignadas'])
            ->name('instructor.misFichasAsignadas')
            ->middleware('can:VER FICHAS ASIGNADAS');
            
        // Ver fichas activas del instructor
        Route::get('/{instructor}/fichas-activas', [InstructorController::class, 'fichasActivas'])
            ->name('instructor.fichasActivas')
            ->middleware('can:VER INSTRUCTOR');
            
        // Ver historial de fichas del instructor
        Route::get('/{instructor}/historial-fichas', [InstructorController::class, 'historialFichas'])
            ->name('instructor.historialFichas')
            ->middleware('can:VER INSTRUCTOR');
            
        // Asignar ficha al instructor
        Route::post('/{instructor}/asignar-ficha', [InstructorController::class, 'asignarFicha'])
            ->name('instructor.asignarFicha')
            ->middleware('can:EDITAR INSTRUCTOR');
            
        // Desasignar ficha del instructor
        Route::delete('/{instructor}/desasignar-ficha/{ficha}', [InstructorController::class, 'desasignarFicha'])
            ->name('instructor.desasignarFicha')
            ->middleware('can:EDITAR INSTRUCTOR');
    });
    
    // Rutas para importación masiva de instructores
    Route::prefix('instructor')->group(function () {
        
        // Mostrar formulario de importación CSV
        Route::get('/importar-csv', [InstructorController::class, 'createImportarCSV'])
            ->name('instructor.createImportarCSV')
            ->middleware('can:CREAR INSTRUCTOR');
            
        // Procesar importación CSV
        Route::post('/importar-csv', [InstructorController::class, 'storeImportarCSV'])
            ->name('instructor.storeImportarCSV')
            ->middleware('can:CREAR INSTRUCTOR');
            
        // Descargar plantilla CSV
        Route::get('/descargar-plantilla-csv', [InstructorController::class, 'descargarPlantillaCSV'])
            ->name('instructor.descargarPlantillaCSV')
            ->middleware('can:CREAR INSTRUCTOR');
    });
    
    // Rutas para reportes y estadísticas
    Route::prefix('instructor')->group(function () {
        
        // Reporte de instructores por regional
        Route::get('/reporte/por-regional', [InstructorController::class, 'reportePorRegional'])
            ->name('instructor.reportePorRegional')
            ->middleware('can:VER INSTRUCTOR');
            
        // Reporte de instructores activos/inactivos
        Route::get('/reporte/por-estado', [InstructorController::class, 'reportePorEstado'])
            ->name('instructor.reportePorEstado')
            ->middleware('can:VER INSTRUCTOR');
            
        // Estadísticas generales de instructores
        Route::get('/estadisticas', [InstructorController::class, 'estadisticas'])
            ->name('instructor.estadisticas')
            ->middleware('can:VER INSTRUCTOR');
            
        // Exportar lista de instructores
        Route::get('/exportar', [InstructorController::class, 'exportar'])
            ->name('instructor.exportar')
            ->middleware('can:VER INSTRUCTOR');
    });
    
    // Rutas para gestión de disponibilidad
    Route::prefix('instructor')->group(function () {
        
        // Ver instructores disponibles
        Route::get('/disponibles', [InstructorController::class, 'disponibles'])
            ->name('instructor.disponibles')
            ->middleware('can:VER INSTRUCTOR');
            
        // Ver instructores ocupados
        Route::get('/ocupados', [InstructorController::class, 'ocupados'])
            ->name('instructor.ocupados')
            ->middleware('can:VER INSTRUCTOR');
            
        // Verificar disponibilidad de instructor
        Route::get('/{instructor}/verificar-disponibilidad', [InstructorController::class, 'verificarDisponibilidad'])
            ->name('instructor.verificarDisponibilidad')
            ->middleware('can:VER INSTRUCTOR');
            
        // Obtener instructores disponibles para una ficha
        Route::get('/disponibles-para-ficha', [InstructorController::class, 'instructoresDisponibles'])
            ->name('instructor.instructoresDisponibles')
            ->middleware('can:VER INSTRUCTOR');
            
        // Validar reglas SENA para asignación
        Route::get('/{instructor}/validar-reglas-sena', [InstructorController::class, 'validarReglasSENA'])
            ->name('instructor.validarReglasSENA')
            ->middleware('can:VER INSTRUCTOR');
            
        // Estadísticas de carga de trabajo
        Route::get('/estadisticas-carga-trabajo', [InstructorController::class, 'estadisticasCargaTrabajo'])
            ->name('instructor.estadisticasCargaTrabajo')
            ->middleware('can:VER INSTRUCTOR');
            
        // Asignar ficha con validaciones
        Route::post('/{instructor}/asignar-ficha', [InstructorController::class, 'asignarFicha'])
            ->name('instructor.asignarFicha')
            ->middleware('can:EDITAR INSTRUCTOR');
            
        // Desasignar ficha
        Route::delete('/{instructor}/desasignar-ficha/{ficha}', [InstructorController::class, 'desasignarFicha'])
            ->name('instructor.desasignarFicha')
            ->middleware('can:EDITAR INSTRUCTOR');
            
        // Dashboard específico para instructores
        Route::get('/dashboard', [InstructorController::class, 'dashboard'])
            ->name('instructor.dashboard');
            // ->middleware('can:VER INSTRUCTOR'); // Temporalmente comentado para debuggear
    });
    
    // Rutas para gestión de horarios
    Route::prefix('instructor')->group(function () {
        
        // Ver horarios del instructor
        Route::get('/{instructor}/horarios', [InstructorController::class, 'horarios'])
            ->name('instructor.horarios')
            ->middleware('can:VER INSTRUCTOR');
            
        // Gestionar horarios del instructor
        Route::get('/{instructor}/gestionar-horarios', [InstructorController::class, 'gestionarHorarios'])
            ->name('instructor.gestionarHorarios')
            ->middleware('can:EDITAR INSTRUCTOR');
            
        // Actualizar horarios del instructor
        Route::put('/{instructor}/actualizar-horarios', [InstructorController::class, 'actualizarHorarios'])
            ->name('instructor.actualizarHorarios')
            ->middleware('can:EDITAR INSTRUCTOR');
    });
    
    // Rutas para gestión de competencias
    Route::prefix('instructor')->group(function () {
        
        // Ver competencias del instructor
        Route::get('/{instructor}/competencias', [InstructorController::class, 'competencias'])
            ->name('instructor.competencias')
            ->middleware('can:VER INSTRUCTOR');
            
        // Gestionar competencias del instructor
        Route::get('/{instructor}/gestionar-competencias', [InstructorController::class, 'gestionarCompetencias'])
            ->name('instructor.gestionarCompetencias')
            ->middleware('can:EDITAR INSTRUCTOR');
            
        // Asignar competencia al instructor
        Route::post('/{instructor}/asignar-competencia', [InstructorController::class, 'asignarCompetencia'])
            ->name('instructor.asignarCompetencia')
            ->middleware('can:EDITAR INSTRUCTOR');
            
        // Remover competencia del instructor
        Route::delete('/{instructor}/remover-competencia/{competencia}', [InstructorController::class, 'removerCompetencia'])
            ->name('instructor.removerCompetencia')
            ->middleware('can:EDITAR INSTRUCTOR');
    });
    
    // Rutas para gestión de perfil profesional
    Route::prefix('instructor')->group(function () {
        
        // Ver perfil profesional del instructor
        Route::get('/{instructor}/perfil-profesional', [InstructorController::class, 'perfilProfesional'])
            ->name('instructor.perfilProfesional')
            ->middleware('can:VER INSTRUCTOR');
            
        // Editar perfil profesional del instructor
        Route::get('/{instructor}/editar-perfil-profesional', [InstructorController::class, 'editarPerfilProfesional'])
            ->name('instructor.editarPerfilProfesional')
            ->middleware('can:EDITAR INSTRUCTOR');
            
        // Actualizar perfil profesional del instructor
        Route::put('/{instructor}/actualizar-perfil-profesional', [InstructorController::class, 'actualizarPerfilProfesional'])
            ->name('instructor.actualizarPerfilProfesional')
            ->middleware('can:EDITAR INSTRUCTOR');
    });
    
    // Rutas para gestión de documentos
    Route::prefix('instructor')->group(function () {
        
        // Ver documentos del instructor
        Route::get('/{instructor}/documentos', [InstructorController::class, 'documentos'])
            ->name('instructor.documentos')
            ->middleware('can:VER INSTRUCTOR');
            
        // Subir documento del instructor
        Route::post('/{instructor}/subir-documento', [InstructorController::class, 'subirDocumento'])
            ->name('instructor.subirDocumento')
            ->middleware('can:EDITAR INSTRUCTOR');
            
        // Descargar documento del instructor
        Route::get('/{instructor}/descargar-documento/{documento}', [InstructorController::class, 'descargarDocumento'])
            ->name('instructor.descargarDocumento')
            ->middleware('can:VER INSTRUCTOR');
            
        // Eliminar documento del instructor
        Route::delete('/{instructor}/eliminar-documento/{documento}', [InstructorController::class, 'eliminarDocumento'])
            ->name('instructor.eliminarDocumento')
            ->middleware('can:EDITAR INSTRUCTOR');
    });
    
    // Rutas para gestión de evaluaciones
    Route::prefix('instructor')->group(function () {
        
        // Ver evaluaciones del instructor
        Route::get('/{instructor}/evaluaciones', [InstructorController::class, 'evaluaciones'])
            ->name('instructor.evaluaciones')
            ->middleware('can:VER INSTRUCTOR');
            
        // Crear evaluación del instructor
        Route::get('/{instructor}/crear-evaluacion', [InstructorController::class, 'crearEvaluacion'])
            ->name('instructor.crearEvaluacion')
            ->middleware('can:EDITAR INSTRUCTOR');
            
        // Guardar evaluación del instructor
        Route::post('/{instructor}/guardar-evaluacion', [InstructorController::class, 'guardarEvaluacion'])
            ->name('instructor.guardarEvaluacion')
            ->middleware('can:EDITAR INSTRUCTOR');
    });
    
    // Rutas para gestión de notificaciones
    Route::prefix('instructor')->group(function () {
        
        // Ver notificaciones del instructor
        Route::get('/{instructor}/notificaciones', [InstructorController::class, 'notificaciones'])
            ->name('instructor.notificaciones')
            ->middleware('can:VER INSTRUCTOR');
            
        // Marcar notificación como leída
        Route::put('/{instructor}/marcar-notificacion-leida/{notificacion}', [InstructorController::class, 'marcarNotificacionLeida'])
            ->name('instructor.marcarNotificacionLeida')
            ->middleware('can:EDITAR INSTRUCTOR');
            
        // Marcar todas las notificaciones como leídas
        Route::put('/{instructor}/marcar-todas-leidas', [InstructorController::class, 'marcarTodasLeidas'])
            ->name('instructor.marcarTodasLeidas')
            ->middleware('can:EDITAR INSTRUCTOR');
    });
    
    // Rutas para gestión de contraseñas
    Route::prefix('instructor')->group(function () {
        
        // Cambiar contraseña del instructor
        Route::get('/{instructor}/cambiar-contraseña', [InstructorController::class, 'cambiarContraseña'])
            ->name('instructor.cambiarContraseña')
            ->middleware('can:EDITAR INSTRUCTOR');
            
        // Actualizar contraseña del instructor
        Route::put('/{instructor}/actualizar-contraseña', [InstructorController::class, 'actualizarContraseña'])
            ->name('instructor.actualizarContraseña')
            ->middleware('can:EDITAR INSTRUCTOR');
            
        // Resetear contraseña del instructor
        Route::post('/{instructor}/resetear-contraseña', [InstructorController::class, 'resetearContraseña'])
            ->name('instructor.resetearContraseña')
            ->middleware('can:EDITAR INSTRUCTOR');
    });
    
    // Rutas para gestión de sesiones
    Route::prefix('instructor')->group(function () {
        
        // Ver sesiones activas del instructor
        Route::get('/{instructor}/sesiones-activas', [InstructorController::class, 'sesionesActivas'])
            ->name('instructor.sesionesActivas')
            ->middleware('can:VER INSTRUCTOR');
            
        // Ver historial de sesiones del instructor
        Route::get('/{instructor}/historial-sesiones', [InstructorController::class, 'historialSesiones'])
            ->name('instructor.historialSesiones')
            ->middleware('can:VER INSTRUCTOR');
            
        // Cerrar sesión del instructor
        Route::post('/{instructor}/cerrar-sesion', [InstructorController::class, 'cerrarSesion'])
            ->name('instructor.cerrarSesion')
            ->middleware('can:EDITAR INSTRUCTOR');
    });
    
    // Rutas para gestión de backup y restauración
    Route::prefix('instructor')->group(function () {
        
        // Crear backup del instructor
        Route::post('/{instructor}/crear-backup', [InstructorController::class, 'crearBackup'])
            ->name('instructor.crearBackup')
            ->middleware('can:EDITAR INSTRUCTOR');
            
        // Restaurar backup del instructor
        Route::post('/{instructor}/restaurar-backup', [InstructorController::class, 'restaurarBackup'])
            ->name('instructor.restaurarBackup')
            ->middleware('can:EDITAR INSTRUCTOR');
            
        // Ver backups del instructor
        Route::get('/{instructor}/backups', [InstructorController::class, 'backups'])
            ->name('instructor.backups')
            ->middleware('can:VER INSTRUCTOR');
    });
});

// Rutas para gestión de personas (relacionadas con instructores)
Route::middleware(['auth', 'can:EDITAR INSTRUCTOR'])->group(function () {
    
    // Cambiar estado de persona
    Route::put('/persona/{persona}/cambiarEstado', [PersonaController::class, 'cambiarEstadoPersona'])
        ->name('persona.cambiarEstadoPersona');
        
    // Cambiar estado de usuario
    Route::put('/persona/{persona}/cambiarEstadoUser', [PersonaController::class, 'cambiarEstadoUser'])
        ->name('persona.cambiarEstadoUser');
});

// Rutas para eliminación sin usuario (mantener compatibilidad)
Route::middleware(['auth', 'can:ELIMINAR INSTRUCTOR'])->group(function () {
    
    Route::get('/instructor/delete/{id}', [InstructorController::class, 'deleteWithoudUser'])
        ->name('instructor.deleteWithoudUser');
});

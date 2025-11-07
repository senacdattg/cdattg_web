<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FichaCaracterizacionController;

/*
|--------------------------------------------------------------------------
| Rutas para FichaCaracterizacion
|--------------------------------------------------------------------------
|
| Aquí se definen todas las rutas relacionadas con el manejo de fichas
| de caracterización, incluyendo CRUD completo y funcionalidades adicionales.
|
*/

// Rutas Resource para CRUD completo
Route::middleware(['auth'])->group(function () {
    
    // Rutas básicas de CRUD
    Route::resource('fichaCaracterizacion', FichaCaracterizacionController::class, [
        'names' => [
            'index' => 'fichaCaracterizacion.index',
            'create' => 'fichaCaracterizacion.create',
            'store' => 'fichaCaracterizacion.store',
            'show' => 'fichaCaracterizacion.show',
            'edit' => 'fichaCaracterizacion.edit',
            'update' => 'fichaCaracterizacion.update',
            'destroy' => 'fichaCaracterizacion.destroy',
        ]
    ]);

    // Rutas adicionales con middleware de permisos específicos
    
    // Búsqueda y filtros
    Route::middleware('can:VER FICHA CARACTERIZACION')->group(function () {
        Route::get('/fichaCaracterizacion/search', [FichaCaracterizacionController::class, 'search'])
            ->name('fichaCaracterizacion.search');
        
        Route::get('/fichaCaracterizacion/estadisticas', [FichaCaracterizacionController::class, 'getEstadisticasFichas'])
            ->name('fichaCaracterizacion.estadisticas');
    });

    // Gestión de estado
    Route::middleware('can:EDITAR FICHA CARACTERIZACION')->group(function () {
        Route::patch('/fichaCaracterizacion/{id}/cambiar-estado', [FichaCaracterizacionController::class, 'cambiarEstadoFicha'])
            ->name('fichaCaracterizacion.cambiarEstado');
    });

    // Validación de eliminación
    Route::middleware('can:ELIMINAR FICHA CARACTERIZACION')->group(function () {
        Route::get('/fichaCaracterizacion/{id}/validar-eliminacion', [FichaCaracterizacionController::class, 'validarEliminacionFicha'])
            ->name('fichaCaracterizacion.validarEliminacion');
    });

    // Gestión de instructores
    Route::middleware('can:EDITAR FICHA CARACTERIZACION')->group(function () {
        Route::get('/fichaCaracterizacion/{id}/instructores', [FichaCaracterizacionController::class, 'gestionarInstructores'])
            ->name('fichaCaracterizacion.gestionarInstructores');
        
        Route::get('/fichaCaracterizacion/{id}/instructores-disponibles', [FichaCaracterizacionController::class, 'obtenerInstructoresDisponiblesParaFicha'])
            ->name('fichaCaracterizacion.instructoresDisponibles');
        
        Route::post('/fichaCaracterizacion/{id}/instructores', [FichaCaracterizacionController::class, 'asignarInstructores'])
            ->name('fichaCaracterizacion.asignarInstructores');
        
        Route::delete('/fichaCaracterizacion/{id}/instructores/{instructorId}', [FichaCaracterizacionController::class, 'desasignarInstructor'])
            ->name('fichaCaracterizacion.desasignarInstructor');
    });

    // Gestión de días de formación (funcionalidad futura)
    Route::middleware('can:EDITAR FICHA CARACTERIZACION')->group(function () {
        Route::get('/fichaCaracterizacion/{id}/dias-formacion', [FichaCaracterizacionController::class, 'gestionarDiasFormacion'])
            ->name('fichaCaracterizacion.gestionarDiasFormacion');
        
        Route::post('/fichaCaracterizacion/{id}/dias-formacion', [FichaCaracterizacionController::class, 'guardarDiasFormacion'])
            ->name('fichaCaracterizacion.guardarDiasFormacion');
        
        Route::put('/fichaCaracterizacion/{id}/dias-formacion/{diaId}', [FichaCaracterizacionController::class, 'actualizarDiaFormacion'])
            ->name('fichaCaracterizacion.actualizarDiaFormacion');
        
        Route::delete('/fichaCaracterizacion/{id}/dias-formacion/{diaId}', [FichaCaracterizacionController::class, 'eliminarDiaFormacion'])
            ->name('fichaCaracterizacion.eliminarDiaFormacion');
    });

    // Rutas para consultas específicas
    Route::middleware('can:VER FICHA CARACTERIZACION')->group(function () {
        // Consultas por criterios específicos
        Route::get('/fichas-por-jornada/{jornadaId}', [FichaCaracterizacionController::class, 'getFichasCaracterizacionPorJornada'])
            ->name('fichaCaracterizacion.porJornada');
        
        Route::get('/fichas-por-programa/{programaId}', [FichaCaracterizacionController::class, 'getFichasCaracterizacionPorPrograma'])
            ->name('fichaCaracterizacion.porPrograma');
        
        Route::get('/fichas-por-sede/{sedeId}', [FichaCaracterizacionController::class, 'getFichasCaracterizacionPorSede'])
            ->name('fichaCaracterizacion.porSede');
        
        Route::get('/fichas-por-instructor/{instructorId}', [FichaCaracterizacionController::class, 'getFichasCaracterizacionPorInstructor'])
            ->name('fichaCaracterizacion.porInstructor');
        
        // Estadísticas específicas
        Route::get('/fichaCaracterizacion/{id}/cantidad-aprendices', [FichaCaracterizacionController::class, 'getCantidadAprendicesPorFicha'])
            ->name('fichaCaracterizacion.cantidadAprendices');
        
        Route::get('/fichaCaracterizacion/{id}/aprendices', [FichaCaracterizacionController::class, 'getAprendicesPorFicha'])
            ->name('fichaCaracterizacion.aprendices');
    });

    // Rutas para reportes (funcionalidad futura)
    Route::middleware('can:VER FICHA CARACTERIZACION')->group(function () {
        Route::get('/fichaCaracterizacion/{id}/reporte', [FichaCaracterizacionController::class, 'generarReporteFicha'])
            ->name('fichaCaracterizacion.reporte');
        
        Route::get('/fichas-reporte', [FichaCaracterizacionController::class, 'generarReporteGeneral'])
            ->name('fichaCaracterizacion.reporteGeneral');
    });

    // Rutas para importación/exportación (funcionalidad futura)
    Route::middleware(['can:CREAR FICHA CARACTERIZACION', 'can:EDITAR FICHA CARACTERIZACION'])->group(function () {
        Route::get('/fichas-exportar', [FichaCaracterizacionController::class, 'exportarFichas'])
            ->name('fichaCaracterizacion.exportar');
        
        Route::get('/fichas-plantilla-importacion', [FichaCaracterizacionController::class, 'descargarPlantillaImportacion'])
            ->name('fichaCaracterizacion.plantillaImportacion');
        
        Route::post('/fichas-importar', [FichaCaracterizacionController::class, 'importarFichas'])
            ->name('fichaCaracterizacion.importar');
    });

});

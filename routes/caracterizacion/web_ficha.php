<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FichaCaracterizacionController;

// Rutas para FichasCaracterizacionController
Route::resource('fichaCaracterizacion', FichaCaracterizacionController::class);
route::middleware('can:VER FICHA CARACTERIZACION')->group(function () {
    Route::get('/ficha/index', [FichaCaracterizacionController::class, 'index'])->name('ficha.index');
    Route::get('/ficha/search', [FichaCaracterizacionController::class, 'search'])->name('ficha.search');
    Route::post('/ficha/search-ajax', [FichaCaracterizacionController::class, 'search'])->name('ficha.search.ajax');
    Route::get('/fichaCaracterizacion/create', [FichaCaracterizacionController::class, 'create'])->name('fichaCaracterizacion.create');
    Route::post('/fichaCaracterizacion/store', [FichaCaracterizacionController::class, 'store'])->name('fichaCaracterizacion.store');
    Route::get('/fichaCaracterizacion/{id}/edit', [FichaCaracterizacionController::class, 'edit'])->name('ficha.edit');
    Route::post('/fichaCaracterizacion/{id}', [FichaCaracterizacionController::class, 'update'])->name('ficha.update');
    Route::delete('/fichaCaracterizacion/{id}', [FichaCaracterizacionController::class, 'destroy'])->name('ficha.destroy');
    
    // Rutas para validaciones de negocio
    Route::post('/ficha/validar', [FichaCaracterizacionController::class, 'validarFicha'])->name('ficha.validar');
    Route::post('/ficha/validar-ambiente', [FichaCaracterizacionController::class, 'validarDisponibilidadAmbiente'])->name('ficha.validar.ambiente');
    Route::post('/ficha/validar-instructor', [FichaCaracterizacionController::class, 'validarDisponibilidadInstructor'])->name('ficha.validar.instructor');
    Route::get('/ficha/{id}/validar-eliminacion', [FichaCaracterizacionController::class, 'validarEliminacionFicha'])->name('ficha.validar.eliminacion');
    Route::get('/ficha/{id}/validar-edicion', [FichaCaracterizacionController::class, 'validarEdicionFicha'])->name('ficha.validar.edicion');
    
    // Ruta para obtener ambientes por sede
    Route::get('/ficha/ambientes-por-sede/{sedeId}', [FichaCaracterizacionController::class, 'getAmbientesPorSede'])->name('ficha.ambientes.por.sede');
    
    // Rutas para gestión de instructores
    Route::get('/fichaCaracterizacion/{id}/gestionar-instructores', [FichaCaracterizacionController::class, 'gestionarInstructores'])->name('fichaCaracterizacion.gestionarInstructores');
    Route::post('/fichaCaracterizacion/{id}/asignar-instructores', [FichaCaracterizacionController::class, 'asignarInstructores'])->name('fichaCaracterizacion.asignarInstructores');
    Route::post('/fichaCaracterizacion/{id}/desasignar-instructores', [FichaCaracterizacionController::class, 'desasignarInstructores'])->name('fichaCaracterizacion.desasignarInstructores');
    
    // Rutas para gestión de días de formación de instructores en fichas
    Route::get('/fichaCaracterizacion/{fichaId}/instructor/{instructorFichaId}/gestionar-dias', [FichaCaracterizacionController::class, 'gestionarDiasInstructor'])->name('fichaCaracterizacion.instructor.gestionarDias');
    Route::post('/fichaCaracterizacion/{fichaId}/instructor/{instructorFichaId}/asignar-dias', [FichaCaracterizacionController::class, 'asignarDiasInstructor'])->name('fichaCaracterizacion.instructor.asignarDias');
    Route::get('/fichaCaracterizacion/{fichaId}/instructor/{instructorFichaId}/obtener-dias', [FichaCaracterizacionController::class, 'obtenerDiasInstructor'])->name('fichaCaracterizacion.instructor.obtenerDias');
    Route::delete('/fichaCaracterizacion/{fichaId}/instructor/{instructorFichaId}/eliminar-dias', [FichaCaracterizacionController::class, 'eliminarDiasInstructor'])->name('fichaCaracterizacion.instructor.eliminarDias');
    Route::post('/fichaCaracterizacion/{fichaId}/instructor/{instructorFichaId}/preview-fechas', [FichaCaracterizacionController::class, 'previewFechasInstructor'])->name('fichaCaracterizacion.instructor.previewFechas');
    
    // Rutas para gestión de días de formación
    Route::get('/fichaCaracterizacion/{id}/gestionar-dias-formacion', [FichaCaracterizacionController::class, 'gestionarDiasFormacion'])->name('fichaCaracterizacion.gestionarDiasFormacion');
    Route::post('/fichaCaracterizacion/{id}/agregar-dia-formacion', [FichaCaracterizacionController::class, 'agregarDiaFormacion'])->name('fichaCaracterizacion.agregarDiaFormacion');
    Route::delete('/fichaCaracterizacion/{id}/eliminar-dia-formacion/{diaId}', [FichaCaracterizacionController::class, 'eliminarDiaFormacion'])->name('fichaCaracterizacion.eliminarDiaFormacion');
    
    // Rutas para gestión de aprendices
    Route::get('/fichaCaracterizacion/{id}/gestionar-aprendices', [FichaCaracterizacionController::class, 'gestionarAprendices'])->name('fichaCaracterizacion.gestionarAprendices');
    Route::post('/fichaCaracterizacion/{id}/asignar-aprendices', [FichaCaracterizacionController::class, 'asignarAprendices'])->name('fichaCaracterizacion.asignarAprendices');
    Route::post('/fichaCaracterizacion/{id}/desasignar-aprendices', [FichaCaracterizacionController::class, 'desasignarAprendices'])->name('fichaCaracterizacion.desasignarAprendices');
});

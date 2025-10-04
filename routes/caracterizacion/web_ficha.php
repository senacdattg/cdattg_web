<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FichaCaracterizacionController;

// Rutas para FichasCaracterizacionController
Route::resource('fichaCaracterizacion', FichaCaracterizacionController::class);
route::middleware('can:VER PROGRAMA DE CARACTERIZACION')->group(function () {
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
});

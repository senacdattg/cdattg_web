<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgramaFormacionController;

/*
|--------------------------------------------------------------------------
| Rutas para Programa de Formación
|--------------------------------------------------------------------------
|
| Aquí se definen las rutas para el CRUD completo de programas de formación
| incluyendo búsqueda, cambio de estado y permisos específicos.
|
*/

// Rutas resource para CRUD completo
Route::resource('programa', ProgramaFormacionController::class)->except(['show']);

// Ruta adicional para mostrar detalles
Route::get('/programa/{programa}', [ProgramaFormacionController::class, 'show'])
    ->name('programa.show')
    ->middleware('can:programa.show');

// Rutas con middleware de permisos específicos
Route::middleware(['can:programa.index'])->group(function () {
    Route::get('/programa', [ProgramaFormacionController::class, 'index'])->name('programa.index');
});

Route::middleware(['can:programa.create'])->group(function () {
    Route::get('/programa/create', [ProgramaFormacionController::class, 'create'])->name('programa.create');
    Route::post('/programa', [ProgramaFormacionController::class, 'store'])->name('programa.store');
});

Route::middleware(['can:programa.edit'])->group(function () {
    Route::get('/programa/{programa}/edit', [ProgramaFormacionController::class, 'edit'])->name('programa.edit');
    Route::put('/programa/{programa}', [ProgramaFormacionController::class, 'update'])->name('programa.update');
    Route::patch('/programa/{programa}', [ProgramaFormacionController::class, 'update']);
});

Route::middleware(['can:programa.delete'])->group(function () {
    Route::delete('/programa/{programa}', [ProgramaFormacionController::class, 'destroy'])->name('programa.destroy');
});

// Ruta para buscar programas
Route::middleware(['can:programa.search'])->group(function () {
    Route::get('/programa-search', [ProgramaFormacionController::class, 'search'])->name('programa.search');
});

// Ruta para cambiar estado del programa
Route::middleware(['can:programa.edit'])->group(function () {
    Route::patch('/programa/{programa}/cambiar-estado', [ProgramaFormacionController::class, 'cambiarEstado'])
        ->name('programa.cambiarEstado');
});

// Rutas adicionales para funcionalidades específicas
Route::middleware(['can:programa.index'])->group(function () {
    // Ruta para obtener programas por red de conocimiento
    Route::get('/programas-por-red/{redConocimientoId}', [ProgramaFormacionController::class, 'getByRedConocimiento'])
        ->name('programa.byRedConocimiento');
    
    // Ruta para obtener programas por nivel de formación
    Route::get('/programas-por-nivel/{nivelFormacionId}', [ProgramaFormacionController::class, 'getByNivelFormacion'])
        ->name('programa.byNivelFormacion');
    
    // Ruta para obtener programas activos
    Route::get('/programas-activos', [ProgramaFormacionController::class, 'getActivos'])
        ->name('programa.activos');
});

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

// Ruta para mostrar detalles
Route::get('/programa/{programa}', [ProgramaFormacionController::class, 'show'])
    ->name('programa.show')
    ->middleware('can:VER PROGRAMA DE FORMACION');

// Rutas con middleware de permisos específicos
Route::middleware(['can:VER PROGRAMAS DE FORMACION'])->group(function () {
    Route::get('/programa', [ProgramaFormacionController::class, 'index'])->name('programa.index');
});

Route::middleware(['can:CREAR PROGRAMA DE FORMACION'])->group(function () {
    Route::get('/programa/create', [ProgramaFormacionController::class, 'create'])->name('programa.create');
    Route::post('/programa', [ProgramaFormacionController::class, 'store'])->name('programa.store');
});

Route::middleware(['can:EDITAR PROGRAMA DE FORMACION'])->group(function () {
    Route::get('/programa/{programa}/edit', [ProgramaFormacionController::class, 'edit'])->name('programa.edit');
    Route::put('/programa/{programa}', [ProgramaFormacionController::class, 'update'])->name('programa.update');
    Route::patch('/programa/{programa}', [ProgramaFormacionController::class, 'update']);
    Route::delete(
        '/programa/{programa}/competencias/{competencia}',
        [ProgramaFormacionController::class, 'detachCompetencia']
    )->name('programa.competencia.detach');
});

Route::middleware(['can:ELIMINAR PROGRAMA DE FORMACION'])->group(function () {
    Route::delete('/programa/{programa}', [ProgramaFormacionController::class, 'destroy'])->name('programa.destroy');
});

// Ruta para buscar programas
Route::middleware(['can:VER PROGRAMAS DE FORMACION'])->group(function () {
    Route::get('/programa-search', [ProgramaFormacionController::class, 'search'])->name('programa.search');
});

// Ruta para cambiar estado del programa
Route::middleware(['can:EDITAR PROGRAMA DE FORMACION'])->group(function () {
    Route::patch('/programa/{programa}/cambiar-estado', [ProgramaFormacionController::class, 'cambiarEstado'])
        ->name('programa.cambiarEstado');
});

// Rutas adicionales para funcionalidades específicas
Route::middleware(['can:VER PROGRAMAS DE FORMACION'])->group(function () {
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

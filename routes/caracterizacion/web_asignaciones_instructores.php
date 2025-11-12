<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsignacionInstructorController;

Route::prefix('asignaciones/instructores')->name('asignaciones.instructores.')->group(function () {
    Route::get('/', [AsignacionInstructorController::class, 'index'])->name('index');
    Route::get('/create', [AsignacionInstructorController::class, 'create'])->name('create');
    Route::post('/', [AsignacionInstructorController::class, 'store'])->name('store');
    Route::get('/{asignacion}', [AsignacionInstructorController::class, 'show'])->name('show');
    Route::get('/{asignacion}/edit', [AsignacionInstructorController::class, 'edit'])->name('edit');
    Route::put('/{asignacion}', [AsignacionInstructorController::class, 'update'])->name('update');

    Route::get('/fichas/{ficha}/competencias', [AsignacionInstructorController::class, 'competenciasPorFicha'])
        ->name('competencias_por_ficha');
    Route::get('/competencias/{competencia}/resultados', [AsignacionInstructorController::class, 'resultadosPorCompetencia'])
        ->name('resultados_por_competencia');
});


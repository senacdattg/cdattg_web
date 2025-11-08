<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Complementarios\AspiranteComplementarioController;

Route::get('/gestion-aspirantes', [AspiranteComplementarioController::class, 'gestionAspirantes'])
    ->name('gestion-aspirantes')
    ->middleware('auth');

Route::get('/programas-complementarios/{curso}', [AspiranteComplementarioController::class, 'verAspirantes'])
    ->name('programas-complementarios.ver-aspirantes')
    ->middleware('auth');

Route::post('/programas-complementarios/{complementarioId}/agregar-aspirante', [AspiranteComplementarioController::class, 'agregarAspirante'])
    ->name('programas-complementarios.agregar-aspirante')
    ->middleware('auth');

Route::delete('/programas-complementarios/{complementarioId}/aspirante/{aspiranteId}', [AspiranteComplementarioController::class, 'eliminarAspirante'])
    ->name('programas-complementarios.eliminar-aspirante')
    ->middleware('auth');

Route::get('/programas-complementarios/{complementarioId}/exportar-excel', [AspiranteComplementarioController::class, 'exportarAspirantesExcel'])
    ->name('programas-complementarios.exportar-excel')
    ->middleware('auth');

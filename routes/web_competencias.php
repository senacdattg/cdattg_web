<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompetenciaController;

/*
|--------------------------------------------------------------------------
| Competencias Routes
|--------------------------------------------------------------------------
|
| Rutas para la gestión de competencias.
| Todas las rutas están protegidas por middleware de autenticación y permisos.
|
*/

Route::resource('competencias', CompetenciaController::class)
    ->parameters(['competencias' => 'competencia']);

Route::get('/competencias-search', [CompetenciaController::class, 'search'])
    ->name('competencias.search')
    ->middleware('can:VER COMPETENCIA');

Route::middleware('can:CAMBIAR ESTADO COMPETENCIA')->group(function () {
    Route::put('/competencias/{competencia}/cambiar-estado', [CompetenciaController::class, 'cambiarEstado'])
         ->name('competencias.cambiarEstado');
});

Route::middleware('can:GESTIONAR RESULTADOS COMPETENCIA')->group(function () {
    Route::get('/competencias/{competencia}/gestionar-resultados', [CompetenciaController::class, 'gestionarResultados'])
         ->name('competencias.gestionarResultados');
    
    Route::post('/competencias/{competencia}/asociar-resultado', [CompetenciaController::class, 'asociarResultado'])
         ->name('competencias.asociarResultado');
    
    Route::post('/competencias/{competencia}/asociar-resultados', [CompetenciaController::class, 'asociarResultados'])
         ->name('competencias.asociarResultados');
    
    Route::delete('/competencias/{competencia}/desasociar-resultado/{resultado}', [CompetenciaController::class, 'desasociarResultado'])
         ->name('competencias.desasociarResultado');
});


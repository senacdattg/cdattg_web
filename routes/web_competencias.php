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
    ->name('competencias.search');

Route::middleware('can:EDITAR COMPETENCIA')->group(function () {
    Route::put('/competencias/{competencia}/cambiar-estado', [CompetenciaController::class, 'cambiarEstado'])
         ->name('competencias.cambiarEstado');
    
    Route::get('/competencias/{competencia}/gestionar-resultados', [CompetenciaController::class, 'gestionarResultados'])
         ->name('competencias.gestionarResultados');
    
    Route::post('/competencias/{competencia}/asociar-resultado', [CompetenciaController::class, 'asociarResultado'])
         ->name('competencias.asociarResultado');
    
    Route::delete('/competencias/{competencia}/desasociar-resultado/{resultado}', [CompetenciaController::class, 'desasociarResultado'])
         ->name('competencias.desasociarResultado');
});


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResultadosAprendizajeController;

/*
|--------------------------------------------------------------------------
| Resultados de Aprendizaje Routes
|--------------------------------------------------------------------------
|
| Rutas para la gestión de resultados de aprendizaje (RAPs).
| Todas las rutas están protegidas por middleware de autenticación y permisos.
|
*/

Route::resource('resultados-aprendizaje', ResultadosAprendizajeController::class)
    ->parameters(['resultados-aprendizaje' => 'resultado_aprendizaje']);

Route::get('/resultados-aprendizaje-search', [ResultadosAprendizajeController::class, 'search'])
    ->name('resultados-aprendizaje.search');

Route::middleware('can:EDITAR RESULTADO APRENDIZAJE')->group(function () {
    Route::put('/resultados-aprendizaje/{resultadoAprendizaje}/cambiar-estado', [ResultadosAprendizajeController::class, 'cambiarEstado'])
         ->name('resultados-aprendizaje.cambiarEstado');
    
    Route::get('/resultados-aprendizaje/{resultadoAprendizaje}/gestionar-competencias', [ResultadosAprendizajeController::class, 'gestionarCompetencias'])
         ->name('resultados-aprendizaje.gestionarCompetencias');
    
    Route::post('/resultados-aprendizaje/{resultadoAprendizaje}/asociar-competencia', [ResultadosAprendizajeController::class, 'asociarCompetencia'])
         ->name('resultados-aprendizaje.asociarCompetencia');
    
    Route::delete('/resultados-aprendizaje/{resultadoAprendizaje}/desasociar-competencia/{competencia}', [ResultadosAprendizajeController::class, 'desasociarCompetencia'])
         ->name('resultados-aprendizaje.desasociarCompetencia');
});


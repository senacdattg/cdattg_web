<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AprendizController;

/*
|--------------------------------------------------------------------------
| Rutas de Aprendices
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas para la gestión de aprendices.
| Todas las rutas están protegidas con middleware de autenticación y permisos.
|
*/

// Rutas CRUD de recurso para Aprendiz
Route::resource('aprendices', AprendizController::class);

// Ruta para cambiar estado del aprendiz
Route::middleware('can:EDITAR APRENDIZ')->group(function () {
    Route::put('/aprendices/{aprendiz}/cambiar-estado', [AprendizController::class, 'cambiarEstado'])
        ->name('aprendices.cambiarEstado');
});

// Rutas API para aprendices
Route::middleware('can:VER APRENDIZ')->group(function () {
    // API: Listar todos los aprendices
    Route::get('/api/aprendices', [AprendizController::class, 'apiIndex'])
        ->name('api.aprendices.index');
    
    // API: Buscar aprendices por nombre o documento
    Route::get('/api/aprendices/search', [AprendizController::class, 'search'])
        ->name('api.aprendices.search');
    
    // API: Obtener aprendices por ficha
    Route::get('/api/aprendices/ficha/{fichaId}', [AprendizController::class, 'getAprendicesByFicha'])
        ->name('api.aprendices.by.ficha');
});


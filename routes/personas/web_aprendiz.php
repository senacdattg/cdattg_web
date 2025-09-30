<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AprendizController;

// Rutas de recurso para Aprendiz
Route::resource('aprendices', AprendizController::class);

// Rutas API adicionales
Route::middleware('can:VER APRENDIZ')->group(function () {
    Route::get('/api/aprendices', [AprendizController::class, 'apiIndex'])->name('api.aprendices.index');
    Route::get('/api/aprendices/search', [AprendizController::class, 'search'])->name('api.aprendices.search');
    Route::get('/api/aprendices/ficha/{fichaId}', [AprendizController::class, 'getAprendicesByFicha'])->name('api.aprendices.by.ficha');
});


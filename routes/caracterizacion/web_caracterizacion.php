<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CaracterizacionController;

// Rutas para CaracterizacionController
Route::resource('caracterizacion', CaracterizacionController::class);
route::middleware('can:VER PROGRAMA DE CARACTERIZACION')->group(function () {
    Route::get('/caracter/index', [CaracterizacionController::class, 'index'])->name('caracter.index');
    Route::get('/caracterizacion/search', [CaracterizacionController::class, 'show'])->name('caracterizacion.search');
    Route::get('/caracterizacion/create', [CaracterizacionController::class, 'create'])->name('caracterizacion.legacy.create');
    Route::post('/caracterizacion/ficha', [CaracterizacionController::class, 'getCaracterByFicha'])->name('caracterizacion.ficha');
    Route::post('/caracterizacion/store', [CaracterizacionController::class, 'store'])->name('caracterizacion.legacy.store');
    Route::get('/caracterizacion/{id}/edit', [CaracterizacionController::class, 'edit'])->name('caracterizacion.legacy.edit');
    Route::get('/caracterizacion/destroy/{id}', [CaracterizacionController::class, 'destroy'])->name('caracterizacion.legacy.destroy');
    Route::post('/caracterizacion/{id}', [CaracterizacionController::class, 'update'])->name('caracterizacion.legacy.update');
    //Route::get('/caracterizacion/{id}/show', [CaracterizacionController::class, 'show'])->name('caracterizacion.show');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\SalidaController;

// Rutas de Salidas del Inventario
Route::prefix('inventario')
    ->name('inventario.salida.')
    ->group(function () {
        Route::get('salida/aprobar', [SalidaController::class, 'aprobar'])->name('aprobar');
    });

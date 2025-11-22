<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControlSeguimiento\IngresoSalidaController;

/**
 * Rutas del módulo de Ingreso y Salida
 *
 * Este módulo pertenece a Control y Seguimiento y usa el componente Livewire
 * IngresoSalidaComponent que maneja toda la lógica de búsqueda y registro
 * de ingresos y salidas de personas.
 */

Route::middleware('auth')
    ->prefix('control-seguimiento/ingreso-salida')
    ->name('control-seguimiento.ingreso-salida.')
    ->group(function () {
        Route::get('/', [IngresoSalidaController::class, 'index'])
            ->name('index');
        Route::get('/registrar', [IngresoSalidaController::class, 'create'])
            ->name('create');
    });


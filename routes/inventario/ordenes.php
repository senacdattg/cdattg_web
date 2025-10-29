<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\OrdenController;

// Rutas para órdenes del inventario (préstamos y salidas)
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        // Vista de préstamos y salidas (GET)
        Route::get('ordenes/prestamos-salidas', [OrdenController::class, 'prestamosSalidas'])
            ->name('prestamos-salidas');
            
        // Alias para solicitar (GET)
        Route::get('ordenes/solicitar', [OrdenController::class, 'prestamosSalidas'])
            ->name('solicitar');
            
        // Procesar préstamo/salida (POST)
        Route::post('ordenes/prestamos-salidas', [OrdenController::class, 'storePrestamos'])
            ->name('prestamos-salidas.store');
            
        // Procesar solicitud (POST) - alias
        Route::post('ordenes/solicitar', [OrdenController::class, 'storePrestamos'])
            ->name('solicitar.store');
        
        // Rutas adicionales para órdenes (sin crear conflictos)
        Route::get('ordenes', [OrdenController::class, 'index'])
            ->name('ordenes.index');
        Route::put('ordenes/{orden}', [OrdenController::class, 'update'])
            ->name('ordenes.update');
        Route::delete('ordenes/{orden}', [OrdenController::class, 'destroy'])
            ->name('ordenes.destroy');
    });

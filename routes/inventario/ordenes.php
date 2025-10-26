<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\OrdenController;

// Rutas para órdenes del inventario (préstamos y salidas)
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        // Solo index porque el flujo es diferente (prestamos_salidas)
        Route::resource('ordenes', OrdenController::class)->only([
            'index', 'store', 'update', 'destroy'
        ]);
        
        // Ruta adicional para préstamos y salidas
        Route::get('ordenes/prestamos-salidas', [OrdenController::class, 'prestamosSalidas'])
            ->name('prestamos-salidas');
    });

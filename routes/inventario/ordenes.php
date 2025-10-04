<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\DetalleOrdenController;
use App\Http\Controllers\Inventario\OrdenController;

// Rutas para órdenes del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        Route::get('ordenes', [OrdenController::class, 'index'])->name('ordenes.index');

        // Préstamos / Salidas
        Route::get('ordenes/prestamos-salidas', [DetalleOrdenController::class, 'create'])
            ->name('ordenes.prestamos_salidas.create');
        Route::post('ordenes/prestamos-salidas', [DetalleOrdenController::class, 'store'])
            ->name('ordenes.prestamos_salidas.store');
    });

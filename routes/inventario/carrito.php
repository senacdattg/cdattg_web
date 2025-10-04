<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\CarritoController;

// Rutas para el carrito del inventario
Route::prefix('inventario')
    ->name('inventario.carrito.')
    ->group(function () {
        Route::get('carrito', [CarritoController::class, 'index'])->name('index');
        Route::post('carrito/agregar', [CarritoController::class, 'agregar'])->name('agregar');
        Route::delete('carrito/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('eliminar');
    });
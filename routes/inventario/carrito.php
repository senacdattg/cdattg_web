<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\CarritoController;

// Rutas para el carrito del inventario
Route::prefix('inventario')
    ->name('inventario.carrito.')
    ->group(function () {
        // Rutas administrativas
        Route::get('carrito', [CarritoController::class, 'index'])->name('index');
        
        // Rutas e-commerce modernas
        Route::get('carrito-ecommerce', [CarritoController::class, 'ecommerce'])->name('ecommerce');
        
        // Rutas AJAX para funcionalidad del carrito
        Route::post('carrito/agregar', [CarritoController::class, 'agregar'])->name('agregar');
        Route::put('carrito/actualizar/{id}', [CarritoController::class, 'actualizar'])->name('actualizar');
        Route::delete('carrito/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('eliminar');
        Route::post('carrito/vaciar', [CarritoController::class, 'vaciar'])->name('vaciar');
        Route::get('carrito/contenido', [CarritoController::class, 'contenido'])->name('contenido');
    });
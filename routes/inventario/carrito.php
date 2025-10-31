<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\CarritoController;

// Rutas para el carrito del inventario
Route::prefix('inventario')
    ->name('inventario.carrito.')
    ->group(function () {    
        // Rutas e-commerce modernas
        Route::get('carrito-sena', [CarritoController::class, 'index'])->name('ecommerce');
        
        // Rutas AJAX para funcionalidad del carrito
        Route::post('carrito/agregar', [CarritoController::class, 'agregar'])->name('agregar');
        Route::put('carrito/actualizar/{id}', [CarritoController::class, 'actualizar'])->name('actualizar');
        Route::delete('carrito/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('eliminar');
        Route::post('carrito/vaciar', [CarritoController::class, 'vaciar'])->name('vaciar');
        Route::get('carrito/contenido', [CarritoController::class, 'contenido'])->name('contenido');
    });
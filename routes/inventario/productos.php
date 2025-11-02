<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\ProductoController;

// Rutas para productos del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->middleware(['auth'])
    ->group(function () {
        // Rutas e-commerce (estilo moderno) - DEBEN IR ANTES del resource
        Route::get('productos/catalogo', [ProductoController::class, 'catalogo'])
            ->name('productos.catalogo');
        
        // Ruta para detalles del producto (en modal)
        Route::get('productos/detalles/{id}', [ProductoController::class, 'detalles'])
            ->name('productos.detalles');
        
        // Rutas AJAX para funcionalidades e-commerce
        Route::post('productos/agregar-carrito', [ProductoController::class, 'agregarAlCarrito'])
            ->name('productos.agregar-carrito');
        
        Route::get('productos/buscar', [ProductoController::class, 'buscar'])
            ->name('productos.buscar');
        
        // Ruta original para búsqueda por código de barras
        Route::get('productos/buscar/{codigo}', [ProductoController::class, 'buscarPorCodigo'])
            ->name('productos.buscar-codigo');
        
        // Rutas administrativas (estilo admin) - resource al final
        Route::resource('productos', ProductoController::class);
    });

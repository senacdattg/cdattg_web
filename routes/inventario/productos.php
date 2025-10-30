<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\ProductoController;

// Rutas para productos del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        // Rutas administrativas (estilo admin)
        Route::resource('productos', ProductoController::class)->except(['catalogo']);
        
        // Rutas e-commerce (estilo moderno)
        Route::get('/productos/catalogo', [ProductoController::class, 'catalogo'])
            ->name('productos.catalogo');
        
        // Vista de prueba simple para diagnosticar problemas
        Route::get('/productos/test-simple', [ProductoController::class, 'simpleCatalogo'])
            ->name('productos.test-simple');
            
        // Vista con datos de prueba
        Route::get('/productos/test-with-data', [ProductoController::class, 'testWithData'])
            ->name('productos.test-with-data');
        
        // Rutas AJAX para funcionalidades e-commerce
        Route::post('/productos/agregar-carrito', [ProductoController::class, 'agregarAlCarrito'])
            ->name('productos.agregar-carrito');
        
        Route::get('/productos/buscar', [ProductoController::class, 'buscar'])
            ->name('productos.buscar');
        
        // Ruta original para búsqueda por código de barras
        Route::get('/productos/buscar/{codigo}', [ProductoController::class, 'buscarPorCodigo'])
            ->name('productos.buscar-codigo');
    });

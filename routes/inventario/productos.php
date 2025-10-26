<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\ProductoController;

// Rutas para productos del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        Route::resource('productos', ProductoController::class);
        Route::get('/productos/buscar/{codigo}', [ProductoController::class, 'buscarPorCodigo']);
    });

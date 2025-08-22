<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\ProductoController;

Route::prefix('inventario')->group(function () {
    Route::resource('productos', ProductoController::class);
    Route::get('buscar_producto', [ProductoController::class, 'to_search'])->name('inventario.producto.buscar');
});

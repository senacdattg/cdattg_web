<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\CarritoController;

Route::prefix('inventario')->group(function () {
    Route::resource('carrito', CarritoController::class)->only(['index', 'agregar', 'eliminar']);
});
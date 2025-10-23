<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\OrdenController;

// Rutas para órdenes del inventario (préstamos y salidas)
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        Route::resource('ordenes', OrdenController::class);
    });

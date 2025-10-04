<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\ProveedorController;

// Rutas para proveedores del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        Route::resource('proveedores', ProveedorController::class);
    });
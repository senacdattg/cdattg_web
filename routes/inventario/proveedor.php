<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\ProveedorController;

// Rutas para proveedores del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        // Rutas completas para proveedores con vistas CRUD
        Route::resource('proveedores', ProveedorController::class)->except(['catalogo'])->parameters([
            'proveedores' => 'proveedor'
        ]);
    });
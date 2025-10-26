<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\ProveedorController;

// Rutas para proveedores del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        // Solo se usa index porque create/edit están en la misma vista
        Route::resource('proveedores', ProveedorController::class)->only([
            'index', 'store', 'update', 'destroy'
        ])->parameters([
            'proveedores' => 'proveedor'
        ]);
    });
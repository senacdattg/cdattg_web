<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\ProveedorController;

// Rutas para proveedores del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->middleware('auth')
    ->group(function () {
        // Ruta para obtener municipios por departamento (DEBE IR ANTES que resource)
        Route::get('proveedores/municipios/{departamentoId}', [ProveedorController::class, 'getMunicipiosPorDepartamento'])
            ->name('proveedores.municipios');

        // Rutas completas para proveedores con vistas CRUD
        Route::resource('proveedores', ProveedorController::class)->except(['catalogo'])->parameters([
            'proveedores' => 'proveedor'
        ]);
    });

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\MarcaController;

// Rutas para marcas del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        // Solo se usa index porque create/edit están en modales
        Route::resource('marcas', MarcaController::class)->only([
            'index', 'store', 'update', 'destroy'
        ]);
    });
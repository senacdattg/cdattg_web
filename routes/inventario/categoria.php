<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\CategoriaController;

// Rutas para categorias del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        // Solo se usa index porque create/edit están en modales
        Route::resource('categorias', CategoriaController::class)->only([
            'index', 'store', 'update', 'destroy'
        ]);
    });
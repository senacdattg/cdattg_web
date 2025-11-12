<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\MarcaController;

// Rutas para marcas del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        // Rutas completas para marcas con vistas CRUD
        Route::resource('marcas', MarcaController::class)->except(['catalogo']);
    });

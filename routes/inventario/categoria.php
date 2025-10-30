<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\CategoriaController;

// Rutas para categorias del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        // Rutas completas para categorías con vistas CRUD
        Route::resource('categorias', CategoriaController::class)->except(['catalogo']);
    });
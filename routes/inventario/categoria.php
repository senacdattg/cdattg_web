<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\CategoriaController;

// Rutas para categorias del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        Route::resource('categorias', CategoriaController::class);
    });
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\MarcaController;

// Rutas para marcas del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        Route::resource('marcas', MarcaController::class);
    });
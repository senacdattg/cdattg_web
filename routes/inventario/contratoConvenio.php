<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\ContratoConvenioController;

// Rutas para el recurso ContratoConvenio
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        Route::resource('contratos-convenios', ContratoConvenioController::class);
    });
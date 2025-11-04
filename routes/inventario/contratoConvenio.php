<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\ContratoConvenioController;

// Rutas para el recurso ContratoConvenio
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        // Rutas completas para contratos y convenios con vistas CRUD
        Route::resource('contratos-convenios', ContratoConvenioController::class)->except(['catalogo'])->parameters([
            'contratos-convenios' => 'contratoConvenio'
        ]);
    });
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\ContratoConvenioController;

// Rutas para el recurso ContratoConvenio
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        // Solo se usa index porque create/edit están en la misma vista
        Route::resource('contratos-convenios', ContratoConvenioController::class)->only([
            'index', 'store', 'update', 'destroy'
        ])->parameters([
            'contratos-convenios' => 'contratoConvenio'
        ]);
    });
<?php

use Illuminate\Support\Facades\Route;

Route::prefix('inventario')->group(function () {
    Route::resource('salida', SalidaController::class)->only(['index', 'create', 'store']);
    Route::get('salida/aprobar', function() {
        return view('inventario.salida.aprobar_salida');
    })->name('salida.aprobar');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermisoController;

Route::middleware('can:ASIGNAR PERMISOS')->group(function () {
    Route::resource('permiso', PermisoController::class)->except(['show']);
    Route::get('/permiso/{user}', [PermisoController::class, 'show'])->name('permiso.show');
});
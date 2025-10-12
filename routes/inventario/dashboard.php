<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\DashboardController;
use App\Http\Controllers\Inventario\ProductoController;

// Grupo de rutas de inventario
Route::prefix('inventario')->group(function () {
    // Dashboard de inventario
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('inventario.dashboard');
});
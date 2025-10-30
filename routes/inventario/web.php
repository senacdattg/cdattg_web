<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\DashboardController;
use App\Http\Controllers\Inventario\ProductoController;

// Rutas protegidas por autenticación (solo dashboard y productos principales)
Route::middleware(['auth'])->group(function () {
    // Dashboard de inventario
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('inventario.dashboard');
});
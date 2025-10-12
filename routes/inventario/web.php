<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\DashboardController;
use App\Http\Controllers\Inventario\ProductoController;

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    // Dashboard de inventario
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('inventario.dashboard');

    // Rutas de productos
    Route::resource('productos', ProductoController::class);
});
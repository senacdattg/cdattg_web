<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\DetalleOrdenController;

// Rutas para órdenes del inventario
Route::get('inventario/ordenes', function () {
    return view('inventario.ordenes.index');
})->name('inventario.ordenes.index');

// Rutas para préstamos/salidas
Route::get('inventario/ordenes/prestamos-salidas', [DetalleOrdenController::class, 'create'])
    ->name('inventario.ordenes.prestamos_salidas.create');

Route::post('inventario/ordenes/prestamos-salidas', [DetalleOrdenController::class, 'store'])
    ->name('inventario.ordenes.prestamos_salidas.store');

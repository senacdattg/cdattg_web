<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogoutController;

// Ruta GET para manejar accesos directos y prefetch de Livewire
Route::get('logout', [LogoutController::class, 'cerrarSesion'])->name('logout.get');
// Ruta POST para logout normal (formularios)
Route::post('logout', [LogoutController::class, 'cerrarSesion'])->name('logout');
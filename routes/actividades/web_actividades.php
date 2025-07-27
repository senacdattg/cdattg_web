<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistroActividadesController;

//Route::resource('registro-actividades', RegistroActividadesController::class);

Route::get('registro-actividades/actividades/{caracterizacion}', [RegistroActividadesController::class, 'index'])->name('registro-actividades.index');
Route::post('registro-actividades/actividades/{caracterizacion}', [RegistroActividadesController::class, 'store'])->name('registro-actividades.store');

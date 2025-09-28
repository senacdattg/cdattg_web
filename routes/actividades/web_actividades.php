<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistroActividadesController;

//Route::resource('registro-actividades', RegistroActividadesController::class);

Route::get('registro-actividades/actividades/{caracterizacion}', [RegistroActividadesController::class, 'index'])->name('registro-actividades.index');
Route::post('registro-actividades/actividades/{caracterizacion}', [RegistroActividadesController::class, 'store'])->name('registro-actividades.store');
Route::get('registro-actividades/actividades/{caracterizacion}/{actividad}/edit', [RegistroActividadesController::class, 'edit'])->name('registro-actividades.edit');
Route::put('registro-actividades/actividades/{caracterizacion}/{actividad}', [RegistroActividadesController::class, 'update'])->name('registro-actividades.update');
Route::delete('registro-actividades/actividades/{caracterizacion}/{actividad}', [RegistroActividadesController::class, 'destroy'])->name('registro-actividades.destroy');
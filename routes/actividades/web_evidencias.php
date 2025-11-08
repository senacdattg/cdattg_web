<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvidenciasController;

Route::resource('evidencias', EvidenciasController::class)->except(['create', 'store']);
Route::get('evidencias/create/{caracterizacion}', [EvidenciasController::class, 'create'])->name('evidencias.create');
Route::post('evidencias/store/{caracterizacion}', [EvidenciasController::class, 'store'])->name('evidencias.store');

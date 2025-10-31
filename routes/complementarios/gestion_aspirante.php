<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplementarioController;

Route::get('/gestion-aspirantes', [ComplementarioController::class, 'gestionAspirantes'])
    ->name('gestion-aspirantes')
    ->middleware('auth');

Route::get('/programas-complementarios/{curso}', [ComplementarioController::class, 'verAspirantes'])
    ->name('programas-complementarios.ver-aspirantes')
    ->middleware('auth');

Route::post('/programas-complementarios/{complementarioId}/agregar-aspirante', [ComplementarioController::class, 'agregarAspirante'])
    ->name('programas-complementarios.agregar-aspirante')
    ->middleware('auth');

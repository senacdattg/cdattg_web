<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplementarioController;

Route::get('/aspirantes/{curso}', [ComplementarioController::class, 'verAspirantes'])
    ->name('aspirantes.ver')
    ->middleware('auth');

Route::get('/mi-perfil', [ComplementarioController::class, 'miPerfil'])
    ->name('aspirantes.mi-perfil')
    ->middleware('auth');
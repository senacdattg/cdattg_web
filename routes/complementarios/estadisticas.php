<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplementarioController;

Route::get('/estadisticas', [ComplementarioController::class, 'estadisticas'])
    ->name('estadisticas')
    ->middleware('auth');

Route::get('/estadisticas/api', [ComplementarioController::class, 'apiEstadisticas'])
    ->name('estadisticas.api')
    ->middleware('auth');

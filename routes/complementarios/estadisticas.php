<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Complementarios\EstadisticaComplementarioController;

Route::get('/estadisticas', [EstadisticaComplementarioController::class, 'estadisticas'])
    ->name('estadisticas')
    ->middleware('auth');

Route::get('/estadisticas/api', [EstadisticaComplementarioController::class, 'apiEstadisticas'])
    ->name('estadisticas.api')
    ->middleware('auth');

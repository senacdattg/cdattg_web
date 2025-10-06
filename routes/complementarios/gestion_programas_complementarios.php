<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplementarioController;

Route::get('/gestion-programas-complementarios', [ComplementarioController::class, 'gestionProgramasComplementarios'])
    ->name('gestion-programas-complementarios')
    ->middleware('auth');

Route::post('/complementarios-ofertados', [ComplementarioController::class, 'store'])
    ->name('complementarios-ofertados.store')
    ->middleware('auth');

Route::put('/complementarios-ofertados/{id}', [ComplementarioController::class, 'update'])
    ->name('complementarios-ofertados.update')
    ->middleware('auth');

Route::delete('/complementarios-ofertados/{id}', [ComplementarioController::class, 'destroy'])
    ->name('complementarios-ofertados.destroy')
    ->middleware('auth');

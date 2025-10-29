<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplementarioController;

Route::get('/gestion-programas-complementarios', [ComplementarioController::class, 'gestionProgramasComplementarios'])
    ->name('gestion-programas-complementarios')
    ->middleware('auth');

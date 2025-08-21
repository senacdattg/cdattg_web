<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplementarioController;

Route::get('/estadisticas', [ComplementarioController::class, 'estadisticas'])
    ->name('estadisticas')
    ->middleware('auth');
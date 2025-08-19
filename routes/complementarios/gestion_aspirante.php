<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplementarioController;

Route::get('/gestion-aspirantes', [ComplementarioController::class, 'gestionAspirantes'])
    ->name('gestion-aspirantes')
    ->middleware('auth');

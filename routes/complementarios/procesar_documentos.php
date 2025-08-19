<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplementarioController;

Route::get('/procesar-documentos', [ComplementarioController::class, 'procesarDcoumentos'])
    ->name('procesar-documentos')
    ->middleware('auth');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Complementarios\AspiranteComplementarioController;

Route::get('/aspirantes/{curso}', [AspiranteComplementarioController::class, 'verAspirantes'])
    ->name('aspirantes.ver')
    ->middleware('auth');

// Route moved to web.php for proper middleware handling
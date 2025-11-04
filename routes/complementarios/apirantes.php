<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplementarioController;

Route::get('/aspirantes/{curso}', [ComplementarioController::class, 'verAspirantes'])
    ->name('aspirantes.ver')
    ->middleware('auth');

// Route moved to web.php for proper middleware handling
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplementarioController;

Route::get('/programas/{programa}', [ComplementarioController::class, 'verPrograma'])
    ->name('programa_complementario.ver');


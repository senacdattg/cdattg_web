<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplementarioController;

Route::get('/programas/{id}', [ComplementarioController::class, 'verPrograma'])
    ->name('programa_complementario.ver');


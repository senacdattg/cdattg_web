<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplementarioController;

Route::get('/estadisticas', [ComplementarioController::class, 'estadisticas'])
    ->name('estadisticas')
    ->middleware('auth');

Route::get('/api/municipios/{departamento_id}', [ComplementarioController::class, 'getMunicipiosByDepartamento'])
    ->name('api.municipios.by.departamento')
    ->middleware('auth');
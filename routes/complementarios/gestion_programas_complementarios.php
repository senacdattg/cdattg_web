<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Complementarios\ProgramaComplementarioController;

Route::get('/gestion-programas-complementarios', [ProgramaComplementarioController::class, 'gestionProgramasComplementarios'])
    ->name('gestion-programas-complementarios')
    ->middleware('auth');

Route::get('/complementarios-ofertados/create', [ProgramaComplementarioController::class, 'create'])
    ->name('complementarios-ofertados.create')
    ->middleware('auth');

Route::get('/complementarios-ofertados/{id}/edit', [ProgramaComplementarioController::class, 'edit'])
    ->name('complementarios-ofertados.edit')
    ->middleware('auth');

Route::post('/complementarios-ofertados', [ProgramaComplementarioController::class, 'store'])
    ->name('complementarios-ofertados.store')
    ->middleware('auth');

Route::put('/complementarios-ofertados/{id}', [ProgramaComplementarioController::class, 'update'])
    ->name('complementarios-ofertados.update')
    ->middleware('auth');

Route::delete('/complementarios-ofertados/{id}', [ProgramaComplementarioController::class, 'destroy'])
    ->name('complementarios-ofertados.destroy')
    ->middleware('auth');

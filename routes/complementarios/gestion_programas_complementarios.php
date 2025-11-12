<?php

use App\Http\Controllers\Complementarios\ProgramaComplementarioController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')
    ->prefix('complementarios-ofertados')
    ->name('complementarios-ofertados.')
    ->group(function () {
        Route::get('/', [ProgramaComplementarioController::class, 'index'])
            ->name('index');

        Route::get('/create', [ProgramaComplementarioController::class, 'create'])
            ->name('create');

        Route::get('/{programa}/edit', [ProgramaComplementarioController::class, 'edit'])
            ->name('edit');

        Route::post('/', [ProgramaComplementarioController::class, 'store'])
            ->name('store');

        Route::put('/{programa}', [ProgramaComplementarioController::class, 'update'])
            ->name('update');

        Route::delete('/{programa}', [ProgramaComplementarioController::class, 'destroy'])
            ->name('destroy');
    });

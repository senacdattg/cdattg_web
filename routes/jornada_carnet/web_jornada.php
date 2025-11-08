<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JornadaController;

Route::resource('jornada', JornadaController::class);

Route::middleware('can:VER PROGRAMA DE CARACTERIZACION')->group(function () {
    Route::get('/jornada/{id}/destroy', [JornadaController::class, 'destroy'])->name('jornada.legacy.destroy');
    Route::post('/jornada/{id}/update', [JornadaController::class, 'update'])->name('jornada.legacy.update');
});

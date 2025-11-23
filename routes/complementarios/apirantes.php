<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Complementarios\AspiranteComplementarioController;

// Ruta antigua mantenida por compatibilidad - redirige a la nueva ruta RESTful
Route::get('/aspirantes/{curso}', function ($curso) {
    $programa = \App\Models\ComplementarioOfertado::where('nombre', str_replace('-', ' ', $curso))->firstOrFail();
    return redirect()->route('aspirantes.programa', ['programa' => $programa->id]);
})->name('aspirantes.ver')->middleware('auth');
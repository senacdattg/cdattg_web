<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Complementarios\AspiranteComplementarioController;

// Rutas antiguas mantenidas por compatibilidad - redirigen a las nuevas rutas RESTful
Route::get('/gestion-aspirantes', function () {
    return redirect()->route('aspirantes.index');
})->name('gestion-aspirantes')->middleware('auth');

// Mantener ruta antigua por compatibilidad (búsqueda por nombre)
Route::get('/programas-complementarios/{curso}', function ($curso) {
    $programa = \App\Models\ComplementarioOfertado::where('nombre', str_replace('-', ' ', $curso))->firstOrFail();
    return redirect()->route('aspirantes.programa', ['programa' => $programa->id]);
})->name('programas-complementarios.ver-aspirantes')->middleware('auth');

Route::post(
    '/programas-complementarios/{complementarioId}/agregar-aspirante',
    [AspiranteComplementarioController::class, 'agregarAspirante']
)
    ->name('programas-complementarios.agregar-aspirante')
    ->middleware('auth');

Route::delete(
    '/programas-complementarios/{complementarioId}/aspirante/{aspiranteId}',
    [AspiranteComplementarioController::class, 'eliminarAspirante']
)
    ->name('programas-complementarios.eliminar-aspirante')
    ->middleware('auth');

Route::get(
    '/programas-complementarios/{complementarioId}/exportar-excel',
    [AspiranteComplementarioController::class, 'exportarAspirantesExcel']
)
    ->name('programas-complementarios.exportar-excel')
    ->middleware('auth');

Route::get(
    '/programas-complementarios/{complementarioId}/descargar-cedulas',
    [AspiranteComplementarioController::class, 'descargarCedulas']
)
    ->name('programas-complementarios.descargar-cedulas')
    ->middleware('auth');

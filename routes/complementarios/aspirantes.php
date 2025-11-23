<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Complementarios\AspiranteComplementarioController;

// Rutas RESTful para aspirantes complementarios
Route::middleware('auth')->group(function () {
    // Lista de programas complementarios (gestión de aspirantes)
    Route::get('/aspirantes', [AspiranteComplementarioController::class, 'index'])
        ->name('aspirantes.index');

    // Lista de aspirantes de un programa específico
    Route::get('/aspirantes/programa/{programa}', [AspiranteComplementarioController::class, 'programa'])
        ->name('aspirantes.programa');

    // Buscar persona por documento
    Route::post('/aspirantes/buscar-persona', [AspiranteComplementarioController::class, 'buscarPersona'])
        ->name('aspirantes.buscar-persona');

    // Mostrar formulario para crear nuevo aspirante
    Route::get('/aspirantes/programa/{programa}/create', [AspiranteComplementarioController::class, 'create'])
        ->name('aspirantes.create');

    // Almacenar nuevo aspirante
    Route::post('/aspirantes/programa/{programa}/store', [AspiranteComplementarioController::class, 'store'])
        ->name('aspirantes.store');

    // Agregar aspirante existente por número de documento (mantener compatibilidad)
    Route::post('/aspirantes/programa/{complementarioId}/agregar-aspirante', [AspiranteComplementarioController::class, 'agregarAspirante'])
        ->name('aspirantes.agregar-existente');

    // Eliminar/rechazar aspirante de un programa
    Route::delete('/aspirantes/programa/{complementarioId}/aspirante/{aspiranteId}', [AspiranteComplementarioController::class, 'eliminarAspirante'])
        ->name('aspirantes.destroy');

    // Exportar aspirantes a Excel
    Route::get('/aspirantes/programa/{complementarioId}/exportar-excel', [AspiranteComplementarioController::class, 'exportarAspirantesExcel'])
        ->name('aspirantes.exportar-excel');

    // Descargar cédulas de aspirantes
    Route::get('/aspirantes/programa/{complementarioId}/descargar-cedulas', [AspiranteComplementarioController::class, 'descargarCedulas'])
        ->name('aspirantes.descargar-cedulas');
});


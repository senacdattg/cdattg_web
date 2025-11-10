<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TalentoHumanoController;
use App\Http\Controllers\PersonaController;

/**
 * Rutas del módulo de Talento Humano
 *
 * Este módulo usa PersonaController para toda la lógica de negocio.
 * TalentoHumanoController solo muestra la vista de consulta.
 */

Route::middleware('auth')->prefix('talento-humano')->name('talento-humano.')->group(function () {
    // Vista principal del módulo (solo muestra el formulario)
    Route::get('/', [TalentoHumanoController::class, 'index'])
        ->name('index');

    // Consultar persona por documento (delega a PersonaController)
    Route::post('/consultar', [PersonaController::class, 'consultarPorDocumento'])
        ->name('consultar');

    // Crear nueva persona (delega a PersonaController)
    Route::post('/personas', [PersonaController::class, 'storeJson'])
        ->name('personas.store');
});

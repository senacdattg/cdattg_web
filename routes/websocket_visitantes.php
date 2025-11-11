<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebSocketVisitantesController;

// Rutas para WebSockets de visitantes
Route::prefix('websocket')->name('websocket.')->group(function () {
    // Obtener estadísticas actuales
    Route::get('/estadisticas', [WebSocketVisitantesController::class, 'obtenerEstadisticas'])
        ->name('estadisticas');

    // Registrar entrada de visitante
    Route::post('/entrada', [WebSocketVisitantesController::class, 'registrarEntrada'])
        ->name('entrada');

    // Registrar salida de visitante
    Route::post('/salida', [WebSocketVisitantesController::class, 'registrarSalida'])
        ->name('salida');

    // Obtener lista de visitantes actuales
    Route::get('/visitantes-actuales', [WebSocketVisitantesController::class, 'obtenerVisitantesActuales'])
        ->name('visitantes-actuales');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FichaCaracterizacionFlutterController;

/*
|--------------------------------------------------------------------------
| API Routes para Flutter (Sin Autenticación)
|--------------------------------------------------------------------------
|
| Aquí se definen las rutas API específicas para la aplicación Flutter
| sin requerir autenticación.
|
*/

// Ruta de prueba simple
Route::get('/flutter/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Ruta Flutter funcionando sin autenticación',
        'timestamp' => now()
    ]);
});

// Rutas públicas para Flutter (sin autenticación)
Route::get('/fichas-caracterizacion/flutter/all', [FichaCaracterizacionFlutterController::class, 'getAllFichasCaracterizacion']);
Route::get('/fichas-caracterizacion/flutter/{id}', [FichaCaracterizacionFlutterController::class, 'getFichaCaracterizacionById']);
Route::post('/fichas-caracterizacion/flutter/search', [FichaCaracterizacionFlutterController::class, 'searchFichasByNumber']);

// Rutas adicionales para Flutter
Route::get('/fichas-caracterizacion/flutter/jornada/{id}', [FichaCaracterizacionFlutterController::class, 'getFichasCaracterizacionPorJornada']);
Route::get('/fichas-caracterizacion/flutter/aprendices/{id}', [FichaCaracterizacionFlutterController::class, 'getCantidadAprendicesPorFicha']);

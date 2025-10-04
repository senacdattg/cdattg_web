<?php

use App\Http\Controllers\AmbienteController;
use App\Http\Controllers\AsistenciaAprendicesController;
use App\Http\Controllers\BloqueController;
use App\Http\Controllers\CaracterizacionController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EntradaSalidaController;
use App\Http\Controllers\FichaCaracterizacionController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\ParametroController;
use App\Http\Controllers\PisoController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\AsistenceQrController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistroAsistenciaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas de prueba sin autenticación
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando correctamente',
        'timestamp' => now()
    ]);
});

// Ruta de prueba para fichas de caracterización sin autenticación
Route::get('/fichas-caracterizacion/test', [FichaCaracterizacionController::class, 'getAllFichasCaracterizacion']);

// Obtener todas las fichas de caracterización con información completa
Route::get('fichas-caracterizacion/all', [FichaCaracterizacionController::class, 'getAllFichasCaracterizacion']);

// Obtener una ficha de caracterización específica por ID
Route::get('fichas-caracterizacion/{id}', [FichaCaracterizacionController::class, 'getFichaCaracterizacionById']);

// Buscar fichas de caracterización por número de ficha
Route::post('fichas-caracterizacion/search', [FichaCaracterizacionController::class, 'searchFichasByNumber']);

// Obtener fichas de caracterización por jornada
Route::get('fichas-caracterizacion/jornada/{id}', [FichaCaracterizacionController::class, 'getFichasCaracterizacionPorJornada']);

// Obtener la cantidad de aprendices por ficha de caracterizacion
Route::get('fichas-caracterizacion/aprendices/{id}', [FichaCaracterizacionController::class, 'getCantidadAprendicesPorFicha']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->group(function(){

    //http://127.0.0.1:8000/api/caracterizacion/byInstructorz
    Route::get('caracterizacion/byInstructor/{id}', [CaracterizacionController::class, 'CaracterizacionByInstructor']);


    // Guardar asistencia de aprendices
    Route::post('asistencia/store', [AsistenciaAprendicesController::class, 'store']);
    Route::post('asistencia/update', [AsistenciaAprendicesController::class, 'update']);
    Route::post('asistencia/novedad', [AsistenciaAprendicesController::class, 'assistenceNovedad']);
    Route::get('asistencia/getFicha/{ficha}/{jornada}', [AsistenciaAprendicesController::class, 'getList']);
    Route::post('asistencia/updateExitAsistence', [AsistenciaAprendicesController::class, 'updateExitAsistence']);
    Route::post('asistencia/updateEntraceAsistence', [AsistenciaAprendicesController::class, 'updateEntraceAsistence']);
});


route::post('authenticate', [LoginController::class, 'authenticate']);
route::post('logout', [LogoutController::class, 'logout']);

// Rutas para registro de asistencias con jornadas y WebSocket en tiempo real
Route::post('/asistencia/entrada', [RegistroAsistenciaController::class, 'registrarEntrada']);
Route::post('/asistencia/salida', [RegistroAsistenciaController::class, 'registrarSalida']);
Route::get('/asistencia/jornada', [RegistroAsistenciaController::class, 'obtenerAsistenciasPorJornada']);
Route::get('/asistencia/fichas', [RegistroAsistenciaController::class, 'obtenerFichasConJornadas']);

// Incluir rutas API para fichas de caracterización con autenticación Sanctum
include_once routes_path('api_fichas_caracterizacion.php');



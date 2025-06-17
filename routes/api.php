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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->group(function(){

    //http://127.0.0.1:8000/api/caracterizacion/byInstructor
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

// Nueva ruta para verificar el documento por QR
Route::post('/verify-document', [AsistenceQrController::class, 'verifyDocument'])->name('api.verifyDocument');


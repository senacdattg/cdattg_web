<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\AsistenceQrController;
use App\Http\Controllers\GoogleDriveController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas web de la aplicación.
|
*/

Route::get('/', [App\Http\Controllers\ComplementarioController::class, 'programasPublicos'])->name('home');

// Google Drive OAuth helper routes (para renovar refresh_token y probar conexión)
Route::get('/google-drive-connect', [GoogleDriveController::class, 'connect'])->name('google.drive.connect');
Route::get('/google-drive-callback', [GoogleDriveController::class, 'callback'])->name('google.drive.callback');
Route::get('/google-drive-test', [GoogleDriveController::class, 'test'])->name('google.drive.test');

// Rutas públicas
foreach (glob(routes_path('autenticacion/public') . '/*.php') as $routeFile) {
    include_once $routeFile;
}

Route::get('/inscripcion', [App\Http\Controllers\ComplementarioController::class, 'inscripcionGeneral'])->name('inscripcion.general');
Route::post('/inscripcion', [App\Http\Controllers\ComplementarioController::class, 'procesarInscripcionGeneral'])->name('inscripcion.procesar');
Route::get('/programas/{id}', [App\Http\Controllers\ComplementarioController::class, 'verPrograma'])->name('programa_complementario.ver');

// Rutas protegidas
Route::middleware('auth')->group(function () {
    $protectedFolders = [
        'autenticacion/private',
        'actividades',
        'usuario',
        'asistencia',
        'caracterizacion',
        'entrada_salida',
        'infraestructura',
        'jornada_carnet',
        'personas',
        'tema_parametro',
        'ubicacion',
        'actividades',
        'complementarios'
    ];

    // Incluir rutas de guías de aprendizaje
    include_once routes_path('web_guias_aprendizaje.php');
    
    // Incluir rutas de resultados de aprendizaje
    include_once routes_path('web_resultados_aprendizaje.php');
    
    // Incluir rutas de competencias
    include_once routes_path('web_competencias.php');

    foreach ($protectedFolders as $folder) {
        foreach (glob(routes_path($folder) . '/*.php') as $routeFile) {
            include_once $routeFile;
        }
    }

    Route::post('/verify-document', [AsistenceQrController::class, 'verifyDocument'])->name('api.verifyDocument');
    
    // Incluir rutas específicas de instructores
    include_once routes_path('web_instructores.php');
});

// Route::get('/perfil', [ProfileController::class, 'index'])->name('profile.index');
// Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
// Route::post('/cambiar-password', [ProfileController::class, 'changePassword'])->name('password.change');

Route::get('/programas-complementarios', [App\Http\Controllers\ComplementarioController::class, 'programasPublicos'])->name('programas-complementarios.publicos');
Route::get('/programas-complementarios/{id}/inscripcion', [App\Http\Controllers\ComplementarioController::class, 'formularioInscripcion'])->name('programas-complementarios.inscripcion');
Route::post('/programas-complementarios/{id}/inscripcion', [App\Http\Controllers\ComplementarioController::class, 'procesarInscripcion'])->name('programas-complementarios.procesar-inscripcion');
Route::get('/programas-complementarios/{id}/documentos', [App\Http\Controllers\ComplementarioController::class, 'formularioDocumentos'])->name('programas-complementarios.documentos');
Route::post('/programas-complementarios/{id}/documentos', [App\Http\Controllers\ComplementarioController::class, 'subirDocumento'])->name('programas-complementarios.subir-documentos');
Route::post('/programas-complementarios/{id}/validar-sofia', [App\Http\Controllers\ComplementarioController::class, 'validarSofia'])->name('programas-complementarios.validar-sofia');
Route::get('/sofia-validation-progress/{progressId}', [App\Http\Controllers\ComplementarioController::class, 'getValidationProgress'])->name('sofia-validation.progress');

Route::middleware('auth')->group(function () {
    Route::get('/mi-perfil', [App\Http\Controllers\ComplementarioController::class, 'Perfil'])->name('aspirantes.perfil');
});
Route::get('/departamentos/{pais}', [DepartamentoController::class, 'getByPais'])->name('departamentos.by.pais');
Route::get('/municipios/{departamento}', [MunicipioController::class, 'getByDepartamento'])->name('municipios.by.departamento');

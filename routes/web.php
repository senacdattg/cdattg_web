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

Route::get(
    '/',
    [App\Http\Controllers\Complementarios\ProgramaComplementarioController::class, 'programasPublicos']
)->name('home');

// Google Drive OAuth helper routes (para renovar refresh_token y probar conexión)
Route::get('/google-drive-connect', [GoogleDriveController::class, 'connect'])->name('google.drive.connect');
Route::get('/google-drive-callback', [GoogleDriveController::class, 'callback'])->name('google.drive.callback');
Route::get('/google-drive-test', [GoogleDriveController::class, 'test'])->name('google.drive.test');

// Rutas públicas
foreach (glob(routes_path('autenticacion/public') . '/*.php') as $routeFile) {
    include_once $routeFile;
}

Route::get(
    '/inscripcion',
    [App\Http\Controllers\Complementarios\InscripcionComplementarioController::class, 'inscripcionGeneral']
)->name('inscripcion.general');
Route::post(
    '/inscripcion',
    [App\Http\Controllers\Complementarios\InscripcionComplementarioController::class, 'procesarInscripcionGeneral']
)->name('inscripcion.procesar');
Route::get(
    '/programas/{id}',
    [App\Http\Controllers\Complementarios\ProgramaComplementarioController::class, 'verPrograma']
)->name('programa_complementario.ver');

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
        'inventario',
        'jornada_carnet',
        'personas',
        'tema_parametro',
        'ubicacion',
        'actividades',
        'complementarios',
        'talento-humano'
    ];
    foreach ($protectedFolders as $folder) {
        foreach (glob(routes_path($folder) . '/*.php') as $routeFile) {
            include_once $routeFile;
        }
    }

    Route::post('/verify-document', [AsistenceQrController::class, 'verifyDocument'])->name('api.verifyDocument');

    Route::prefix('inventario')->name('inventario.')->group(function () {
        Route::get('productos/{id}/etiqueta', [App\Http\Controllers\Inventario\ProductoController::class, 'etiqueta'])->name('productos.etiqueta');
    });
});

// Route::get('/perfil', [ProfileController::class, 'index'])->name('profile.index');
// Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
// Route::post('/cambiar-password', [ProfileController::class, 'changePassword'])->name('password.change');

Route::get(
    '/programas-complementarios',
    [App\Http\Controllers\Complementarios\ProgramaComplementarioController::class, 'programasPublicos']
)->name('programas-complementarios.publicos');
Route::get(
    '/programas-complementarios/{id}/inscripcion',
    [App\Http\Controllers\Complementarios\InscripcionComplementarioController::class, 'formularioInscripcion']
)->name('programas-complementarios.inscripcion');
Route::post(
    '/programas-complementarios/{id}/inscripcion',
    [App\Http\Controllers\Complementarios\InscripcionComplementarioController::class, 'procesarInscripcion']
)->name('programas-complementarios.procesar-inscripcion');
// Rutas obsoletas - ahora el documento se sube junto con la inscripción
// Route::get(
//     '/programas-complementarios/{id}/documentos',
//     [App\Http\Controllers\Complementarios\DocumentoComplementarioController::class, 'formularioDocumentos']
// )->name('programas-complementarios.documentos');
// Route::post(
//     '/programas-complementarios/{id}/documentos',
//     [App\Http\Controllers\Complementarios\DocumentoComplementarioController::class, 'subirDocumento']
// )->name('programas-complementarios.subir-documentos');
Route::post(
    '/programas-complementarios/{id}/validar-sofia',
    [App\Http\Controllers\Complementarios\ValidacionSofiaController::class, 'validarSofia']
)->name('programas-complementarios.validar-sofia');
Route::post(
    '/programas-complementarios/{id}/validar-documento',
    [App\Http\Controllers\Complementarios\AspiranteComplementarioController::class, 'validarDocumentos']
)->name('programas-complementarios.validar-documento');
Route::get(
    '/sofia-validation-progress/{progressId}',
    [App\Http\Controllers\Complementarios\ValidacionSofiaController::class, 'getValidationProgress']
)->name('sofia-validation.progress');

Route::middleware('auth')->group(function () {
    Route::get(
        '/mi-perfil',
        [App\Http\Controllers\Complementarios\PerfilComplementarioController::class, 'Perfil']
    )->name('aspirantes.perfil');
});
Route::get('/departamentos/{pais}', [DepartamentoController::class, 'getByPais'])->name('departamentos.by.pais');
Route::get('/municipios/{departamento}', [MunicipioController::class, 'getByDepartamento'])->name('municipios.by.departamento');

<?php

use App\Http\Controllers\AsistenceQrController;
use App\Http\Controllers\Complementarios\AspiranteComplementarioController;
use App\Http\Controllers\Complementarios\InscripcionComplementarioController;
use App\Http\Controllers\Complementarios\PerfilComplementarioController;
use App\Http\Controllers\Complementarios\ProgramaComplementarioController;
use App\Http\Controllers\Complementarios\ValidacionSofiaController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\Inventario\ProductoController;
use App\Http\Controllers\MunicipioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas web de la aplicación.
|
*/

$loadRouteFolders = static function (array $folders, array $middleware = ['web']): void {
    foreach ($folders as $folder) {
        foreach (glob(routes_path($folder) . '/*.php') as $routeFile) {
            Route::middleware($middleware)->group($routeFile);
        }
    }
};

Route::middleware('web')->group(function () {
    Route::get('/', [ProgramaComplementarioController::class, 'programasPublicos'])
        ->name('home');

    Route::prefix('google-drive')
        ->name('google.drive.')
        ->group(function () {
            Route::get('connect', [GoogleDriveController::class, 'connect'])
                ->name('connect');
            Route::get('callback', [GoogleDriveController::class, 'callback'])
                ->name('callback');
            Route::get('test', [GoogleDriveController::class, 'test'])
                ->name('test');
        });

    Route::prefix('programas-complementarios')
        ->name('programas-complementarios.')
        ->group(function () {
            Route::get('/', [ProgramaComplementarioController::class, 'programasPublicos'])
                ->name('publicos');
            Route::get('{id}/inscripcion', [InscripcionComplementarioController::class, 'formularioInscripcion'])
                ->name('inscripcion');
            Route::post('{id}/inscripcion', [InscripcionComplementarioController::class, 'procesarInscripcion'])
                ->name('procesar-inscripcion');
            Route::post('{id}/validar-sofia', [ValidacionSofiaController::class, 'validarSofia'])
                ->name('validar-sofia');
            Route::post('{id}/validar-documento', [AspiranteComplementarioController::class, 'validarDocumentos'])
                ->name('validar-documento');
        });

    Route::prefix('inscripcion')
        ->name('inscripcion.')
        ->group(function () {
            Route::get('/', [InscripcionComplementarioController::class, 'inscripcionGeneral'])
                ->name('general');
            Route::post('/', [InscripcionComplementarioController::class, 'procesarInscripcionGeneral'])
                ->name('procesar');
        });

    Route::get('/programas/{id}', [ProgramaComplementarioController::class, 'verPrograma'])
        ->name('programa_complementario.ver');

    Route::get('/sofia-validation-progress/{progressId}', [ValidacionSofiaController::class, 'getValidationProgress'])
        ->name('sofia-validation.progress');

    Route::get('/departamentos/{pais}', [DepartamentoController::class, 'getByPais'])
        ->name('departamentos.by.pais');
    Route::get('/municipios/{departamento}', [MunicipioController::class, 'getByDepartamento'])
        ->name('municipios.by.departamento');
});

$loadRouteFolders(['autenticacion/public']);

$protectedRouteFolders = [
    'actividades',
    'asistencia',
    'autenticacion/private',
    'caracterizacion',
    'complementarios',
    'entrada_salida',
    'infraestructura',
    'inventario',
    'jornada_carnet',
    'personas',
    'talento-humano',
    'tema_parametro',
    'ubicacion',
    'usuario',
    'websocket_visitantes',
];

$loadRouteFolders($protectedRouteFolders, ['web', 'auth']);

Route::middleware(['web', 'auth'])->group(function () {
    Route::post(
        '/verify-document',
        [AsistenceQrController::class, 'verifyDocument']
    )->name('api.verifyDocument');

    Route::prefix('inventario')
        ->name('inventario.')
        ->group(function () {
            Route::get(
                'productos/{id}/etiqueta',
                [ProductoController::class, 'etiqueta']
            )->name('productos.etiqueta');
        });

    Route::get(
        '/mi-perfil',
        [PerfilComplementarioController::class, 'Perfil']
    )->name('aspirantes.perfil');
});

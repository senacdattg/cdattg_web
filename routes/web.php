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
    // Ruta principal del dashboard Flutter (debe ir antes de otras rutas)
    Route::get('/dashboard-ingreso-salida', function () {
        $indexPath = public_path('dashboard-ingreso-salida/index.html');
        
        if (!file_exists($indexPath)) {
            return response('Dashboard no encontrado. Asegúrate de haber copiado los archivos. Ruta funcionando correctamente.', 404)
                ->header('Content-Type', 'text/plain');
        }
        
        return response()->file($indexPath);
    });

    // Ruta catch-all para assets y rutas internas de Flutter
    Route::get('/dashboard-ingreso-salida/{path}', function ($path) {
        $filePath = public_path("dashboard-ingreso-salida/{$path}");
        
        // Si el archivo existe, servirlo con el Content-Type correcto
        if (file_exists($filePath) && is_file($filePath)) {
            $mimeType = mime_content_type($filePath);
            return response()->file($filePath, [
                'Content-Type' => $mimeType ?: 'application/octet-stream',
            ]);
        }
        
        // Si no existe, devolver index.html (para rutas internas de Flutter)
        $indexPath = public_path('dashboard-ingreso-salida/index.html');
        if (file_exists($indexPath)) {
            return response()->file($indexPath);
        }
        
        abort(404);
    })->where('path', '.*');

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
                ->name('index');

            Route::get('{programa}/inscripcion', [InscripcionComplementarioController::class, 'formularioInscripcion'])
                ->name('inscripcion');
            Route::post('{programa}/inscripcion', [InscripcionComplementarioController::class, 'procesarInscripcion'])
                ->name('procesar-inscripcion');
            Route::post('{programa}/validar-sofia', [ValidacionSofiaController::class, 'validarSofia'])
                ->name('validar-sofia');
            Route::post('{programa}/validar-documento', [AspiranteComplementarioController::class, 'validarDocumentos'])
                ->name('validar-documento');

            Route::get('{programa}', [ProgramaComplementarioController::class, 'verPrograma'])
                ->name('show');
        });

    Route::prefix('inscripcion')
        ->name('inscripcion.')
        ->group(function () {
            Route::get('/', [InscripcionComplementarioController::class, 'inscripcionGeneral'])
                ->name('general');
            Route::post('/', [InscripcionComplementarioController::class, 'procesarInscripcionGeneral'])
                ->name('procesar');
        });

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

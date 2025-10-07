<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\AsistenceQrController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas web de la aplicación.
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Rutas públicas
foreach (glob(routes_path('autenticacion/public') . '/*.php') as $routeFile) {
    include_once $routeFile;
}

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
        'actividades'
    ];

    // Incluir rutas de guías de aprendizaje
    include_once routes_path('web_guias_aprendizaje.php');
    
    // Incluir rutas de resultados de aprendizaje
    include_once routes_path('web_resultados_aprendizaje.php');

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

Route::get('/departamentos/{pais}', [DepartamentoController::class, 'getByPais'])->name('departamentos.by.pais');
Route::get('/municipios/{departamento}', [MunicipioController::class, 'getByDepartamento'])->name('municipios.by.departamento');

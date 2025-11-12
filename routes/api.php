<?php

use App\Http\Controllers\AmbienteController;
use App\Http\Controllers\Api\UbicacionPublicApiController;
use App\Http\Controllers\AsistenciaAprendicesController;
use App\Http\Controllers\BloqueController;
use App\Http\Controllers\CaracterizacionController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EntradaSalidaController;
use App\Http\Controllers\FichaCaracterizacionController;
use App\Http\Controllers\FichaCaracterizacionFlutterController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\ParametroController;
use App\Http\Controllers\PisoController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\AsistenceQrController;
use App\Http\Controllers\RegistroAsistenciaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí se registran todas las rutas API de la aplicación.
| Todos los endpoints son públicos (sin autenticación).
|
*/

// ==========================================
// RUTAS DE PRUEBA
// ==========================================

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando correctamente',
        'timestamp' => now()
    ]);
});

Route::get('/flutter/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Ruta Flutter funcionando sin autenticación',
        'timestamp' => now()
    ]);
});

// ==========================================
// RUTAS DE AUTENTICACIÓN
// ==========================================

Route::post('authenticate', [LoginController::class, 'authenticate']);
Route::post('logout', [LogoutController::class, 'logout']);
Route::get('/user', function (Request $request) {
    return $request->user();
});

// ==========================================
// FICHAS DE CARACTERIZACIÓN - GENERALES
// ==========================================

Route::get('/fichas-caracterizacion/test', [FichaCaracterizacionController::class, 'getAllFichasCaracterizacion']);
Route::get('fichas-caracterizacion/all', [FichaCaracterizacionController::class, 'getAllFichasCaracterizacion']);
Route::get('fichas-caracterizacion/{id}', [FichaCaracterizacionController::class, 'getFichaCaracterizacionById']);
Route::post('fichas-caracterizacion/search', [FichaCaracterizacionController::class, 'searchFichasByNumber']);
Route::get('fichas-caracterizacion/jornada/{id}', [FichaCaracterizacionController::class, 'getFichasCaracterizacionPorJornada']);
Route::get('fichas-caracterizacion/aprendices/{id}', [FichaCaracterizacionController::class, 'getCantidadAprendicesPorFicha']);
Route::get('fichas-caracterizacion/con-aprendices', [FichaCaracterizacionController::class, 'getAllFichasConCantidadAprendices']);

// ==========================================
// FICHAS DE CARACTERIZACIÓN - FLUTTER
// ==========================================

// IMPORTANTE: Las rutas específicas deben ir ANTES de las rutas con parámetros dinámicos
Route::get('/fichas-caracterizacion/flutter/all', [FichaCaracterizacionFlutterController::class, 'getAllFichasCaracterizacion']);
Route::get('/fichas-caracterizacion/flutter/con-aprendices', [FichaCaracterizacionFlutterController::class, 'getAllFichasConAprendices']);
Route::post('/fichas-caracterizacion/flutter/search', [FichaCaracterizacionFlutterController::class, 'searchFichasByNumber']);
Route::get('/fichas-caracterizacion/flutter/jornada/{id}', [FichaCaracterizacionFlutterController::class, 'getFichasCaracterizacionPorJornada']);
Route::get('/fichas-caracterizacion/flutter/aprendices/{id}', [FichaCaracterizacionFlutterController::class, 'getCantidadAprendicesPorFicha']);
Route::get('/fichas-caracterizacion/flutter/{id}', [FichaCaracterizacionFlutterController::class, 'getFichaCaracterizacionById']);

// ==========================================
// SELECTORES DINÁMICOS
// ==========================================

Route::get('/paises', [UbicacionPublicApiController::class, 'paises'])->name('api.paises');

Route::post('/check-cedula', function (Request $request) {
    $request->validate([
        'cedula' => 'required|string|max:20'
    ]);

    $persona = app(\App\Services\PersonaService::class)->buscarPorDocumento(trim($request->cedula));

    if (!$persona) {
        return response()->json([
            'success' => false,
            'message' => 'Cédula disponible',
            'available' => true
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Cédula ya registrada',
        'available' => false
    ]);
})->name('api.check-cedula');

Route::post('/check-celular', function (Request $request) {
    $request->validate([
        'celular' => 'required|string|size:10'
    ]);

    $persona = \App\Models\Persona::where('celular', trim($request->celular))->first();

    if (!$persona) {
        return response()->json([
            'success' => false,
            'message' => 'Celular disponible',
            'available' => true
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Celular ya registrado',
        'available' => false
    ]);
})->name('api.check-celular');

Route::post('/check-telefono', function (Request $request) {
    $request->validate([
        'telefono' => 'required|string|size:7'
    ]);

    $persona = \App\Models\Persona::where('telefono', trim($request->telefono))->first();

    if (!$persona) {
        return response()->json([
            'success' => false,
            'message' => 'Teléfono disponible',
            'available' => true
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Teléfono ya registrado',
        'available' => false
    ]);
})->name('api.check-telefono');

Route::get('/modalidades', function () {
    return \App\Models\Parametro::where('tema_id', function($query) {
        $query->select('id')
              ->from('temas')
              ->where('nombre', 'MODALIDAD_FORMACION');
    })->get(['id', 'name']);
})->name('api.modalidades');

Route::get('/jornadas', function () {
    return \App\Models\JornadaFormacion::all(['id', 'name']);
})->name('api.jornadas');

Route::get('/instructores', function () {
    return \App\Models\Instructor::with('persona:id,primer_nombre,primer_apellido')
        ->get(['id', 'persona_id']);
})->name('api.instructores');

Route::get('/sedes', function () {
    return \App\Models\Sede::all(['id', 'nombre']);
})->name('api.sedes');

Route::get('/programas', function () {
    return \App\Models\ProgramaFormacion::with('sede:id,nombre')
        ->get(['id', 'nombre', 'codigo', 'sede_id']);
})->name('api.programas');

Route::get('/ambientes/{sedeId}', function ($sedeId) {
    return \App\Models\Ambiente::whereHas('piso.bloque', function($query) use ($sedeId) {
        $query->where('sede_id', $sedeId);
    })->with(['piso:id,nombre,bloque_id', 'piso.bloque:id,nombre'])
      ->get(['id', 'nombre', 'piso_id']);
})->name('api.ambientes.por.sede');

// ==========================================
// FICHAS - CONSULTAS ESPECÍFICAS
// ==========================================

Route::get('/fichas/por-programa/{programaId}', function ($programaId) {
    return \App\Models\FichaCaracterizacion::where('programa_formacion_id', $programaId)
        ->get(['id', 'ficha', 'status']);
})->name('api.fichas.por.programa');

Route::get('/fichas/por-sede/{sedeId}', function ($sedeId) {
    return \App\Models\FichaCaracterizacion::where('sede_id', $sedeId)
        ->with('programaFormacion:id,nombre')
        ->get(['id', 'ficha', 'programa_formacion_id', 'status']);
})->name('api.fichas.por.sede');

Route::get('/fichas/por-instructor/{instructorId}', function ($instructorId) {
    return \App\Models\FichaCaracterizacion::where('instructor_id', $instructorId)
        ->with('programaFormacion:id,nombre')
        ->get(['id', 'ficha', 'programa_formacion_id', 'status']);
})->name('api.fichas.por.instructor');

// ==========================================
// FICHAS - ESTADÍSTICAS
// ==========================================

Route::get('/fichas/estadisticas', function () {
    $totalFichas = \App\Models\FichaCaracterizacion::count();
    $fichasActivas = \App\Models\FichaCaracterizacion::where('status', true)->count();
    $fichasInactivas = \App\Models\FichaCaracterizacion::where('status', false)->count();
    $fichasConAprendices = \App\Models\FichaCaracterizacion::has('aprendices')->count();
    $totalAprendices = \App\Models\FichaCaracterizacion::withCount('aprendices')->get()->sum('aprendices_count');

    return [
        'total_fichas' => $totalFichas,
        'fichas_activas' => $fichasActivas,
        'fichas_inactivas' => $fichasInactivas,
        'fichas_con_aprendices' => $fichasConAprendices,
        'total_aprendices' => $totalAprendices,
        'promedio_aprendices_por_ficha' => $totalFichas > 0 ? round($totalAprendices / $totalFichas, 2) : 0
    ];
})->name('api.fichas.estadisticas');

Route::get('/fichas/{id}/info', function ($id) {
    return \App\Models\FichaCaracterizacion::with([
        'programaFormacion:id,nombre,codigo',
        'sede:id,nombre',
        'instructor.persona:id,primer_nombre,primer_apellido',
        'modalidadFormacion:id,name',
        'jornadaFormacion:id,name',
        'ambiente:id,nombre'
    ])->findOrFail($id);
})->name('api.fichas.info');

Route::get('/fichas/{id}/aprendices', function ($id) {
    return \App\Models\FichaCaracterizacion::findOrFail($id)
        ->aprendices()
        ->with('persona:id,primer_nombre,primer_apellido,numero_documento,email,telefono')
        ->get();
})->name('api.fichas.aprendices');

Route::get('/fichas/{id}/estadisticas', function ($id) {
    $ficha = \App\Models\FichaCaracterizacion::findOrFail($id);
    
    return [
        'total_aprendices' => $ficha->contarAprendices(),
        'duracion_dias' => $ficha->duracionEnDias(),
        'duracion_meses' => $ficha->duracionEnMeses(),
        'horas_promedio_dia' => $ficha->horasPromedioPorDia(),
        'porcentaje_avance' => $ficha->porcentajeAvance(),
        'esta_en_curso' => $ficha->estaEnCurso()
    ];
})->name('api.fichas.estadisticas.detalle');

// ==========================================
// FICHAS - VALIDACIONES
// ==========================================

Route::get('/fichas/validar-numero/{numero}', function ($numero) {
    $existe = \App\Models\FichaCaracterizacion::where('ficha', $numero)->exists();
    return ['disponible' => !$existe];
})->name('api.fichas.validar.numero');

Route::get('/fichas/validar-numero/{numero}/{id}', function ($numero, $id) {
    $existe = \App\Models\FichaCaracterizacion::where('ficha', $numero)
        ->where('id', '!=', $id)
        ->exists();
    return ['disponible' => !$existe];
})->name('api.fichas.validar.numero.update');

// ==========================================
// FICHAS - BÚSQUEDAS Y FILTROS
// ==========================================

Route::get('/fichas/buscar', function (\Illuminate\Http\Request $request) {
    $query = $request->get('q', '');
    
    return \App\Models\FichaCaracterizacion::where('ficha', 'like', "%{$query}%")
        ->orWhereHas('programaFormacion', function($q) use ($query) {
            $q->where('nombre', 'like', "%{$query}%");
        })
        ->with('programaFormacion:id,nombre')
        ->limit(10)
        ->get(['id', 'ficha', 'programa_formacion_id', 'status']);
})->name('api.fichas.buscar');

Route::get('/fichas/filtrar', function (\Illuminate\Http\Request $request) {
    $query = \App\Models\FichaCaracterizacion::query();
    
    if ($request->has('estado') && $request->estado !== '') {
        $query->where('status', $request->estado);
    }
    
    if ($request->has('programa_id') && $request->programa_id) {
        $query->where('programa_formacion_id', $request->programa_id);
    }
    
    if ($request->has('sede_id') && $request->sede_id) {
        $query->where('sede_id', $request->sede_id);
    }
    
    if ($request->has('modalidad_id') && $request->modalidad_id) {
        $query->where('modalidad_formacion_id', $request->modalidad_id);
    }
    
    if ($request->has('jornada_id') && $request->jornada_id) {
        $query->where('jornada_id', $request->jornada_id);
    }
    
    return $query->with([
        'programaFormacion:id,nombre',
        'sede:id,nombre',
        'modalidadFormacion:id,name',
        'jornadaFormacion:id,name'
    ])->paginate(15);
})->name('api.fichas.filtrar');

Route::post('/verificar-disponibilidad-fechas-instructor', [FichaCaracterizacionController::class, 'verificarDisponibilidadFechasInstructor'])
    ->name('api.fichas.verificar-disponibilidad-fechas-instructor');

// ==========================================
// CARACTERIZACIÓN
// ==========================================

Route::get('caracterizacion/byInstructor/{id}', [CaracterizacionController::class, 'CaracterizacionByInstructor']);

// ==========================================
// ASISTENCIAS
// ==========================================

Route::post('asistencia/store', [AsistenciaAprendicesController::class, 'store']);
Route::post('asistencia/update', [AsistenciaAprendicesController::class, 'update']);
Route::post('asistencia/novedad', [AsistenciaAprendicesController::class, 'assistenceNovedad']);
Route::get('asistencia/getFicha/{ficha}/{jornada}', [AsistenciaAprendicesController::class, 'getList']);
Route::post('asistencia/updateExitAsistence', [AsistenciaAprendicesController::class, 'updateExitAsistence']);
Route::post('asistencia/updateEntraceAsistence', [AsistenciaAprendicesController::class, 'updateEntraceAsistence']);

// Rutas para registro de asistencias con jornadas y WebSocket en tiempo real
Route::post('/asistencia/entrada', [RegistroAsistenciaController::class, 'registrarEntrada']);
Route::post('/asistencia/salida', [RegistroAsistenciaController::class, 'registrarSalida']);
Route::get('/asistencia/jornada', [RegistroAsistenciaController::class, 'obtenerAsistenciasPorJornada']);
Route::get('/asistencia/fichas', [RegistroAsistenciaController::class, 'obtenerFichasConJornadas']);

// ==========================================
// WEBSOCKETS - RUTAS PÚBLICAS
// ==========================================

Route::get('/websocket/estadisticas', [\App\Http\Controllers\WebSocketVisitantesController::class, 'obtenerEstadisticas']);
Route::post('/websocket/entrada', [\App\Http\Controllers\WebSocketVisitantesController::class, 'registrarEntrada']);
Route::post('/websocket/salida', [\App\Http\Controllers\WebSocketVisitantesController::class, 'registrarSalida']);
Route::get('/websocket/visitantes-actuales', [\App\Http\Controllers\WebSocketVisitantesController::class, 'obtenerVisitantesActuales']);

// ==========================================
// REGISTRO DE PRESENCIA - ESTADÍSTICAS DE PERSONAS DENTRO
// ==========================================

Route::post('/presencia/entrada', [\App\Http\Controllers\PersonaIngresoSalidaController::class, 'registrarEntrada']);
Route::post('/presencia/salida', [\App\Http\Controllers\PersonaIngresoSalidaController::class, 'registrarSalida']);
Route::get('/presencia/estadisticas', [\App\Http\Controllers\PersonaIngresoSalidaController::class, 'estadisticasPersonasDentro']);
Route::get('/presencia/estadisticas/hoy', [\App\Http\Controllers\PersonaIngresoSalidaController::class, 'estadisticasPersonasDentroHoy']);
Route::get('/presencia/personas-dentro', [\App\Http\Controllers\PersonaIngresoSalidaController::class, 'personasDentro']);
Route::get('/presencia/estadisticas/fecha', [\App\Http\Controllers\PersonaIngresoSalidaController::class, 'estadisticasPorFecha']);
Route::get('/presencia/estadisticas/sede/{sedeId}', [\App\Http\Controllers\PersonaIngresoSalidaController::class, 'estadisticasPorSede']);

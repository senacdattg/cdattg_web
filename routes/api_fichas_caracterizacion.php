<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FichaCaracterizacionApiController;
use App\Http\Controllers\FichaCaracterizacionController;

/*
|--------------------------------------------------------------------------
| API Routes para FichaCaracterizacion
|--------------------------------------------------------------------------
|
| Aquí se definen las rutas API para funcionalidades AJAX y consultas
| específicas de fichas de caracterización.
|
*/

// Rutas sin autenticación (middleware removido)
Route::prefix('api')->group(function () {
    
    // Rutas para selectores dinámicos
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

    // Rutas para ambientes por sede
    Route::get('/ambientes/{sedeId}', function ($sedeId) {
        return \App\Models\Ambiente::whereHas('piso.bloque', function($query) use ($sedeId) {
            $query->where('sede_id', $sedeId);
        })->with(['piso:id,nombre,bloque_id', 'piso.bloque:id,nombre'])
          ->get(['id', 'nombre', 'piso_id']);
    })->name('api.ambientes.por.sede');

    // Rutas para consultas específicas de fichas
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

    // Rutas para estadísticas
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

    // Rutas para validaciones
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

    // Rutas para información de fichas específicas
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

    // Rutas para búsquedas
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

    // Rutas para filtros avanzados
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

    // Endpoint para verificar disponibilidad de fechas de instructores
    Route::post('/verificar-disponibilidad-fechas-instructor', [FichaCaracterizacionController::class, 'verificarDisponibilidadFechasInstructor'])
        ->name('api.fichas.verificar-disponibilidad-fechas-instructor');
});

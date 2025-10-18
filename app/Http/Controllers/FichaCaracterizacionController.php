<?php

namespace App\Http\Controllers;

use App\Services\FichaService;
use App\Services\FichaCaracterizacionValidationService;
use App\Repositories\ConfiguracionRepository;
use App\Models\FichaCaracterizacion;
use App\Models\ProgramaFormacion;
use App\Models\InstructorFichaCaracterizacion;
use App\Http\Requests\StoreFichaCaracterizacionRequest;
use App\Http\Requests\UpdateFichaCaracterizacionRequest;
use App\Http\Requests\AsignarInstructoresRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class FichaCaracterizacionController extends Controller
{
    protected FichaService $fichaService;
    protected FichaCaracterizacionValidationService $validationService;
    protected ConfiguracionRepository $configuracionRepo;

    /**
     * Constructor del controlador.
     */
    public function __construct(
        FichaService $fichaService,
        FichaCaracterizacionValidationService $validationService,
        ConfiguracionRepository $configuracionRepo
    ) {
        $this->middleware('auth');
        $this->fichaService = $fichaService;
        $this->validationService = $validationService;
        $this->configuracionRepo = $configuracionRepo;

        $this->middleware('can:VER FICHA CARACTERIZACION')->only(['index', 'show', 'create', 'edit']);
        $this->middleware('can:CREAR FICHA CARACTERIZACION')->only(['store']);
        $this->middleware('can:EDITAR FICHA CARACTERIZACION')->only(['update']);
        $this->middleware('can:ELIMINAR FICHA CARACTERIZACION')->only(['destroy']);
    }

    /**
     * Muestra una lista de todas las fichas de caracterización.
     *
     * Este método recupera todas las fichas de caracterización junto con sus
     * relaciones y las pasa a la vista 'fichas.index'. También incluye datos
     * necesarios para los filtros avanzados.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse La vista que muestra la lista de fichas de caracterización.
     */
    public function index(Request $request)
    {
        try {
            $filtros = $request->only(['search', 'estado', 'programa_id', 'jornada_id', 'regional_id']);
            $filtros['per_page'] = 10;

            $fichas = $this->fichaService->listarConFiltros($filtros);

            // Obtener datos para filtros (con caché)
            $programas = $this->configuracionRepo->obtenerProgramasActivos();
            $instructores = \App\Models\Instructor::with('persona')->orderBy('id', 'desc')->get();
            $ambientes = \App\Models\Ambiente::with('piso.bloque')->orderBy('title', 'asc')->get();
            $sedes = \App\Models\Sede::orderBy('sede', 'asc')->get();
            $modalidades = \App\Models\Parametro::whereHas('parametrosTemas', function($query) {
                $query->where('tema_id', 5);
            })->orderBy('name', 'asc')->get();
            $jornadas = \App\Models\JornadaFormacion::orderBy('jornada', 'asc')->get();

            return view('fichas.index', compact('fichas', 'programas', 'instructores', 'ambientes', 'sedes', 'modalidades', 'jornadas'))
                ->with('filters', $request->all());
        } catch (\Exception $e) {
            Log::error('Error al cargar fichas de caracterización', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()->with('error', 'Error al cargar las fichas de caracterización. Por favor, intente nuevamente.');
        }
    }


    /**
     * Muestra el formulario para crear una nueva ficha de caracterización.
     *
     * Obtiene una lista de programas de formación ordenados alfabéticamente por nombre
     * y los pasa a la vista 'fichas.create'.
     *
     * Muestra el formulario para crear una nueva ficha de caracterización.
     *
     * Obtiene una lista de programas de formación ordenados alfabéticamente por nombre
     * y los pasa a la vista 'fichas.create'.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse La vista para crear una nueva ficha de caracterización con los programas de formación.
     */
    public function create()
    {
        try {
            Log::info('Acceso al formulario de creación de ficha de caracterización', [
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            // Obtener todos los datos necesarios para los selectores
            $programas = ProgramaFormacion::orderBy('nombre', 'asc')->get();
            $instructores = \App\Models\Instructor::with('persona')->orderBy('id', 'desc')->get();
            $ambientes = \App\Models\Ambiente::with('piso.bloque')->orderBy('title', 'asc')->get();
            $sedes = \App\Models\Sede::orderBy('sede', 'asc')->get();
            $modalidades = \App\Models\Parametro::whereHas('parametrosTemas', function($query) {
                $query->where('tema_id', 5);
            })->orderBy('name', 'asc')->get(); // MODALIDADES DE FORMACION (tema_id = 5)
            $jornadas = \App\Models\JornadaFormacion::orderBy('jornada', 'asc')->get();

            Log::info('Datos cargados para creación de ficha', [
                'total_programas' => $programas->count(),
                'total_instructores' => $instructores->count(),
                'total_ambientes' => $ambientes->count(),
                'total_sedes' => $sedes->count(),
                'total_modalidades' => $modalidades->count(),
                'total_jornadas' => $jornadas->count(),
                'user_id' => Auth::id()
            ]);

            return view('fichas.create', compact('programas', 'instructores', 'ambientes', 'sedes', 'modalidades', 'jornadas'));

        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de creación de ficha', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()->with('error', 'Error al cargar el formulario de creación. Por favor, intente nuevamente.');
        }
    }


    /**
     * Almacena una nueva ficha de caracterización en la base de datos.
     *
     * @param StoreFichaCaracterizacionRequest $request La solicitud HTTP que contiene los datos de la ficha.
     *
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta 'fichaCaracterizacion.index' con un mensaje de éxito.
     */
    public function store(StoreFichaCaracterizacionRequest $request)
    {
        Log::info('=== MÉTODO STORE LLAMADO ===', [
            'user_id' => Auth::id(),
            'all_data' => $request->all(),
            'timestamp' => now()
        ]);
        
        try {
            Log::info('Inicio de creación de nueva ficha de caracterización', [
                'user_id' => Auth::id(),
                'request_data' => $request->validated(),
                'timestamp' => now()
            ]);

            // Validar disponibilidad del ambiente
            $datos = $request->validated();
            if (isset($datos['ambiente_id']) && isset($datos['sede_id']) && isset($datos['jornada_id'])) {
                $ambientesOcupados = FichaCaracterizacion::where('ambiente_id', $datos['ambiente_id'])
                    ->where('sede_id', $datos['sede_id'])
                    ->where('jornada_id', $datos['jornada_id'])
                    ->where('status', 1) // Solo fichas activas
                    ->exists();

                if ($ambientesOcupados) {
                    Log::warning('Intento de crear ficha con ambiente ya ocupado', [
                        'ambiente_id' => $datos['ambiente_id'],
                        'sede_id' => $datos['sede_id'],
                        'jornada_id' => $datos['jornada_id'],
                        'user_id' => Auth::id()
                    ]);

                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'El ambiente seleccionado ya está siendo utilizado por otra ficha en la misma sede y jornada.');
                }
            }

            DB::beginTransaction();

            $ficha = new FichaCaracterizacion();
            $ficha->fill($request->validated());
            $ficha->user_create_id = Auth::id();
            $ficha->user_edit_id = Auth::id(); // Agregar user_edit_id para creación

            if ($ficha->save()) {
                // Guardar días de formación si se proporcionaron
                if ($request->has('dias_formacion') && is_array($request->dias_formacion)) {
                    Log::info('Guardando días de formación', [
                        'dias_formacion' => $request->dias_formacion,
                        'horarios_completos' => $request->horarios,
                        'all_request' => $request->all()
                    ]);
                    
                    foreach ($request->dias_formacion as $diaId) {
                        $fichaDia = new \App\Models\FichaDiasFormacion();
                        $fichaDia->ficha_id = $ficha->id;
                        $fichaDia->dia_id = $diaId;
                        
                        // Obtener horarios desde el array
                        $horarios = $request->input('horarios', []);
                        
                        if (isset($horarios[$diaId])) {
                            $fichaDia->hora_inicio = $horarios[$diaId]['hora_inicio'] ?? '08:00:00';
                            $fichaDia->hora_fin = $horarios[$diaId]['hora_fin'] ?? '16:00:00';
                            
                            Log::info("Horario para día {$diaId}", [
                                'hora_inicio' => $fichaDia->hora_inicio,
                                'hora_fin' => $fichaDia->hora_fin
                            ]);
                        } else {
                            // Horarios por defecto
                            $fichaDia->hora_inicio = '08:00:00';
                            $fichaDia->hora_fin = '16:00:00';
                            
                            Log::warning("No se encontraron horarios para día {$diaId}, usando valores por defecto");
                        }
                        
                        $fichaDia->save();
                    }
                }
                
                DB::commit();
                
                Log::info('Ficha de caracterización creada exitosamente', [
                    'ficha_id' => $ficha->id,
                    'numero_ficha' => $ficha->ficha,
                    'programa_id' => $ficha->programa_formacion_id,
                    'dias_formacion' => $request->dias_formacion ?? [],
                    'user_id' => Auth::id()
                ]);

                return redirect()->route('fichaCaracterizacion.index')
                    ->with('success', 'Ficha de caracterización creada exitosamente.');
            }

            DB::rollBack();
            throw new \Exception('Error al guardar la ficha en la base de datos.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear ficha de caracterización', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->validated(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return back()->with('error', 'Ocurrió un error al crear la ficha de caracterización. Por favor, intente nuevamente.')
                ->withInput();
        }
    }


    /**
     * Muestra el formulario de edición para una ficha de caracterización específica.
     *
     * @param string $id El ID de la ficha de caracterización a editar.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse La vista del formulario de edición con la ficha de caracterización y los programas de formación.
     */
    public function edit(string $id)
    {
        try {
            Log::info('Acceso al formulario de edición de ficha de caracterización', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);
            
            // Obtener todos los datos necesarios para los selectores
            $programas = ProgramaFormacion::orderBy('nombre', 'asc')->get();
            $instructores = \App\Models\Instructor::with('persona')->orderBy('id', 'desc')->get();
            $ambientes = \App\Models\Ambiente::with('piso.bloque')->orderBy('title', 'asc')->get();
            $sedes = \App\Models\Sede::orderBy('sede', 'asc')->get();
            $modalidades = \App\Models\Parametro::whereHas('parametrosTemas', function($query) {
                $query->where('tema_id', 5);
            })->orderBy('name', 'asc')->get(); // MODALIDADES DE FORMACION (tema_id = 5)
            $jornadas = \App\Models\JornadaFormacion::orderBy('jornada', 'asc')->get();

            Log::info('Datos cargados para edición de ficha', [
                'ficha_id' => $ficha->id,
                'numero_ficha' => $ficha->ficha,
                'total_programas' => $programas->count(),
                'total_instructores' => $instructores->count(),
                'total_ambientes' => $ambientes->count(),
                'total_sedes' => $sedes->count(),
                'total_modalidades' => $modalidades->count(),
                'total_jornadas' => $jornadas->count(),
                'user_id' => Auth::id()
            ]);

            return view('fichas.edit', compact('ficha', 'programas', 'instructores', 'ambientes', 'sedes', 'modalidades', 'jornadas'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Intento de editar ficha de caracterización inexistente', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'La ficha de caracterización solicitada no existe.');

        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de edición de ficha', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()->with('error', 'Error al cargar el formulario de edición. Por favor, intente nuevamente.');
        }
    }


    /**
     * Actualiza una ficha de caracterización existente.
     *
     * @param UpdateFichaCaracterizacionRequest $request La solicitud HTTP que contiene los datos de la ficha a actualizar.
     * @param string $id El ID de la ficha de caracterización que se va a actualizar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la lista de fichas de caracterización con un mensaje de éxito.
     */
    public function update(UpdateFichaCaracterizacionRequest $request, string $id)
    {
        try {
            Log::info('Inicio de actualización de ficha de caracterización', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'request_data' => $request->validated(),
                'timestamp' => now()
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);

            // Guardar datos originales para el log
            $datosOriginales = [
                'programa_formacion_id' => $ficha->programa_formacion_id,
                'ficha' => $ficha->ficha,
                'fecha_inicio' => $ficha->fecha_inicio,
                'fecha_fin' => $ficha->fecha_fin,
            ];

            DB::beginTransaction();

            $ficha->fill($request->validated());
            $ficha->user_edit_id = Auth::id();

            if ($ficha->save()) {
                DB::commit();
                
                Log::info('Ficha de caracterización actualizada exitosamente', [
                    'ficha_id' => $ficha->id,
                    'datos_originales' => $datosOriginales,
                    'datos_nuevos' => $request->validated(),
                    'user_id' => Auth::id()
                ]);

                return redirect()->route('fichaCaracterizacion.index')
                    ->with('success', 'Ficha de caracterización actualizada exitosamente.');
            }

            DB::rollBack();
            throw new \Exception('Error al actualizar la ficha en la base de datos.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Intento de actualizar ficha de caracterización inexistente', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'La ficha de caracterización solicitada no existe.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al actualizar ficha de caracterización', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->validated(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return back()->with('error', 'Ocurrió un error al actualizar la ficha de caracterización. Por favor, intente nuevamente.')
                ->withInput();
        }
    }


    /**
     * Elimina una ficha de caracterización específica.
     *
     * @param string $id El ID de la ficha de caracterización a eliminar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la lista de fichas de caracterización con un mensaje de éxito.
     */
    public function destroy(string $id)
    {
        try {
            $user = Auth::user();
            
            Log::info('═══════════════════════════════════════════════════════════');
            Log::info('INICIO: Intento de eliminación de ficha de caracterización', [
                'ficha_id' => $id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_roles' => $user->roles->pluck('name')->toArray(),
                'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            // Buscar la ficha
            $ficha = FichaCaracterizacion::findOrFail($id);
            
            Log::info('Ficha encontrada', [
                'ficha_id' => $ficha->id,
                'numero_ficha' => $ficha->ficha,
                'programa_formacion_id' => $ficha->programa_formacion_id,
                'programa_nombre' => $ficha->programaFormacion->nombre ?? 'N/A',
                'instructor_id' => $ficha->instructor_id,
                'sede_id' => $ficha->sede_id,
                'status' => $ficha->status ? 'Activa' : 'Inactiva',
                'fecha_inicio' => $ficha->fecha_inicio ? $ficha->fecha_inicio->format('Y-m-d') : null,
                'fecha_fin' => $ficha->fecha_fin ? $ficha->fecha_fin->format('Y-m-d') : null
            ]);

            // Verificar autorización
            try {
                $this->authorize('delete', $ficha);
                Log::info('✓ Autorización verificada: Usuario autorizado para eliminar la ficha');
            } catch (\Illuminate\Auth\Access\AuthorizationException $authException) {
                Log::error('✗ Autorización denegada', [
                    'user_id' => $user->id,
                    'ficha_id' => $id,
                    'error' => 'El usuario no tiene permiso para eliminar esta ficha',
                    'user_roles' => $user->roles->pluck('name')->toArray(),
                    'required_permission' => 'ELIMINAR FICHA CARACTERIZACION'
                ]);
                throw $authException;
            }

            // Verificar si la ficha tiene aprendices asignados
            $aprendicesCount = $ficha->contarAprendices();
            Log::info('Verificando aprendices asignados', [
                'tiene_aprendices' => $ficha->tieneAprendices(),
                'cantidad_aprendices' => $aprendicesCount
            ]);

            if ($ficha->tieneAprendices()) {
                Log::warning('✗ VALIDACIÓN FALLIDA: Ficha con aprendices asignados', [
                    'ficha_id' => $id,
                    'numero_ficha' => $ficha->ficha,
                    'aprendices_count' => $aprendicesCount,
                    'user_id' => $user->id,
                    'razon' => 'No se puede eliminar una ficha que tiene aprendices asignados'
                ]);

                return redirect()->route('fichaCaracterizacion.index')
                    ->with('error', 'No se puede eliminar la ficha porque tiene ' . $aprendicesCount . ' aprendice(s) asignado(s).');
            }

            Log::info('✓ Validación de aprendices pasada: La ficha no tiene aprendices asignados');

            // Verificar si tiene instructores asignados
            $instructoresCount = $ficha->instructorFicha()->count();
            Log::info('Verificando instructores asignados', [
                'cantidad_instructores' => $instructoresCount
            ]);

            // Verificar si tiene asistencias
            $tieneAsistencias = DB::table('asistencia_aprendices')
                ->join('aprendiz_fichas_caracterizacion', 'asistencia_aprendices.aprendiz_id', '=', 'aprendiz_fichas_caracterizacion.aprendiz_id')
                ->where('aprendiz_fichas_caracterizacion.ficha_id', $id)
                ->exists();
            
            Log::info('Verificando asistencias registradas', [
                'tiene_asistencias' => $tieneAsistencias
            ]);

            if ($tieneAsistencias) {
                Log::warning('✗ VALIDACIÓN FALLIDA: Ficha con asistencias registradas', [
                    'ficha_id' => $id,
                    'numero_ficha' => $ficha->ficha,
                    'razon' => 'No se puede eliminar una ficha que tiene asistencias registradas'
                ]);

                return redirect()->route('fichaCaracterizacion.index')
                    ->with('error', 'No se puede eliminar la ficha porque tiene asistencias registradas.');
            }

            // Guardar información de la ficha antes de eliminar para el log
            $fichaInfo = [
                'id' => $ficha->id,
                'numero_ficha' => $ficha->ficha,
                'programa_formacion_id' => $ficha->programa_formacion_id,
                'programa_nombre' => $ficha->programaFormacion->nombre ?? 'N/A',
                'instructor_id' => $ficha->instructor_id,
                'sede_id' => $ficha->sede_id,
                'sede_nombre' => $ficha->sede->nombre ?? 'N/A',
                'fecha_inicio' => $ficha->fecha_inicio ? $ficha->fecha_inicio->format('Y-m-d') : null,
                'fecha_fin' => $ficha->fecha_fin ? $ficha->fecha_fin->format('Y-m-d') : null,
                'created_at' => $ficha->created_at ? $ficha->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $ficha->updated_at ? $ficha->updated_at->format('Y-m-d H:i:s') : null
            ];

            Log::info('Iniciando transacción de base de datos para eliminar la ficha');
            DB::beginTransaction();

            try {
                $ficha->delete();
                
                DB::commit();
                
                Log::info('═══════════════════════════════════════════════════════════');
                Log::info('✓ ÉXITO: Ficha de caracterización eliminada correctamente', [
                    'ficha_eliminada' => $fichaInfo,
                    'eliminada_por_user_id' => $user->id,
                    'eliminada_por_user_name' => $user->name,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]);
                Log::info('═══════════════════════════════════════════════════════════');

                return redirect()->route('fichaCaracterizacion.index')
                    ->with('success', 'Ficha de caracterización #' . $fichaInfo['numero_ficha'] . ' eliminada exitosamente.');
                    
            } catch (\Exception $dbException) {
                DB::rollBack();
                
                Log::error('✗ ERROR en la base de datos al eliminar la ficha', [
                    'ficha_id' => $id,
                    'error' => $dbException->getMessage(),
                    'file' => $dbException->getFile(),
                    'line' => $dbException->getLine(),
                    'trace' => $dbException->getTraceAsString()
                ]);
                
                throw new \Exception('Error al eliminar la ficha de la base de datos: ' . $dbException->getMessage());
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('═══════════════════════════════════════════════════════════');
            Log::error('✗ ERROR: Ficha de caracterización no encontrada', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
            Log::error('═══════════════════════════════════════════════════════════');

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'La ficha de caracterización solicitada no existe.');

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('═══════════════════════════════════════════════════════════');
            Log::error('✗ ERROR: Acceso denegado - Sin autorización para eliminar', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email ?? 'N/A',
                'user_roles' => Auth::user()->roles->pluck('name')->toArray() ?? [],
                'error' => 'El usuario no tiene los permisos necesarios para eliminar fichas',
                'required_permission' => 'ELIMINAR FICHA CARACTERIZACION',
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
            Log::error('═══════════════════════════════════════════════════════════');

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'No tienes permisos para eliminar fichas de caracterización.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('═══════════════════════════════════════════════════════════');
            Log::error('✗ ERROR GENERAL al eliminar ficha de caracterización', [
                'ficha_id' => $id,
                'error_message' => $e->getMessage(),
                'error_type' => get_class($e),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
            Log::error('═══════════════════════════════════════════════════════════');

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'Ocurrió un error al eliminar la ficha de caracterización. Por favor, intente nuevamente. Error: ' . $e->getMessage());
        }
    }

    /**
     * Búsqueda avanzada de fichas de caracterización con múltiples filtros.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function search(Request $request)
    {
        try {
            Log::info('Búsqueda avanzada de fichas de caracterización', [
                'filters' => $request->all(),
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            $query = FichaCaracterizacion::with([
                'programaFormacion',
                'instructor.persona',
                'ambiente.piso.bloque',
                'modalidadFormacion',
                'sede',
                'jornadaFormacion',
                'aprendices'
            ]);

            // Filtro por término de búsqueda general
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('ficha', 'LIKE', "%{$searchTerm}%")
                      ->orWhereHas('programaFormacion', function ($subQuery) use ($searchTerm) {
                          $subQuery->where('nombre', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('codigo', 'LIKE', "%{$searchTerm}%");
                      })
                      ->orWhereHas('instructor.persona', function ($subQuery) use ($searchTerm) {
                          $subQuery->where('primer_nombre', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('segundo_nombre', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('primer_apellido', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('segundo_apellido', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('numero_documento', 'LIKE', "%{$searchTerm}%");
                      })
                      ->orWhereHas('ambiente', function ($subQuery) use ($searchTerm) {
                          $subQuery->where('title', 'LIKE', "%{$searchTerm}%");
                      });
                });
            }

            // Filtro por programa de formación
            if ($request->filled('programa_id')) {
                $query->where('programa_formacion_id', $request->input('programa_id'));
            }

            // Filtro por instructor
            if ($request->filled('instructor_id')) {
                $query->where('instructor_id', $request->input('instructor_id'));
            }

            // Filtro por ambiente
            if ($request->filled('ambiente_id')) {
                $query->where('ambiente_id', $request->input('ambiente_id'));
            }

            // Filtro por sede
            if ($request->filled('sede_id')) {
                $query->where('sede_id', $request->input('sede_id'));
            }

            // Filtro por modalidad de formación
            if ($request->filled('modalidad_id')) {
                $query->where('modalidad_formacion_id', $request->input('modalidad_id'));
            }

            // Filtro por jornada
            if ($request->filled('jornada_id')) {
                $query->where('jornada_id', $request->input('jornada_id'));
            }

            // Filtro por estado
            if ($request->filled('estado')) {
                $query->where('status', $request->input('estado'));
            }

            // Filtro por rango de fechas de inicio
            if ($request->filled('fecha_inicio_desde')) {
                $query->where('fecha_inicio', '>=', $request->input('fecha_inicio_desde'));
            }

            if ($request->filled('fecha_inicio_hasta')) {
                $query->where('fecha_inicio', '<=', $request->input('fecha_inicio_hasta'));
            }

            // Filtro por rango de fechas de fin
            if ($request->filled('fecha_fin_desde')) {
                $query->where('fecha_fin', '>=', $request->input('fecha_fin_desde'));
            }

            if ($request->filled('fecha_fin_hasta')) {
                $query->where('fecha_fin', '<=', $request->input('fecha_fin_hasta'));
            }

            // Filtro por fichas con/sin aprendices
            if ($request->filled('con_aprendices')) {
                if ($request->input('con_aprendices') == '1') {
                    $query->has('aprendices');
                } else {
                    $query->doesntHave('aprendices');
                }
            }

            // Ordenamiento
            $sortBy = $request->input('sort_by', 'id');
            $sortDirection = $request->input('sort_direction', 'desc');
            $allowedSortFields = ['id', 'ficha', 'fecha_inicio', 'fecha_fin', 'total_horas', 'created_at'];
            
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                $query->orderBy('id', 'desc');
            }

            // Paginación
            $perPage = $request->input('per_page', 10);
            $fichas = $query->paginate($perPage);

            Log::info('Búsqueda avanzada completada', [
                'filters_applied' => $request->all(),
                'resultados_encontrados' => $fichas->count(),
                'total_resultados' => $fichas->total(),
                'user_id' => Auth::id()
            ]);

            // Si es una petición AJAX, devolver JSON
            if ($request->ajax()) {
                $fichasFormateadas = $fichas->map(function ($ficha) {
                    return [
                        'id' => $ficha->id,
                        'ficha' => $ficha->ficha,
                        'status' => $ficha->status,
                        'fecha_inicio' => $ficha->fecha_inicio,
                        'fecha_fin' => $ficha->fecha_fin,
                        'total_horas' => $ficha->total_horas,
                        'programa_formacion' => [
                            'id' => $ficha->programaFormacion->id ?? null,
                            'nombre' => $ficha->programaFormacion->nombre ?? 'N/A',
                            'codigo' => $ficha->programaFormacion->codigo ?? 'N/A',
                        ],
                        'instructor_principal' => [
                            'id' => $ficha->instructor->id ?? null,
                            'persona' => [
                                'id' => $ficha->instructor->persona->id ?? null,
                                'primer_nombre' => $ficha->instructor->persona->primer_nombre ?? 'N/A',
                                'primer_apellido' => $ficha->instructor->persona->primer_apellido ?? 'N/A',
                            ]
                        ],
                        'sede' => [
                            'id' => $ficha->sede->id ?? null,
                            'sede' => $ficha->sede->sede ?? 'N/A',
                        ],
                        'modalidad_formacion' => [
                            'id' => $ficha->modalidadFormacion->id ?? null,
                            'nombre' => $ficha->modalidadFormacion->name ?? 'N/A',
                        ],
                        'ambiente' => [
                            'id' => $ficha->ambiente->id ?? null,
                            'title' => $ficha->ambiente->title ?? 'N/A',
                        ],
                        'aprendices_count' => $ficha->aprendices->count() ?? 0,
                        'created_at' => $ficha->created_at,
                        'updated_at' => $ficha->updated_at,
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => [
                        'fichas' => $fichasFormateadas,
                        'pagination' => [
                            'current_page' => $fichas->currentPage(),
                            'last_page' => $fichas->lastPage(),
                            'per_page' => $fichas->perPage(),
                            'total' => $fichas->total(),
                            'from' => $fichas->firstItem(),
                            'to' => $fichas->lastItem()
                        ]
                    ]
                ]);
            }

            // Para peticiones normales, devolver vista
            return view('fichas.index', compact('fichas'))
                ->with('filters', $request->all());

        } catch (\Exception $e) {
            Log::error('Error en búsqueda avanzada de fichas', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'filters' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al realizar la búsqueda',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error al realizar la búsqueda. Por favor, intente nuevamente.');
        }
    }

    /**
     * Muestra una ficha de caracterización específica.
     *
     * @param string $id El ID de la ficha de caracterización a mostrar.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse La vista que muestra los detalles de la ficha de caracterización.
     */
    public function show(string $id)
    {
        try {
            Log::info('Visualización de ficha de caracterización', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            $ficha = FichaCaracterizacion::with([
                'programaFormacion',
                'instructor.persona',
                'instructorFicha.instructor.persona',
                'jornadaFormacion',
                'ambiente',
                'modalidadFormacion',
                'sede',
                'diasFormacion.dia',
                'aprendices'
            ])->findOrFail($id);

            Log::info('Ficha de caracterización cargada para visualización', [
                'ficha_id' => $ficha->id,
                'numero_ficha' => $ficha->ficha,
                'aprendices_count' => $ficha->aprendices->count(),
                'user_id' => Auth::id()
            ]);

            return view('fichas.show', compact('ficha'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Intento de visualizar ficha de caracterización inexistente', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'La ficha de caracterización solicitada no existe.');

        } catch (\Exception $e) {
            Log::error('Error al cargar ficha de caracterización para visualización', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()->with('error', 'Error al cargar los detalles de la ficha. Por favor, intente nuevamente.');
        }
    }

    /**
     * Obtiene todas las fichas de caracterización con su información completa.
     *
     * Este método recupera todas las fichas de caracterización junto con todas sus relaciones
     * y devuelve una respuesta JSON con la información completa de cada ficha.
     *
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con todas las fichas de caracterización y su información.
     */
    public function getAllFichasCaracterizacion()
    {
        try {
            $fichas = FichaCaracterizacion::with([
                'programaFormacion',
                'instructor.persona',
                'jornadaFormacion',
                'ambiente.piso.bloque',
                'modalidadFormacion',
                'sede',
                'diasFormacion.dia',
                'instructorFicha.instructor.persona'
            ])->where('status', true)->orderBy('id', 'desc')->get();

            $fichasFormateadas = $fichas->map(function ($ficha) {
                return [
                    'id' => $ficha->id,
                    'numero_ficha' => $ficha->ficha,
                    'fecha_inicio' => $ficha->fecha_inicio,
                    'fecha_fin' => $ficha->fecha_fin,
                    'total_horas' => $ficha->total_horas,
                    'status' => $ficha->status,
                    'programa_formacion' => [
                        'id' => $ficha->programaFormacion->id ?? null,
                        'nombre' => $ficha->programaFormacion->nombre ?? 'N/A',
                        'codigo' => $ficha->programaFormacion->codigo ?? 'N/A',
                        'nivel_formacion' => $ficha->programaFormacion->nivelFormacion->name ?? 'N/A',
                    ],
                    'instructor_principal' => [
                        'id' => $ficha->instructor->id ?? null,
                        'persona' => [
                            'id' => $ficha->instructor->persona->id ?? null,
                            'primer_nombre' => $ficha->instructor->persona->primer_nombre ?? 'N/A',
                            'segundo_nombre' => $ficha->instructor->persona->segundo_nombre ?? '',
                            'primer_apellido' => $ficha->instructor->persona->primer_apellido ?? 'N/A',
                            'segundo_apellido' => $ficha->instructor->persona->segundo_apellido ?? '',
                            'tipo_documento' => $ficha->instructor->persona->tipo_documento ?? 'N/A',
                            'numero_documento' => $ficha->instructor->persona->numero_documento ?? 'N/A',
                            'email' => $ficha->instructor->persona->email ?? 'N/A',
                            'telefono' => $ficha->instructor->persona->telefono ?? 'N/A',
                        ]
                    ],
                    'jornada_formacion' => [
                        'id' => $ficha->jornadaFormacion->id ?? null,
                        'jornada' => $ficha->jornadaFormacion->jornada ?? 'N/A',
                    ],
                    'ambiente' => [
                        'id' => $ficha->ambiente->id ?? null,
                        'nombre' => $ficha->ambiente->title ?? 'N/A',
                        'piso' => [
                            'id' => $ficha->ambiente->piso->id ?? null,
                            'piso' => $ficha->ambiente->piso->piso ?? 'N/A',
                            'bloque' => [
                                'id' => $ficha->ambiente->piso->bloque->id ?? null,
                                'nombre' => $ficha->ambiente->piso->bloque->bloque ?? 'N/A',
                            ] ?? null,
                        ] ?? null,
                    ] ?? null,
                    'modalidad_formacion' => [
                        'id' => $ficha->modalidadFormacion->id ?? null,
                        'nombre' => $ficha->modalidadFormacion->name ?? 'N/A',
                    ] ?? null,
                    'sede' => [
                        'id' => $ficha->sede->id ?? null,
                        'sede' => $ficha->sede->sede ?? 'N/A',
                        'direccion' => $ficha->sede->direccion ?? 'N/A',
                    ] ?? null,
                    'dias_formacion' => $ficha->diasFormacion->map(function ($dia) {
                        return [
                            'id' => $dia->id,
                            'hora_inicio' => $dia->hora_inicio,
                            'hora_fin' => $dia->hora_fin,
                            'dia' => [
                                'id' => $dia->dia->id ?? null,
                                'nombre' => $dia->dia->name ?? 'N/A',
                            ] ?? null,
                        ];
                    }),
                    'instructores_asignados' => $ficha->instructorFicha->map(function ($instructorFicha) {
                        return [
                            'id' => $instructorFicha->id,
                            'fecha_inicio' => $instructorFicha->fecha_inicio,
                            'fecha_fin' => $instructorFicha->fecha_fin,
                            'total_horas' => $instructorFicha->total_horas_instructor,
                            'instructor' => [
                                'id' => $instructorFicha->instructor->id ?? null,
                                'persona' => [
                                    'id' => $instructorFicha->instructor->persona->id ?? null,
                                    'primer_nombre' => $instructorFicha->instructor->persona->primer_nombre ?? 'N/A',
                                    'segundo_nombre' => $instructorFicha->instructor->persona->segundo_nombre ?? '',
                                    'primer_apellido' => $instructorFicha->instructor->persona->primer_apellido ?? 'N/A',
                                    'segundo_apellido' => $instructorFicha->instructor->persona->segundo_apellido ?? '',
                                    'tipo_documento' => $instructorFicha->instructor->persona->tipo_documento ?? 'N/A',
                                    'numero_documento' => $instructorFicha->instructor->persona->numero_documento ?? 'N/A',
                                    'email' => $instructorFicha->instructor->persona->email ?? 'N/A',
                                    'telefono' => $instructorFicha->instructor->persona->telefono ?? 'N/A',
                                ] ?? null,
                            ] ?? null,
                        ];
                    }),
                    'created_at' => $ficha->created_at,
                    'updated_at' => $ficha->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Fichas de caracterización obtenidas exitosamente',
                'data' => $fichasFormateadas,
                'total' => $fichasFormateadas->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las fichas de caracterización',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene una ficha de caracterización específica por ID con su información completa.
     *
     * Este método recupera una ficha de caracterización específica junto con todas sus relaciones
     * y devuelve una respuesta JSON con la información completa de la ficha.
     *
     * @param int $id El ID de la ficha de caracterización.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con la ficha de caracterización y su información.
     */
    public function getFichaCaracterizacionById($id)
    {
        try {
            $ficha = FichaCaracterizacion::with([
                'programaFormacion',
                'instructor.persona',
                'jornadaFormacion',
                'ambiente.piso.bloque',
                'modalidadFormacion',
                'sede',
                'diasFormacion.dia',
                'instructorFicha.instructor.persona'
            ])->where('status', true)->find($id);

            if (!$ficha) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ficha de caracterización no encontrada'
                ], 404);
            }

            $fichaFormateada = [
                'id' => $ficha->id,
                'numero_ficha' => $ficha->ficha,
                'fecha_inicio' => $ficha->fecha_inicio,
                'fecha_fin' => $ficha->fecha_fin,
                'total_horas' => $ficha->total_horas,
                'status' => $ficha->status,
                'programa_formacion' => [
                    'id' => $ficha->programaFormacion->id ?? null,
                    'nombre' => $ficha->programaFormacion->nombre ?? 'N/A',
                    'codigo' => $ficha->programaFormacion->codigo ?? 'N/A',
                    'nivel_formacion' => $ficha->programaFormacion->nivel_formacion ?? 'N/A',
                ],
                'instructor_principal' => [
                    'id' => $ficha->instructor->id ?? null,
                    'persona' => [
                        'id' => $ficha->instructor->persona->id ?? null,
                        'primer_nombre' => $ficha->instructor->persona->primer_nombre ?? 'N/A',
                        'segundo_nombre' => $ficha->instructor->persona->segundo_nombre ?? '',
                        'primer_apellido' => $ficha->instructor->persona->primer_apellido ?? 'N/A',
                        'segundo_apellido' => $ficha->instructor->persona->segundo_apellido ?? '',
                        'tipo_documento' => $ficha->instructor->persona->tipo_documento ?? 'N/A',
                        'numero_documento' => $ficha->instructor->persona->numero_documento ?? 'N/A',
                        'email' => $ficha->instructor->persona->email ?? 'N/A',
                        'telefono' => $ficha->instructor->persona->telefono ?? 'N/A',
                    ]
                ],
                'jornada_formacion' => [
                    'id' => $ficha->jornadaFormacion->id ?? null,
                    'jornada' => $ficha->jornadaFormacion->jornada ?? 'N/A',
                ],
                'ambiente' => [
                    'id' => $ficha->ambiente->id ?? null,
                    'nombre' => $ficha->ambiente->title ?? 'N/A',
                    'piso' => [
                        'id' => $ficha->ambiente->piso->id ?? null,
                        'piso' => $ficha->ambiente->piso->piso ?? 'N/A',
                        'bloque' => [
                            'id' => $ficha->ambiente->piso->bloque->id ?? null,
                            'nombre' => $ficha->ambiente->piso->bloque->bloque ?? 'N/A',
                        ] ?? null,
                    ] ?? null,
                ] ?? null,
                'modalidad_formacion' => [
                    'id' => $ficha->modalidadFormacion->id ?? null,
                    'nombre' => $ficha->modalidadFormacion->name ?? 'N/A',
                ] ?? null,
                'sede' => [
                    'id' => $ficha->sede->id ?? null,
                    'sede' => $ficha->sede->sede ?? 'N/A',
                    'direccion' => $ficha->sede->direccion ?? 'N/A',
                ] ?? null,
                'dias_formacion' => $ficha->diasFormacion->map(function ($dia) {
                    return [
                        'id' => $dia->id,
                        'hora_inicio' => $dia->hora_inicio,
                        'hora_fin' => $dia->hora_fin,
                        'dia' => [
                            'id' => $dia->dia->id ?? null,
                            'nombre' => $dia->dia->name ?? 'N/A',
                        ] ?? null,
                    ];
                }),
                'instructores_asignados' => $ficha->instructorFicha->map(function ($instructorFicha) {
                    return [
                        'id' => $instructorFicha->id,
                        'fecha_inicio' => $instructorFicha->fecha_inicio,
                        'fecha_fin' => $instructorFicha->fecha_fin,
                        'total_horas_ficha' => $instructorFicha->total_horas_ficha,
                        'instructor' => [
                            'id' => $instructorFicha->instructor->id ?? null,
                            'persona' => [
                                'id' => $instructorFicha->instructor->persona->id ?? null,
                                'primer_nombre' => $instructorFicha->instructor->persona->primer_nombre ?? 'N/A',
                                'segundo_nombre' => $instructorFicha->instructor->persona->segundo_nombre ?? '',
                                'primer_apellido' => $instructorFicha->instructor->persona->primer_apellido ?? 'N/A',
                                'segundo_apellido' => $instructorFicha->instructor->persona->segundo_apellido ?? '',
                                'tipo_documento' => $instructorFicha->instructor->persona->tipo_documento ?? 'N/A',
                                'numero_documento' => $instructorFicha->instructor->persona->numero_documento ?? 'N/A',
                                'email' => $instructorFicha->instructor->persona->email ?? 'N/A',
                                'telefono' => $instructorFicha->instructor->persona->telefono ?? 'N/A',
                            ] ?? null,
                        ] ?? null,
                    ];
                }),
                'created_at' => $ficha->created_at,
                'updated_at' => $ficha->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Ficha de caracterización obtenida exitosamente',
                'data' => $fichaFormateada
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la ficha de caracterización',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene la cantidad de aprendices asociados a una ficha de caracterización por su ID.
     *
     * @param int $id El ID de la ficha de caracterización.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con la cantidad de aprendices.
     */
    public function getCantidadAprendicesPorFicha($id)
    {
        try {
            // Se asume que existe una relación 'aprendices' en el modelo FichaCaracterizacion
            $ficha = FichaCaracterizacion::withCount('aprendices')->find($id);

            if (!$ficha) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ficha de caracterización no encontrada',
                    'cantidad_aprendices' => 0
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cantidad de aprendices obtenida exitosamente',
                'ficha_id' => $ficha->id,
                'cantidad_aprendices' => $ficha->aprendices_count
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la cantidad de aprendices',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Busca fichas de caracterización por número de ficha.
     *
     * Este método permite buscar fichas de caracterización por su número de ficha
     * y devuelve una respuesta JSON con las fichas encontradas.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene el número de ficha.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con las fichas encontradas.
     */
    public function searchFichasByNumber(Request $request)
    {
        try {
            $request->validate([
                'numero_ficha' => 'required|string|max:255',
            ]);

            $numeroFicha = $request->input('numero_ficha');

            $fichas = FichaCaracterizacion::with([
                'programaFormacion',
                'instructor.persona',
                'jornadaFormacion',
                'ambiente.piso.bloque',
                'modalidadFormacion',
                'sede',
                'diasFormacion.dia',
                'instructorFicha.instructor.persona'
            ])->where('status', true)
              ->where('ficha', 'LIKE', "%{$numeroFicha}%")
              ->orderBy('id', 'desc')
              ->get();

            if ($fichas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron fichas de caracterización con el número proporcionado',
                    'data' => []
                ], 404);
            }

            $fichasFormateadas = $fichas->map(function ($ficha) {
                return [
                    'id' => $ficha->id,
                    'numero_ficha' => $ficha->ficha,
                    'fecha_inicio' => $ficha->fecha_inicio,
                    'fecha_fin' => $ficha->fecha_fin,
                    'total_horas' => $ficha->total_horas,
                    'status' => $ficha->status,
                    'programa_formacion' => [
                        'id' => $ficha->programaFormacion->id ?? null,
                        'nombre' => $ficha->programaFormacion->nombre ?? 'N/A',
                        'codigo' => $ficha->programaFormacion->codigo ?? 'N/A',
                        'nivel_formacion' => $ficha->programaFormacion->nivel_formacion ?? 'N/A',
                    ],
                    'instructor_principal' => [
                        'id' => $ficha->instructor->id ?? null,
                        'persona' => [
                            'id' => $ficha->instructor->persona->id ?? null,
                            'primer_nombre' => $ficha->instructor->persona->primer_nombre ?? 'N/A',
                            'segundo_nombre' => $ficha->instructor->persona->segundo_nombre ?? '',
                            'primer_apellido' => $ficha->instructor->persona->primer_apellido ?? 'N/A',
                            'segundo_apellido' => $ficha->instructor->persona->segundo_apellido ?? '',
                            'tipo_documento' => $ficha->instructor->persona->tipo_documento ?? 'N/A',
                            'numero_documento' => $ficha->instructor->persona->numero_documento ?? 'N/A',
                            'email' => $ficha->instructor->persona->email ?? 'N/A',
                            'telefono' => $ficha->instructor->persona->telefono ?? 'N/A',
                        ]
                    ],
                    'jornada_formacion' => [
                        'id' => $ficha->jornadaFormacion->id ?? null,
                        'jornada' => $ficha->jornadaFormacion->jornada ?? 'N/A',
                    ],
                    'ambiente' => [
                        'id' => $ficha->ambiente->id ?? null,
                        'nombre' => $ficha->ambiente->title ?? 'N/A',
                        'piso' => [
                            'id' => $ficha->ambiente->piso->id ?? null,
                            'piso' => $ficha->ambiente->piso->piso ?? 'N/A',
                            'bloque' => [
                                'id' => $ficha->ambiente->piso->bloque->id ?? null,
                                'nombre' => $ficha->ambiente->piso->bloque->bloque ?? 'N/A',
                            ] ?? null,
                        ] ?? null,
                    ] ?? null,
                    'modalidad_formacion' => [
                        'id' => $ficha->modalidadFormacion->id ?? null,
                        'nombre' => $ficha->modalidadFormacion->name ?? 'N/A',
                    ] ?? null,
                    'sede' => [
                        'id' => $ficha->sede->id ?? null,
                        'sede' => $ficha->sede->sede ?? 'N/A',
                        'direccion' => $ficha->sede->direccion ?? 'N/A',
                    ] ?? null,
                    'dias_formacion' => $ficha->diasFormacion->map(function ($dia) {
                        return [
                            'id' => $dia->id,
                            'hora_inicio' => $dia->hora_inicio,
                            'hora_fin' => $dia->hora_fin,
                            'dia' => [
                                'id' => $dia->dia->id ?? null,
                                'nombre' => $dia->dia->name ?? 'N/A',
                            ] ?? null,
                        ];
                    }),
                    'instructores_asignados' => $ficha->instructorFicha->map(function ($instructorFicha) {
                        return [
                            'id' => $instructorFicha->id,
                            'fecha_inicio' => $instructorFicha->fecha_inicio,
                            'fecha_fin' => $instructorFicha->fecha_fin,
                            'total_horas_ficha' => $instructorFicha->total_horas_ficha,
                            'instructor' => [
                                'id' => $instructorFicha->instructor->id ?? null,
                                'persona' => [
                                    'id' => $instructorFicha->instructor->persona->id ?? null,
                                    'primer_nombre' => $instructorFicha->instructor->persona->primer_nombre ?? 'N/A',
                                    'segundo_nombre' => $instructorFicha->instructor->persona->segundo_nombre ?? '',
                                    'primer_apellido' => $instructorFicha->instructor->persona->primer_apellido ?? 'N/A',
                                    'segundo_apellido' => $instructorFicha->instructor->persona->segundo_apellido ?? '',
                                    'tipo_documento' => $instructorFicha->instructor->persona->tipo_documento ?? 'N/A',
                                    'numero_documento' => $instructorFicha->instructor->persona->numero_documento ?? 'N/A',
                                    'email' => $instructorFicha->instructor->persona->email ?? 'N/A',
                                    'telefono' => $instructorFicha->instructor->persona->telefono ?? 'N/A',
                                ] ?? null,
                            ] ?? null,
                        ];
                    }),
                    'created_at' => $ficha->created_at,
                    'updated_at' => $ficha->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Fichas de caracterización encontradas exitosamente',
                'data' => $fichasFormateadas,
                'total' => $fichasFormateadas->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar las fichas de caracterización',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene las fichas de caracterización filtradas por jornada de formación.
     *
     * Este método permite obtener todas las fichas de caracterización que pertenecen
     * a una jornada específica y devuelve una respuesta JSON con la información completa
     * de cada ficha.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene el ID de la jornada.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con las fichas encontradas.
     */
    public function getFichasCaracterizacionPorJornada(Request $request)
    {
        try {
            $jornadaId = $request->id;

            $fichas = FichaCaracterizacion::with([
                'programaFormacion',
                'instructor.persona',
                'jornadaFormacion',
                'ambiente.piso.bloque',
                'modalidadFormacion',
                'sede',
                'diasFormacion.dia',
                'instructorFicha.instructor.persona'
            ])->withCount('aprendices')
              ->where('status', true)
              ->where('id', $jornadaId)
              ->orderBy('id', 'desc')
              ->get();

            if ($fichas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron fichas de caracterización para la jornada especificada',
                    'data' => [],
                    'id' => $jornadaId
                ], 404);
            }

            $fichasFormateadas = $fichas->map(function ($ficha) {
                return [
                    'id' => $ficha->id,
                    'numero_ficha' => $ficha->ficha,
                    'fecha_inicio' => $ficha->fecha_inicio,
                    'fecha_fin' => $ficha->fecha_fin,
                    'total_horas' => $ficha->total_horas,
                    'status' => $ficha->status,
                    'cantidad_aprendices' => $ficha->aprendices_count,
                    'programa_formacion' => [
                        'id' => $ficha->programaFormacion->id ?? null,
                        'nombre' => $ficha->programaFormacion->nombre ?? 'N/A',
                        'codigo' => $ficha->programaFormacion->codigo ?? 'N/A',
                        'nivel_formacion' => $ficha->programaFormacion->nivelFormacion->name ?? 'N/A',
                    ],
                    'instructor_principal' => [
                        'id' => $ficha->instructor->id ?? null,
                        'persona' => [
                            'id' => $ficha->instructor->persona->id ?? null,
                            'primer_nombre' => $ficha->instructor->persona->primer_nombre ?? 'N/A',
                            'segundo_nombre' => $ficha->instructor->persona->segundo_nombre ?? '',
                            'primer_apellido' => $ficha->instructor->persona->primer_apellido ?? 'N/A',
                            'segundo_apellido' => $ficha->instructor->persona->segundo_apellido ?? '',
                            'tipo_documento' => $ficha->instructor->persona->tipo_documento ?? 'N/A',
                            'numero_documento' => $ficha->instructor->persona->numero_documento ?? 'N/A',
                            'email' => $ficha->instructor->persona->email ?? 'N/A',
                            'telefono' => $ficha->instructor->persona->telefono ?? 'N/A',
                        ]
                    ],
                    'jornada_formacion' => [
                        'id' => $ficha->jornadaFormacion->id ?? null,
                        'jornada' => $ficha->jornadaFormacion->jornada ?? 'N/A',
                    ],
                    'ambiente' => [
                        'id' => $ficha->ambiente->id ?? null,
                        'nombre' => $ficha->ambiente->title ?? 'N/A',
                        'piso' => [
                            'id' => $ficha->ambiente->piso->id ?? null,
                            'piso' => $ficha->ambiente->piso->piso ?? 'N/A',
                            'bloque' => [
                                'id' => $ficha->ambiente->piso->bloque->id ?? null,
                                'nombre' => $ficha->ambiente->piso->bloque->bloque ?? 'N/A',
                            ] ?? null,
                        ] ?? null,
                    ] ?? null,
                    'modalidad_formacion' => [
                        'id' => $ficha->modalidadFormacion->id ?? null,
                        'nombre' => $ficha->modalidadFormacion->name ?? 'N/A',
                    ] ?? null,
                    'sede' => [
                        'id' => $ficha->sede->id ?? null,
                        'sede' => $ficha->sede->sede ?? 'N/A',
                        'direccion' => $ficha->sede->direccion ?? 'N/A',
                    ] ?? null,
                    'dias_formacion' => $ficha->diasFormacion->map(function ($dia) {
                        return [
                            'id' => $dia->id,
                            'hora_inicio' => $dia->hora_inicio,
                            'hora_fin' => $dia->hora_fin,
                            'dia' => [
                                'id' => $dia->dia->id ?? null,
                                'nombre' => $dia->dia->name ?? 'N/A',
                            ] ?? null,
                        ];
                    }),
                    'instructores_asignados' => $ficha->instructorFicha->map(function ($instructorFicha) {
                        return [
                            'id' => $instructorFicha->id,
                            'fecha_inicio' => $instructorFicha->fecha_inicio,
                            'fecha_fin' => $instructorFicha->fecha_fin,
                            'total_horas' => $instructorFicha->total_horas_instructor,
                            'instructor' => [
                                'id' => $instructorFicha->instructor->id ?? null,
                                'persona' => [
                                    'id' => $instructorFicha->instructor->persona->id ?? null,
                                    'primer_nombre' => $instructorFicha->instructor->persona->primer_nombre ?? 'N/A',
                                    'segundo_nombre' => $instructorFicha->instructor->persona->segundo_nombre ?? '',
                                    'primer_apellido' => $instructorFicha->instructor->persona->primer_apellido ?? 'N/A',
                                    'segundo_apellido' => $instructorFicha->instructor->persona->segundo_apellido ?? '',
                                    'tipo_documento' => $instructorFicha->instructor->persona->tipo_documento ?? 'N/A',
                                    'numero_documento' => $instructorFicha->instructor->persona->numero_documento ?? 'N/A',
                                    'email' => $instructorFicha->instructor->persona->email ?? 'N/A',
                                    'telefono' => $instructorFicha->instructor->persona->telefono ?? 'N/A',
                                ] ?? null,
                            ] ?? null,
                        ];
                    }),
                    'created_at' => $ficha->created_at,
                    'updated_at' => $ficha->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Fichas de caracterización obtenidas exitosamente por jornada',
                'data' => $fichasFormateadas,
                'total' => $fichasFormateadas->count(),
                'jornada_id' => $jornadaId
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las fichas de caracterización por jornada',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene estadísticas generales de las fichas de caracterización.
     *
     * @return \Illuminate\Http\JsonResponse Estadísticas de fichas de caracterización.
     */
    public function getEstadisticasFichas()
    {
        try {
            Log::info('Solicitud de estadísticas de fichas de caracterización', [
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            $totalFichas = FichaCaracterizacion::count();
            $fichasActivas = FichaCaracterizacion::where('status', 1)->count();
            $fichasInactivas = FichaCaracterizacion::where('status', 0)->count();
            $fichasConAprendices = FichaCaracterizacion::has('aprendices')->count();
            $fichasSinAprendices = FichaCaracterizacion::doesntHave('aprendices')->count();
            $totalAprendices = FichaCaracterizacion::withCount('aprendices')->get()->sum('aprendices_count');

            $estadisticas = [
                'total_fichas' => $totalFichas,
                'fichas_activas' => $fichasActivas,
                'fichas_inactivas' => $fichasInactivas,
                'fichas_con_aprendices' => $fichasConAprendices,
                'fichas_sin_aprendices' => $fichasSinAprendices,
                'total_aprendices' => $totalAprendices,
                'promedio_aprendices_por_ficha' => $fichasConAprendices > 0 ? round($totalAprendices / $fichasConAprendices, 2) : 0
            ];

            Log::info('Estadísticas de fichas calculadas exitosamente', [
                'estadisticas' => $estadisticas,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas obtenidas exitosamente',
                'data' => $estadisticas
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al calcular estadísticas de fichas', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las estadísticas de fichas',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Cambia el estado de una ficha (activar/desactivar).
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id El ID de la ficha.
     * @return \Illuminate\Http\JsonResponse Resultado del cambio de estado.
     */
    public function cambiarEstadoFicha(Request $request, string $id)
    {
        try {
            Log::info('Cambio de estado de ficha de caracterización', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'timestamp' => now()
            ]);

            $request->validate([
                'status' => 'required|boolean'
            ], [
                'status.required' => 'El estado es obligatorio.',
                'status.boolean' => 'El estado debe ser verdadero o falso.'
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);
            $estadoAnterior = $ficha->status;
            
            DB::beginTransaction();

            $ficha->status = $request->input('status');
            $ficha->user_edit_id = Auth::id();

            if ($ficha->save()) {
                DB::commit();
                
                $mensaje = $ficha->status ? 'Ficha activada exitosamente' : 'Ficha desactivada exitosamente';
                
                Log::info('Estado de ficha cambiado exitosamente', [
                    'ficha_id' => $ficha->id,
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo' => $ficha->status,
                    'user_id' => Auth::id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $mensaje,
                    'data' => [
                        'ficha_id' => $ficha->id,
                        'numero_ficha' => $ficha->ficha,
                        'estado_anterior' => $estadoAnterior,
                        'estado_nuevo' => $ficha->status
                    ]
                ], 200);
            }

            DB::rollBack();
            throw new \Exception('Error al cambiar el estado de la ficha');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación al cambiar estado de ficha', [
                'ficha_id' => $id,
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Intento de cambiar estado de ficha inexistente', [
                'ficha_id' => $id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'La ficha solicitada no existe'
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al cambiar estado de ficha', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado de la ficha',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica si una ficha tiene asistencias registradas.
     *
     * @param int $fichaId El ID de la ficha.
     * @return bool True si tiene asistencias, false en caso contrario.
     */
    private function fichaTieneAsistencias(int $fichaId): bool
    {
        try {
            // Buscar asistencias relacionadas con aprendices de esta ficha
            $tieneAsistencias = DB::table('asistencia_aprendices')
                ->join('aprendiz_fichas_caracterizacion', 'asistencia_aprendices.aprendiz_id', '=', 'aprendiz_fichas_caracterizacion.aprendiz_id')
                ->where('aprendiz_fichas_caracterizacion.ficha_id', $fichaId)
                ->exists();

            return $tieneAsistencias;

        } catch (\Exception $e) {
            Log::error('Error al verificar asistencias de ficha', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage()
            ]);
            
            // En caso de error, asumir que sí tiene asistencias para ser conservador
            return true;
        }
    }

    /**
     * Valida una ficha de caracterización usando el servicio de validación.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validarFicha(Request $request)
    {
        try {
            $validator = new FichaCaracterizacionValidationService();
            
            $datos = $request->all();
            $excluirFichaId = $request->input('excluir_ficha_id');
            
            $resultado = $validator->validarFichaCompleta($datos, $excluirFichaId);
            
            return response()->json($resultado);

        } catch (\Exception $e) {
            Log::error('Error al validar ficha', [
                'error' => $e->getMessage(),
                'datos' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'valido' => false,
                'mensaje' => 'Error interno al validar la ficha.',
                'errores' => ['Error interno en la validación'],
                'advertencias' => []
            ], 500);
        }
    }

    /**
     * Valida la disponibilidad de un ambiente en un rango de fechas.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validarDisponibilidadAmbiente(Request $request)
    {
        try {
            $request->validate([
                'ambiente_id' => 'required|integer|exists:ambientes,id',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after:fecha_inicio',
                'excluir_ficha_id' => 'nullable|integer'
            ]);

            // Validar disponibilidad del ambiente directamente
            $ambientesOcupados = FichaCaracterizacion::where('ambiente_id', $request->ambiente_id)
                ->where('status', 1)
                ->where(function($query) use ($request) {
                    $query->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
                          ->orWhereBetween('fecha_fin', [$request->fecha_inicio, $request->fecha_fin])
                          ->orWhere(function($subQuery) use ($request) {
                              $subQuery->where('fecha_inicio', '<=', $request->fecha_inicio)
                                       ->where('fecha_fin', '>=', $request->fecha_fin);
                          });
                });
            
            if ($request->excluir_ficha_id) {
                $ambientesOcupados->where('id', '!=', $request->excluir_ficha_id);
            }
            
            $resultado = [
                'valido' => !$ambientesOcupados->exists(),
                'mensaje' => $ambientesOcupados->exists() 
                    ? 'El ambiente no está disponible en el rango de fechas especificado'
                    : 'El ambiente está disponible'
            ];
            
            return response()->json($resultado);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Error de validación en los datos enviados.',
                'errores' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al validar disponibilidad de ambiente', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'valido' => false,
                'mensaje' => 'Error interno al validar disponibilidad del ambiente.'
            ], 500);
        }
    }

    /**
     * Valida la disponibilidad de un instructor en un rango de fechas.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validarDisponibilidadInstructor(Request $request)
    {
        try {
            $request->validate([
                'instructor_id' => 'required|integer|exists:instructors,id',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after:fecha_inicio',
                'excluir_ficha_id' => 'nullable|integer'
            ]);

            // Validar disponibilidad del instructor directamente
            $instructoresOcupados = InstructorFichaCaracterizacion::where('instructor_id', $request->instructor_id)
                ->where(function($query) use ($request) {
                    $query->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
                          ->orWhereBetween('fecha_fin', [$request->fecha_inicio, $request->fecha_fin])
                          ->orWhere(function($subQuery) use ($request) {
                              $subQuery->where('fecha_inicio', '<=', $request->fecha_inicio)
                                       ->where('fecha_fin', '>=', $request->fecha_fin);
                          });
                });
            
            if ($request->excluir_ficha_id) {
                $instructoresOcupados->where('ficha_id', '!=', $request->excluir_ficha_id);
            }
            
            $resultado = [
                'valido' => !$instructoresOcupados->exists(),
                'mensaje' => $instructoresOcupados->exists() 
                    ? 'El instructor no está disponible en el rango de fechas especificado'
                    : 'El instructor está disponible'
            ];
            
            return response()->json($resultado);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Error de validación en los datos enviados.',
                'errores' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al validar disponibilidad de instructor', [
                'error' => $e->getMessage(),
                'datos' => $request->all()
            ]);

            return response()->json([
                'valido' => false,
                'mensaje' => 'Error interno al validar disponibilidad del instructor.'
            ], 500);
        }
    }

    /**
     * Valida si una ficha puede ser eliminada.
     *
     * @param int $id ID de la ficha
     * @return \Illuminate\Http\JsonResponse
     */
    public function validarEliminacionFicha(string $id)
    {
        try {
            $validator = new FichaCaracterizacionValidationService();
            $resultado = $validator->validarEliminacionFicha($id);
            
            return response()->json($resultado);

        } catch (\Exception $e) {
            Log::error('Error al validar eliminación de ficha', [
                'ficha_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'valido' => false,
                'mensaje' => 'Error interno al validar eliminación de la ficha.',
                'errores' => ['Error interno en la validación']
            ], 500);
        }
    }

    /**
     * Valida si una ficha puede ser editada.
     *
     * @param int $id ID de la ficha
     * @return \Illuminate\Http\JsonResponse
     */
    public function validarEdicionFicha(string $id)
    {
        try {
            $validator = new FichaCaracterizacionValidationService();
            $resultado = $validator->validarEdicionFicha($id);
            
            return response()->json($resultado);

        } catch (\Exception $e) {
            Log::error('Error al validar edición de ficha', [
                'ficha_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'valido' => false,
                'mensaje' => 'Error interno al validar edición de la ficha.',
                'errores' => ['Error interno en la validación'],
                'advertencias' => []
            ], 500);
        }
    }

    /**
     * Muestra la vista para gestionar instructores de una ficha.
     *
     * @param int $id El ID de la ficha.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function gestionarInstructores(string $id)
    {
        try {
            Log::info('Acceso a gestión de instructores con sistema robusto', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'timestamp' => now()
            ]);

            // Buscar la ficha con todas sus relaciones necesarias
            $ficha = FichaCaracterizacion::with([
                'instructor.persona',
                'instructorFicha.instructor.persona',
                'instructorFicha.instructorFichaDias.dia',
                'diasFormacion.dia',
                'programaFormacion.redConocimiento',
                'sede.regional'
            ])->findOrFail($id);

            // Verificar que la ficha esté activa
            if (!$ficha->status) {
                return redirect()->route('fichaCaracterizacion.show', $id)
                    ->with('warning', 'La ficha no está activa. No se pueden gestionar instructores.');
            }

            // Obtener instructores ya asignados a esta ficha
            $instructoresAsignados = $ficha->instructorFicha()
                ->with(['instructor.persona', 'instructorFichaDias.dia'])
                ->get();

            // Obtener días de formación asignados a la ficha
            $diasFormacionFicha = $ficha->diasFormacion()
                ->with('dia')
                ->get()
                ->pluck('dia')
                ->unique('id')
                ->sortBy('id');

            // Usar el servicio robusto para obtener instructores disponibles
            $asignacionService = app(\App\Services\AsignacionInstructorService::class);
            $instructoresConDisponibilidad = $asignacionService->obtenerInstructoresDisponibles((int)$id);

            // Filtrar instructores ya asignados de la lista de disponibles
            // EXCLUIR: No filtrar al instructor líder (principal) ya que es una asignación separada
            $instructorLiderId = $ficha->instructor_id; // Instructor líder de la ficha
            $instructoresAsignadosIds = $instructoresAsignados->pluck('instructor_id')->toArray();
            
            Log::info('🔍 DEBUG FILTRADO INSTRUCTORES', [
                'instructor_lider_id' => $instructorLiderId,
                'instructores_asignados_ids' => $instructoresAsignadosIds,
                'total_disponibles_antes_filtro' => count($instructoresConDisponibilidad)
            ]);
            
            $instructoresConDisponibilidad = array_filter($instructoresConDisponibilidad, function($instructorData) use ($instructoresAsignadosIds, $instructorLiderId) {
                $instructorId = $instructorData['instructor']->id;
                
                // Si es el instructor líder, siempre incluirlo en la lista (puede ser reasignado)
                if ($instructorId == $instructorLiderId) {
                    Log::info('🔍 INCLUYENDO INSTRUCTOR LÍDER', ['instructor_id' => $instructorId]);
                    return true;
                }
                
                // Para otros instructores, excluir si ya están asignados como instructores adicionales
                $incluir = !in_array($instructorId, $instructoresAsignadosIds);
                if (!$incluir) {
                    Log::info('🔍 EXCLUYENDO INSTRUCTOR ASIGNADO', ['instructor_id' => $instructorId]);
                }
                return $incluir;
            });
            
            // Reindexar el array para mantener índices numéricos
            $instructoresConDisponibilidad = array_values($instructoresConDisponibilidad);

            // Obtener estadísticas de asignaciones para mostrar en la vista
            $estadisticasAsignaciones = $asignacionService->obtenerEstadisticasAsignaciones();

            // Obtener logs recientes de asignaciones para esta ficha
            $logsRecientes = \App\Models\AsignacionInstructorLog::where('ficha_id', $id)
                ->with(['instructor.persona', 'user'])
                ->orderBy('fecha_accion', 'desc')
                ->limit(10)
                ->get();

            Log::info('Datos de gestión de instructores cargados con sistema robusto', [
                'ficha_id' => $id,
                'total_instructores_evaluados' => count($instructoresConDisponibilidad),
                'instructores_disponibles' => count(array_filter($instructoresConDisponibilidad, fn($i) => $i['disponible'])),
                'instructores_asignados' => $instructoresAsignados->count(),
                'logs_recientes' => $logsRecientes->count()
            ]);

            return view('fichas.gestionar-instructores', compact(
                'ficha',
                'instructoresAsignados',
                'instructoresConDisponibilidad',
                'diasFormacionFicha',
                'estadisticasAsignaciones',
                'logsRecientes'
            ));

        } catch (\Exception $e) {
            Log::error('Error al cargar gestión de instructores', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'Error al cargar la gestión de instructores: ' . $e->getMessage());
        }
    }

    /**
     * Asigna instructores a una ficha.
     *
     * @param AsignarInstructoresRequest $request
     * @param string $id El ID de la ficha.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asignarInstructores(AsignarInstructoresRequest $request, string $id)
    {
        try {
            // El FormRequest ya maneja todas las validaciones
            $instructoresData = $request->validated()['instructores'];
            $instructorPrincipalId = $request->validated()['instructor_principal_id'];

            Log::info('Iniciando asignación de instructores con validaciones robustas', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'instructores_count' => count($instructoresData),
                'instructor_principal_id' => $instructorPrincipalId,
                'timestamp' => now()
            ]);

            // Usar el servicio especializado para la asignación
            $asignacionService = app(\App\Services\AsignacionInstructorService::class);
            $resultado = $asignacionService->asignarInstructores(
                $instructoresData,
                $id,
                $instructorPrincipalId,
                Auth::id()
            );

            if ($resultado['success']) {
                return redirect()->route('fichaCaracterizacion.gestionarInstructores', $id)
                    ->with('success', $resultado['message'] . ' Se asignaron ' . $resultado['total_asignados'] . ' instructores.');
            } else {
                return back()
                    ->withErrors(['error' => $resultado['message']])
                    ->withInput();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación en asignación de instructores', [
                'ficha_id' => $id,
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            
            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Error crítico en asignación de instructores', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return back()
                ->withErrors(['error' => 'Error crítico al asignar instructores. Por favor, contacte al administrador.'])
                ->withInput();
        }
    }

    /**
     * Desasigna un instructor de una ficha.
     *
     * @param string $id El ID de la ficha.
     * @param string $instructorId El ID del instructor.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function desasignarInstructor(string $id, string $instructorId)
    {
        try {
            Log::info('Iniciando desasignación de instructor con servicio robusto', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'instructor_id' => $instructorId,
                'timestamp' => now()
            ]);

            // Usar el servicio especializado para la desasignación
            $asignacionService = app(\App\Services\AsignacionInstructorService::class);
            $resultado = $asignacionService->desasignarInstructor(
                (int)$instructorId,
                (int)$id,
                Auth::id()
            );

            if ($resultado['success']) {
                return back()->with('success', $resultado['message']);
            } else {
                return back()->withErrors(['error' => $resultado['message']]);
            }

        } catch (\Exception $e) {
            Log::error('Error crítico al desasignar instructor', [
                'ficha_id' => $id,
                'instructor_id' => $instructorId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return back()->withErrors(['error' => 'Error crítico al desasignar instructor. Por favor, contacte al administrador.']);
        }
    }

    /**
     * Verifica la disponibilidad de instructores para una ficha.
     *
     * @param \Illuminate\Database\Eloquent\Collection $instructores
     * @param \App\Models\FichaCaracterizacion $ficha
     * @return array
     */
    private function verificarDisponibilidadInstructores($instructores, $ficha)
    {
        $disponibilidad = [];

        foreach ($instructores as $instructor) {
            // Verificar si el instructor ya está asignado a otras fichas en el mismo rango de fechas
            $fichasSuperpuestas = InstructorFichaCaracterizacion::where('instructor_id', $instructor->id)
                ->where('ficha_id', '!=', $ficha->id)
                ->where(function ($query) use ($ficha) {
                    $query->whereBetween('fecha_inicio', [$ficha->fecha_inicio, $ficha->fecha_fin])
                        ->orWhereBetween('fecha_fin', [$ficha->fecha_inicio, $ficha->fecha_fin])
                        ->orWhere(function ($subQuery) use ($ficha) {
                            $subQuery->where('fecha_inicio', '<=', $ficha->fecha_inicio)
                                ->where('fecha_fin', '>=', $ficha->fecha_fin);
                        });
                })
                ->count();

            $disponibilidad[$instructor->id] = [
                'disponible' => $fichasSuperpuestas == 0,
                'fichas_superpuestas' => $fichasSuperpuestas,
                'instructor' => $instructor
            ];
        }

        return $disponibilidad;
    }

    /**
     * Muestra la vista para gestionar días de formación de una ficha.
     *
     * @param int $id El ID de la ficha.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function gestionarDiasFormacion(string $id)
    {
        //try {
            Log::info('Acceso a gestión de días de formación', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'timestamp' => now()
            ]);

            // Buscar la ficha con sus relaciones
            $ficha = FichaCaracterizacion::with([
                'programaFormacion',
                'jornadaFormacion',
                'diasFormacion.dia',
                'sede'
            ])->findOrFail($id);

            // Obtener días de la semana disponibles
            $diasSemana = \App\Models\Parametro::whereIn('id', [12, 13, 14, 15, 16, 17]) // LUNES a SÁBADO
                ->orderBy('id')
                ->get();

            // Obtener días ya asignados a esta ficha
            $diasAsignados = $ficha->diasFormacion()
                ->with('dia')
                ->get();

            // Configuración de jornadas y días permitidos
            $configuracionJornadas = $this->obtenerConfiguracionJornadas();

            // Calcular horas totales actuales
            $horasTotalesActuales = $this->calcularHorasTotales($diasAsignados, $ficha);

            Log::info('Datos de gestión de días cargados', [
                'ficha_id' => $id,
                'total_dias_disponibles' => $diasSemana->count(),
                'dias_asignados' => $diasAsignados->count(),
                'horas_totales_actuales' => $horasTotalesActuales
            ]);

            return view('fichas.gestionar-dias-formacion', compact(
                'ficha',
                'diasSemana',
                'diasAsignados',
                'configuracionJornadas',
                'horasTotalesActuales'
            ));

        // } catch (\Exception $e) {
        //     Log::error('Error al cargar gestión de días de formación', [
        //         'ficha_id' => $id,
        //         'user_id' => Auth::id(),
        //         'error' => $e->getMessage(),
        //         'line' => $e->getLine()
        //     ]);

        //     return redirect()->route('fichaCaracterizacion.index')
        //         ->with('error', 'Error al cargar la gestión de días de formación: ' . $e->getMessage());
        // }
    }

    /**
     * Guarda los días de formación de una ficha.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id El ID de la ficha.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function guardarDiasFormacion(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            Log::info('Iniciando guardado de días de formación', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'dias' => $request->dias ?? [],
                'timestamp' => now()
            ]);

            // Validar datos
            $request->validate([
                'dias' => 'required|array|min:1',
                'dias.*.dia_id' => 'required|exists:parametros,id',
                'dias.*.hora_inicio' => 'required|date_format:H:i',
                'dias.*.hora_fin' => 'required|date_format:H:i|after:dias.*.hora_inicio'
            ], [
                'dias.required' => 'Debe seleccionar al menos un día de formación.',
                'dias.*.dia_id.required' => 'El día es requerido.',
                'dias.*.dia_id.exists' => 'El día seleccionado no existe.',
                'dias.*.hora_inicio.required' => 'La hora de inicio es requerida.',
                'dias.*.hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
                'dias.*.hora_fin.required' => 'La hora de fin es requerida.',
                'dias.*.hora_fin.date_format' => 'La hora de fin debe tener el formato HH:MM.',
                'dias.*.hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.'
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);

            // Validar días según jornada
            $configuracionJornadas = $this->obtenerConfiguracionJornadas();
            $jornadaId = $ficha->jornada_id;
            
            if (isset($configuracionJornadas[$jornadaId])) {
                $diasPermitidos = $configuracionJornadas[$jornadaId]['dias_permitidos'];
                $diasSeleccionados = collect($request->dias)->pluck('dia_id')->toArray();
                
                $diasNoPermitidos = array_diff($diasSeleccionados, $diasPermitidos);
                if (!empty($diasNoPermitidos)) {
                    $nombresDias = \App\Models\Parametro::whereIn('id', $diasNoPermitidos)->pluck('name')->toArray();
                    return back()->withErrors([
                        'dias' => 'Los días ' . implode(', ', $nombresDias) . ' no están permitidos para la jornada ' . $configuracionJornadas[$jornadaId]['nombre'] . '.'
                    ]);
                }
            }

            // Eliminar días existentes
            $ficha->diasFormacion()->delete();

            // Crear nuevos días de formación
            foreach ($request->dias as $diaData) {
                $ficha->diasFormacion()->create([
                    'dia_id' => $diaData['dia_id'],
                    'hora_inicio' => $diaData['hora_inicio'],
                    'hora_fin' => $diaData['hora_fin']
                ]);
            }

            // Calcular y actualizar horas totales
            $nuevasHorasTotales = $this->calcularHorasTotales($ficha->diasFormacion()->get(), $ficha);
            $ficha->update([
                'total_horas' => $nuevasHorasTotales,
                'user_edit_id' => Auth::id()
            ]);

            DB::commit();

            Log::info('Días de formación guardados exitosamente', [
                'ficha_id' => $id,
                'total_dias' => count($request->dias),
                'horas_totales' => $nuevasHorasTotales,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('fichaCaracterizacion.show', $id)
                ->with('success', 'Días de formación guardados exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Error de validación en guardado de días de formación', [
                'ficha_id' => $id,
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            throw $e;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar días de formación', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return back()->withErrors([
                'error' => 'Error al guardar días de formación: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Actualiza un día de formación específico.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id El ID de la ficha.
     * @param string $diaId El ID del día de formación.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function actualizarDiaFormacion(Request $request, string $id, string $diaId)
    {
        try {
            DB::beginTransaction();

            Log::info('Iniciando actualización de día de formación', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'dia_id' => $diaId,
                'timestamp' => now()
            ]);

            // Validar datos
            $request->validate([
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio'
            ], [
                'hora_inicio.required' => 'La hora de inicio es requerida.',
                'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
                'hora_fin.required' => 'La hora de fin es requerida.',
                'hora_fin.date_format' => 'La hora de fin debe tener el formato HH:MM.',
                'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.'
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);
            $diaFormacion = $ficha->diasFormacion()->findOrFail($diaId);

            // Actualizar el día de formación
            $diaFormacion->update([
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin
            ]);

            // Recalcular horas totales
            $nuevasHorasTotales = $this->calcularHorasTotales($ficha->diasFormacion()->get(), $ficha);
            $ficha->update([
                'total_horas' => $nuevasHorasTotales,
                'user_edit_id' => Auth::id()
            ]);

            DB::commit();

            Log::info('Día de formación actualizado exitosamente', [
                'ficha_id' => $id,
                'dia_id' => $diaId,
                'horas_totales' => $nuevasHorasTotales,
                'user_id' => Auth::id()
            ]);

            return back()->with('success', 'Día de formación actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar día de formación', [
                'ficha_id' => $id,
                'dia_id' => $diaId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return back()->withErrors([
                'error' => 'Error al actualizar día de formación: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Elimina un día de formación específico.
     *
     * @param string $id El ID de la ficha.
     * @param string $diaId El ID del día de formación.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function eliminarDiaFormacion(string $id, string $diaId)
    {
        try {
            DB::beginTransaction();

            Log::info('Iniciando eliminación de día de formación', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'dia_id' => $diaId,
                'timestamp' => now()
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);
            $diaFormacion = $ficha->diasFormacion()->findOrFail($diaId);

            // Eliminar el día de formación
            $diaFormacion->delete();

            // Recalcular horas totales
            $nuevasHorasTotales = $this->calcularHorasTotales($ficha->diasFormacion()->get(), $ficha);
            $ficha->update([
                'total_horas' => $nuevasHorasTotales,
                'user_edit_id' => Auth::id()
            ]);

            DB::commit();

            Log::info('Día de formación eliminado exitosamente', [
                'ficha_id' => $id,
                'dia_id' => $diaId,
                'horas_totales' => $nuevasHorasTotales,
                'user_id' => Auth::id()
            ]);

            return back()->with('success', 'Día de formación eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar día de formación', [
                'ficha_id' => $id,
                'dia_id' => $diaId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return back()->withErrors([
                'error' => 'Error al eliminar día de formación: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene la configuración de jornadas y días permitidos.
     *
     * @return array
     */
    private function obtenerConfiguracionJornadas()
    {
        return [
            1 => [ // MAÑANA
                'nombre' => 'MAÑANA',
                'dias_permitidos' => [12, 13, 14, 15, 16, 17], // LUNES a SÁBADO
                'horario_tipico' => ['08:00', '12:00']
            ],
            2 => [ // TARDE
                'nombre' => 'TARDE',
                'dias_permitidos' => [12, 13, 14, 15, 16, 17], // LUNES a SÁBADO
                'horario_tipico' => ['14:00', '18:00']
            ],
            3 => [ // NOCHE
                'nombre' => 'NOCHE',
                'dias_permitidos' => [12, 13, 14, 15, 16, 17], // LUNES a SÁBADO
                'horario_tipico' => ['18:00', '22:00']
            ],
            4 => [ // FIN DE SEMANA
                'nombre' => 'FIN DE SEMANA',
                'dias_permitidos' => [17], // SÁBADO
                'horario_tipico' => ['08:00', '17:00']
            ],
            5 => [ // MIXTA
                'nombre' => 'MIXTA',
                'dias_permitidos' => [12, 13, 14, 15, 16, 17], // LUNES a SÁBADO
                'horario_tipico' => ['08:00', '18:00']
            ]
        ];
    }

    /**
     * Calcula las horas totales de formación basado en los días asignados.
     *
     * @param \Illuminate\Database\Eloquent\Collection $diasFormacion
     * @param \App\Models\FichaCaracterizacion $ficha
     * @return int
     */
    private function calcularHorasTotales($diasFormacion, $ficha)
    {
        $horasTotales = 0;
        $duracionEnDias = $ficha->duracionEnDias();

        foreach ($diasFormacion as $dia) {
            try {
                // Usar parse() en lugar de createFromFormat() para mayor flexibilidad
                $horaInicio = \Carbon\Carbon::parse($dia->hora_inicio);
                $horaFin = \Carbon\Carbon::parse($dia->hora_fin);
            $horasPorDia = $horaInicio->diffInHours($horaFin);
            
            $horasTotales += $horasPorDia * $duracionEnDias;
            } catch (\Exception $e) {
                // Log del error y continuar con el siguiente día
                Log::warning("Error al calcular horas para el día: " . $e->getMessage(), [
                    'dia_id' => $dia->id ?? 'N/A',
                    'hora_inicio' => $dia->hora_inicio ?? 'N/A',
                    'hora_fin' => $dia->hora_fin ?? 'N/A'
                ]);
                continue;
            }
        }

        return $horasTotales;
    }

    /**
     * Obtiene los ambientes disponibles filtrados por sede.
     *
     * @param int $sedeId ID de la sede
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAmbientesPorSede($sedeId)
    {
        try {
            Log::info('Obteniendo ambientes por sede', [
                'sede_id' => $sedeId,
                'user_id' => Auth::id()
            ]);

            $ambientes = \App\Models\Ambiente::with(['piso.bloque'])
                ->whereHas('piso.bloque', function($query) use ($sedeId) {
                    $query->where('sede_id', $sedeId);
                })
                ->where('status', 1) // Solo ambientes activos
                ->orderBy('title', 'asc')
                ->get();

            $ambientesFormateados = $ambientes->map(function($ambiente) {
                return [
                    'id' => $ambiente->id,
                    'title' => $ambiente->title,
                    'descripcion' => $ambiente->piso ? 
                        $ambiente->piso->bloque->nombre . ' - ' . $ambiente->piso->nombre : 
                        'Sin ubicación'
                ];
            });

            Log::info('Ambientes obtenidos exitosamente', [
                'sede_id' => $sedeId,
                'total_ambientes' => $ambientesFormateados->count(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'data' => $ambientesFormateados
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener ambientes por sede', [
                'sede_id' => $sedeId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los ambientes de la sede.'
            ], 500);
        }
    }

    /**
     * Muestra la vista para gestionar aprendices de una ficha.
     *
     * @param int $id ID de la ficha de caracterización
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function gestionarAprendices($id)
    {
        try {
            Log::info('Acceso a gestión de aprendices', [
                'ficha_id' => $id,
                'user_id' => Auth::id()
            ]);

            $ficha = FichaCaracterizacion::with([
                'programaFormacion',
                'aprendices.persona.user'
            ])->findOrFail($id);

            // Obtener todas las personas que NO están asignadas a esta ficha
            // Estrategia: obtener personas que no están en la tabla pivot para esta ficha
            $aprendicesAsignadosIds = $ficha->aprendices()->pluck('aprendices.id');
            
            $personasDisponibles = \App\Models\Persona::with('user', 'aprendiz')
                ->where('status', 1) // Solo personas activas
                ->where(function($query) use ($aprendicesAsignadosIds) {
                    // Personas que no tienen aprendiz asignado a esta ficha
                    if ($aprendicesAsignadosIds->count() > 0) {
                        $query->whereDoesntHave('aprendiz', function($subQuery) use ($aprendicesAsignadosIds) {
                            $subQuery->whereIn('id', $aprendicesAsignadosIds);
                        });
                    }
                    
                    // Personas sin rol APRENDIZ O aprendices desasignados
                    $query->where(function($subQuery) {
                        // Sin rol APRENDIZ
                        $subQuery->whereHas('user', function($userQuery) {
                            $userQuery->whereDoesntHave('roles', function($roleQuery) {
                                $roleQuery->where('name', 'APRENDIZ');
                            });
                        })
                        // O aprendices desasignados (estado = 0)
                        ->orWhereHas('aprendiz', function($aprendizQuery) {
                            $aprendizQuery->where('estado', 0);
                        })
                        // O sin registro de aprendiz
                        ->orWhereDoesntHave('aprendiz');
                    });
                })
                ->orderBy('id', 'desc')
                ->get();

            // Debug: Obtener algunos aprendices desasignados para verificar
            $aprendicesDesasignados = \App\Models\Aprendiz::where('estado', 0)->with('persona')->get();
            
            Log::info('Vista de gestión de aprendices cargada', [
                'ficha_id' => $id,
                'aprendices_asignados' => $ficha->aprendices->count(),
                'personas_disponibles' => $personasDisponibles->count(),
                'aprendices_desasignados_total' => $aprendicesDesasignados->count(),
                'aprendices_desasignados_ids' => $aprendicesDesasignados->pluck('id')->toArray(),
                'user_id' => Auth::id()
            ]);

            return view('fichas.gestionar-aprendices', compact('ficha', 'personasDisponibles'));

        } catch (\Exception $e) {
            Log::error('Error al cargar gestión de aprendices', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('fichaCaracterizacion.show', $id)
                ->with('error', 'Error al cargar la gestión de aprendices.');
        }
    }

    /**
     * Asigna aprendices a una ficha de caracterización.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id ID de la ficha de caracterización
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asignarAprendices(Request $request, $id)
    {
        try {
            $request->validate([
                'personas' => 'required|array|min:1',
                'personas.*' => 'exists:personas,id'
            ]);

            Log::info('Iniciando asignación de personas como aprendices', [
                'ficha_id' => $id,
                'personas_ids' => $request->input('personas'),
                'user_id' => Auth::id()
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);
            $personasIds = $request->input('personas');

            DB::beginTransaction();

            // Obtener el rol de APRENDIZ
            $rolAprendiz = \Spatie\Permission\Models\Role::where('name', 'APRENDIZ')->first();
            
            if (!$rolAprendiz) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'El rol de APRENDIZ no existe en el sistema.');
            }

            // Procesar cada persona
            foreach ($personasIds as $personaId) {
                $persona = \App\Models\Persona::with('user')->findOrFail($personaId);
                
                // Crear o actualizar registro de aprendiz
                $aprendiz = \App\Models\Aprendiz::updateOrCreate([
                    'persona_id' => $personaId,
                ], [
                    'ficha_caracterizacion_id' => $id, // Asignar la ficha actual
                    'estado' => 1,
                    'user_create_id' => Auth::id(),
                    'user_edit_id' => Auth::id(),
                ]);
                
                // Verificar que el aprendiz no esté ya asignado a esta ficha en la tabla pivot
                if ($ficha->aprendices()->where('aprendiz_id', $aprendiz->id)->exists()) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'La persona ya está asignada como aprendiz a esta ficha.');
                }
                
                // Asignar aprendiz a la ficha en la tabla pivot
                $ficha->aprendices()->attach($aprendiz->id);
                
                // Asignar rol de APRENDIZ al usuario si tiene usuario asociado
                if ($persona->user) {
                    if (!$persona->user->hasRole('APRENDIZ')) {
                        $persona->user->assignRole('APRENDIZ');
                    }
                }
            }

            DB::commit();

            Log::info('Personas asignadas como aprendices exitosamente', [
                'ficha_id' => $id,
                'personas_asignadas' => count($personasIds),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('fichaCaracterizacion.gestionarAprendices', $id)
                ->with('success', 'Personas asignadas como aprendices exitosamente a la ficha.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación al asignar personas como aprendices', [
                'ficha_id' => $id,
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al asignar personas como aprendices', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Error al asignar personas como aprendices. Por favor, intente nuevamente.');
        }
    }

    /**
     * Desasigna aprendices de una ficha de caracterización.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id ID de la ficha de caracterización
     * @return \Illuminate\Http\RedirectResponse
     */
    public function desasignarAprendices(Request $request, $id)
    {
        try { 
            Log::info('=== INICIO DESASIGNACIÓN APRENDICES ===', [
                'ficha_id' => $id,
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            // Validación de entrada
            $request->validate([
                'personas' => 'required|array|min:1',
                'personas.*' => 'exists:personas,id'
            ]);

            $personasIds = $request->input('personas');
            
            Log::info('Validación exitosa, procesando personas', [
                'ficha_id' => $id,
                'personas_ids' => $personasIds,
                'total_personas' => count($personasIds),
                'user_id' => Auth::id()
            ]);

            // Buscar la ficha
            $ficha = FichaCaracterizacion::findOrFail($id);
            
            Log::info('Ficha encontrada', [
                'ficha_id' => $ficha->id,
                'numero_ficha' => $ficha->ficha,
                'user_id' => Auth::id()
            ]);

            DB::beginTransaction();

            try {
                // Obtener los aprendices asignados a esta ficha específica que corresponden a las personas seleccionadas
                // Usamos la tabla pivot para obtener solo los aprendices asignados a ESTA ficha
                $aprendicesAsignados = $ficha->aprendices()
                    ->whereHas('persona', function($query) use ($personasIds) {
                        $query->whereIn('id', $personasIds);
                    })
                    ->get();
                
                Log::info('Aprendices asignados a esta ficha encontrados', [
                    'ficha_id' => $id,
                    'personas_solicitadas' => count($personasIds),
                    'personas_ids' => $personasIds,
                    'aprendices_encontrados' => $aprendicesAsignados->count(),
                    'aprendices_ids' => $aprendicesAsignados->pluck('id')->toArray(),
                    'aprendices_persona_ids' => $aprendicesAsignados->pluck('persona_id')->toArray(),
                    'user_id' => Auth::id()
                ]);
                
                // Verificar que todas las personas seleccionadas tengan un aprendiz asignado a esta ficha
                $personasEncontradas = $aprendicesAsignados->pluck('persona_id')->toArray();
                $personasNoEncontradas = array_diff($personasIds, $personasEncontradas);
                
                if (count($personasNoEncontradas) > 0) {
                    Log::warning('Personas no encontradas como aprendices en esta ficha', [
                        'ficha_id' => $id,
                        'personas_no_encontradas' => $personasNoEncontradas,
                        'personas_solicitadas' => $personasIds,
                        'user_id' => Auth::id()
                    ]);
                    
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Algunas personas no están asignadas como aprendices a esta ficha. Personas ID: ' . implode(', ', $personasNoEncontradas));
                }
                
                // Obtener los IDs de los aprendices a desasignar
                $aprendicesIds = $aprendicesAsignados->pluck('id');
                
                Log::info('IDs de aprendices a desasignar', [
                    'ficha_id' => $id,
                    'aprendices_ids' => $aprendicesIds->toArray(),
                    'total' => $aprendicesIds->count(),
                    'user_id' => Auth::id()
                ]);

                // Desasignar aprendices de la ficha
                $resultadoDetach = $ficha->aprendices()->detach($aprendicesIds);
                
                Log::info('Desasignación de ficha completada', [
                    'ficha_id' => $id,
                    'aprendices_desasignados' => $aprendicesIds->toArray(),
                    'resultado_detach' => $resultadoDetach,
                    'user_id' => Auth::id()
                ]);

                // Actualizar registros de aprendiz: limpiar ficha_caracterizacion_id y desactivar
                $aprendicesActualizados = [];
                foreach ($aprendicesIds as $aprendizId) {
                    try {
                        $aprendiz = \App\Models\Aprendiz::find($aprendizId);
                        if ($aprendiz) {
                            $estadoAnterior = $aprendiz->estado;
                            $fichaAnterior = $aprendiz->ficha_caracterizacion_id;
                            
                            $aprendiz->update([
                                'ficha_caracterizacion_id' => null, // Limpiar la ficha asignada
                                'estado' => 0, // Desactivar pero mantener registro
                                'user_edit_id' => Auth::id(),
                            ]);
                            
                            $aprendicesActualizados[] = [
                                'aprendiz_id' => $aprendiz->id,
                                'persona_id' => $aprendiz->persona_id,
                                'estado_anterior' => $estadoAnterior,
                                'ficha_anterior' => $fichaAnterior
                            ];
                            
                            Log::info('Aprendiz desactivado exitosamente', [
                                'aprendiz_id' => $aprendiz->id,
                                'persona_id' => $aprendiz->persona_id,
                                'estado_anterior' => $estadoAnterior,
                                'estado_nuevo' => 0,
                                'ficha_anterior' => $fichaAnterior,
                                'ficha_caracterizacion_id_nuevo' => null,
                                'user_id' => Auth::id()
                            ]);
                        } else {
                            Log::warning('Aprendiz no encontrado durante actualización', [
                                'aprendiz_id' => $aprendizId,
                                'user_id' => Auth::id()
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error al actualizar aprendiz individual', [
                            'aprendiz_id' => $aprendizId,
                            'error' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'user_id' => Auth::id()
                        ]);
                        throw $e; // Re-lanzar para que se maneje en el catch principal
                    }
                }

                DB::commit();

                Log::info('=== DESASIGNACIÓN COMPLETADA EXITOSAMENTE ===', [
                    'ficha_id' => $id,
                    'personas_desasignadas' => count($personasIds),
                    'aprendices_actualizados' => $aprendicesActualizados,
                    'user_id' => Auth::id()
                ]);

                return redirect()->route('fichaCaracterizacion.gestionarAprendices', $id)
                    ->with('success', 'Personas desasignadas como aprendices exitosamente de la ficha. Total: ' . count($personasIds));

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e; // Re-lanzar para que se maneje en el catch principal
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('=== ERROR DE VALIDACIÓN EN DESASIGNACIÓN ===', [
                'ficha_id' => $id,
                'request_data' => $request->all(),
                'validation_errors' => $e->errors(),
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Error de validación: ' . implode(', ', Arr::flatten($e->errors())));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('=== FICHA NO ENCONTRADA ===', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'La ficha de caracterización no existe.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('=== ERROR CRÍTICO EN DESASIGNACIÓN APRENDICES ===', [
                'ficha_id' => $id,
                'request_data' => $request->all(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            return redirect()->back()
                ->with('error', 'Error crítico al desasignar aprendices: ' . $e->getMessage() . '. Revisar logs para más detalles.');
        }
    }

    /**
     * Verifica si un instructor tiene asignaciones superpuestas en un rango de fechas.
     *
     * @param int $instructorId
     * @param string $fechaInicio
     * @param string $fechaFin
     * @param int|null $excludeInstructorFichaId (opcional, para excluir una asignación específica al editar)
     * @return array
     */
    public function verificarConflictosFechasInstructor($instructorId, $fechaInicio, $fechaFin, $excludeInstructorFichaId = null)
    {
        try {
            $query = InstructorFichaCaracterizacion::where('instructor_id', $instructorId)
                ->where(function($q) use ($fechaInicio, $fechaFin) {
                    // Verificar superposición de rangos de fechas
                    $q->where(function($subQ) use ($fechaInicio, $fechaFin) {
                        // La nueva fecha de inicio está dentro de un rango existente
                        $subQ->where('fecha_inicio', '<=', $fechaInicio)
                             ->where('fecha_fin', '>=', $fechaInicio);
                    })->orWhere(function($subQ) use ($fechaInicio, $fechaFin) {
                        // La nueva fecha de fin está dentro de un rango existente
                        $subQ->where('fecha_inicio', '<=', $fechaFin)
                             ->where('fecha_fin', '>=', $fechaFin);
                    })->orWhere(function($subQ) use ($fechaInicio, $fechaFin) {
                        // El nuevo rango contiene completamente un rango existente
                        $subQ->where('fecha_inicio', '>=', $fechaInicio)
                             ->where('fecha_fin', '<=', $fechaFin);
                    });
                });

            // Excluir una asignación específica si se está editando
            if ($excludeInstructorFichaId) {
                $query->where('id', '!=', $excludeInstructorFichaId);
            }

            $conflictos = $query->with(['fichaCaracterizacion.programaFormacion'])->get();

            return [
                'tiene_conflictos' => $conflictos->count() > 0,
                'conflictos' => $conflictos->map(function($conflicto) {
                    return [
                        'id' => $conflicto->id,
                        'fecha_inicio' => $conflicto->fecha_inicio ? \Carbon\Carbon::parse($conflicto->fecha_inicio)->format('d/m/Y') : 'N/A',
                        'fecha_fin' => $conflicto->fecha_fin ? \Carbon\Carbon::parse($conflicto->fecha_fin)->format('d/m/Y') : 'N/A',
                        'ficha' => $conflicto->fichaCaracterizacion->ficha,
                        'programa' => $conflicto->fichaCaracterizacion->programaFormacion->nombre ?? 'Sin programa',
                        'total_horas' => $conflicto->total_horas_instructor
                    ];
                })
            ];

        } catch (\Exception $e) {
            Log::error('Error al verificar conflictos de fechas del instructor', [
                'instructor_id' => $instructorId,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'error' => $e->getMessage()
            ]);

            return [
                'tiene_conflictos' => false,
                'conflictos' => [],
                'error' => 'Error al verificar disponibilidad de fechas'
            ];
        }
    }

    /**
     * API endpoint para verificar conflictos de fechas de un instructor.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verificarDisponibilidadFechasInstructor(Request $request)
    {
        try {
            $request->validate([
                'instructor_id' => 'required|exists:instructors,id',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'exclude_instructor_ficha_id' => 'nullable|integer'
            ]);

            $resultado = $this->verificarConflictosFechasInstructor(
                $request->instructor_id,
                $request->fecha_inicio,
                $request->fecha_fin,
                $request->exclude_instructor_ficha_id
            );

            return response()->json([
                'disponible' => !$resultado['tiene_conflictos'],
                'conflictos' => $resultado['conflictos'],
                'mensaje' => $resultado['tiene_conflictos'] 
                    ? 'El instructor tiene asignaciones superpuestas en ese rango de fechas'
                    : 'El instructor está disponible en ese rango de fechas'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Datos de entrada inválidos',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en verificación de disponibilidad de fechas', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }
}

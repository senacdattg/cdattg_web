<?php

namespace App\Http\Controllers;

use App\Configuration\UploadLimits;
use App\Models\Instructor;
use App\Http\Requests\StoreInstructorRequest;
use App\Http\Requests\UpdateInstructorRequest;
use App\Http\Requests\InstructorRequest;
use App\Http\Requests\CreateInstructorRequest;
use App\Services\InstructorBusinessRulesService;
use App\Models\RedConocimiento;
use App\Models\FichaCaracterizacion;
use App\Models\Persona;
use App\Models\Regional;
use App\Models\Tema;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\InstructoresDisponiblesRequest;
use App\Http\Requests\VerificarDisponibilidadRequest;
use Illuminate\Support\Facades\Auth;

class InstructorController extends Controller
{
    private const MAX_IMPORT_FILE_KB = UploadLimits::IMPORT_FILE_SIZE_KB;
    private const CSV_HEADER_EMAIL = 'CORREO INSTITUCIONAL';

    protected $businessRulesService;
    protected $instructorService;

    public function __construct(
        InstructorBusinessRulesService $businessRulesService,
        \App\Services\InstructorService $instructorService
    ) {
        $this->middleware('auth'); // Middleware de autenticación para todos los métodos del controlador
        $this->businessRulesService = $businessRulesService;
        $this->instructorService = $instructorService;

        // Middleware específico para métodos individuales usando permisos de Instructor
        // Temporalmente comentado para debuggear
        // $this->middleware('can:VER INSTRUCTOR')->only(['index', 'show']);
        // $this->middleware('can:CREAR INSTRUCTOR')->only(['create', 'store']);
        $this->middleware('can:EDITAR INSTRUCTOR')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR INSTRUCTOR')->only('destroy');
        $this->middleware('can:GESTIONAR ESPECIALIDADES INSTRUCTOR')->only(['especialidades', 'asignarEspecialidad']);
        $this->middleware('can:VER FICHAS ASIGNADAS')->only('fichasAsignadas');
        $this->middleware('can:CAMBIAR ESTADO INSTRUCTOR')->only('cambiarEstado');
        $this->middleware('validate.content.length:' . UploadLimits::IMPORT_CONTENT_LENGTH_BYTES)
            ->only('storeImportarCSV');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $filtros = [
                'search' => $request->input('search'),
                'estado' => $request->input('estado', 'todos'),
                'especialidad' => $request->input('especialidad'),
                'regional' => $request->input('regional'),
                'per_page' => 15
            ];

            $instructores = $this->instructorService->listarConFiltros($filtros);

            // Obtener datos para filtros
            $regionales = Regional::where('status', true)->orderBy('nombre')->get();
            $especialidades = RedConocimiento::where('status', true)->orderBy('nombre')->get();
            
            // Obtener datos para el formulario de creación en acordeón
            $personasDisponibles = Persona::query()
                ->whereDoesntHave('instructor')
                ->orderBy('primer_nombre')
                ->orderBy('primer_apellido')
                ->get();
            
            $jornadasTrabajo = \App\Models\JornadaFormacion::orderBy('jornada')->get();
            
            // Tipos de vinculación desde parametros_temas (tema: TIPOS DE VINCULACION)
            $tiposVinculacion = \App\Models\ParametroTema::whereHas('tema', function($q) {
                $q->where('name', 'LIKE', '%TIPOS DE VINCULACION%');
            })->whereHas('parametro', function($query) {
                $query->where('status', true);
            })->where('status', true)
              ->with('parametro')
              ->get()
              ->sortBy(function($pt) {
                  return $pt->parametro->name;
              })
              ->values();
            
            // Niveles académicos desde parametros_temas (tema: NIVELES ACADEMICOS)
            $nivelesAcademicos = \App\Models\ParametroTema::whereHas('tema', function($q) {
                $q->where('name', 'LIKE', '%NIVELES ACADEMICOS%');
            })->whereHas('parametro', function($query) {
                $query->where('status', true);
            })->where('status', true)
              ->with('parametro')
              ->get()
              ->sortBy(function($pt) {
                  return $pt->parametro->name;
              })
              ->values();

            // Estadísticas
            $estadisticas = $this->instructorService->obtenerEstadisticas();

            return view(
                'Instructores.index',
                compact(
                    'instructores',
                    'regionales',
                    'especialidades',
                    'estadisticas',
                    'filtros',
                    'personasDisponibles',
                    'jornadasTrabajo',
                    'tiposVinculacion',
                    'nivelesAcademicos'
                )
            );
        } catch (Exception $e) {
            Log::error('Error al listar instructores: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Error al cargar la lista de instructores.');
        }
    }

    /**
     * Búsqueda avanzada de instructores con AJAX
     */
    public function search(Request $request)
    {
        try {
            $search = $request->input('search');
            $filtroEstado = $request->input('estado', 'todos');
            $filtroEspecialidad = $request->input('especialidad');
            $filtroRegional = $request->input('regional');
            $page = $request->input('page', 1);

            // Construir query base con relaciones
            $query = Instructor::with([
                'persona',
                'regional',
                'instructorFichas' => function ($q) {
                    $q->with('ficha.programaFormacion');
                }
            ]);

            // Aplicar filtros
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('persona', function ($personaQuery) use ($search) {
                        $personaQuery->where('primer_nombre', 'like', "%{$search}%")
                            ->orWhere('segundo_nombre', 'like', "%{$search}%")
                            ->orWhere('primer_apellido', 'like', "%{$search}%")
                            ->orWhere('segundo_apellido', 'like', "%{$search}%")
                            ->orWhere('numero_documento', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            }

            if ($filtroEstado !== 'todos') {
                if ($filtroEstado === 'activos') {
                    $query->where('status', true);
                } elseif ($filtroEstado === 'inactivos') {
                    $query->where('status', false);
                }
            }

            if ($filtroEspecialidad) {
                $query->whereJsonContains('especialidades', $filtroEspecialidad);
            }

            if ($filtroRegional) {
                $query->where('regional_id', $filtroRegional);
            }

            // Obtener resultados paginados
            $instructores = $query->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $page);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('Instructores.partials.instructores-table', compact('instructores'))->render(),
                    'pagination' => $instructores->links()->toHtml()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        } catch (Exception $e) {
            Log::error('Error en búsqueda de instructores', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error en la búsqueda'
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Error en la búsqueda de instructores.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Instructores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateInstructorRequest $request)
    {
        try {
            $datos = $request->validated();

            // Preparar especialidades (array de IDs)
            if ($request->has('especialidades') && is_array($request->input('especialidades'))) {
                $especialidadesIds = array_filter($request->input('especialidades', []));
                $datos['especialidades'] = $especialidadesIds;
            } else {
                $datos['especialidades'] = [];
            }

            // Preparar jornadas (array de IDs)
            $jornadasIds = [];
            if ($request->has('jornadas') && is_array($request->input('jornadas'))) {
                $jornadasIds = array_filter($request->input('jornadas', []));
            }
            
            // Preparar arrays JSON - campos dinámicos que vienen como arrays
            $camposJsonArray = [
                'titulos_obtenidos',
                'instituciones_educativas',
                'certificaciones_tecnicas',
                'cursos_complementarios',
                'areas_experticia',
                'competencias_tic',
                'idiomas',
                'habilidades_pedagogicas',
                'documentos_adjuntos'
            ];
            
            foreach ($camposJsonArray as $campo) {
                if ($request->has($campo) && is_array($request->input($campo, []))) {
                    // Filtrar valores vacíos y trim
                    $valores = array_filter(array_map('trim', $request->input($campo, [])));
                    $datos[$campo] = !empty($valores) ? array_values($valores) : null;
                } else {
                    $datos[$campo] = null;
                }
            }

            // Agregar usuario creador
            $datos['user_create_id'] = Auth::id();

            $instructor = $this->instructorService->crear($datos, $jornadasIds);

            return redirect()
                ->route('instructor.index')
                ->with('success', '¡Instructor asignado exitosamente!');
        } catch (Exception $e) {
            Log::error('Error al crear instructor: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Instructor $instructor)
    {
        try {
            $fichasCaracterizacion = FichaCaracterizacion::all();
            $instructor->persona->edad = Carbon::parse($instructor->persona->fecha_de_nacimiento)->age;
            $instructor->persona->fecha_de_nacimiento = Carbon::parse(
                $instructor->persona->fecha_de_nacimiento
            )->format('d/m/Y');

            return view('Instructores.show', compact('instructor', 'fichasCaracterizacion'));
        } catch (Exception $e) {
            Log::error('Error al mostrar instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->route('instructor.index')
                ->with('error', 'Error al cargar los datos del instructor. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instructor $instructor)
    {
        try {
            // llamar los tipos de documentos
            $documentos = Tema::with([
                'parametros' => function ($query) {
                    $query->wherePivot('status', 1);
                },
            ])->findOrFail(2);

            // llamar los generos
            $generos = Tema::with([
                'parametros' => function ($query) {
                    $query->wherePivot('status', 1);
                },
            ])->findOrFail(3);

            $regionales = Regional::where('status', 1)->get();
            
            // Obtener centros de formación según la regional del instructor
            $centrosFormacion = collect([]);
            if ($instructor->regional_id) {
                $centrosFormacion = \App\Models\CentroFormacion::where('regional_id', $instructor->regional_id)
                    ->where('status', true)
                    ->orderBy('nombre')
                    ->get();
            }
            
            // Obtener especialidades (RedConocimiento) disponibles según la regional del instructor
            $especialidades = collect([]);
            if ($instructor->regional_id) {
                $especialidades = RedConocimiento::where('regionals_id', $instructor->regional_id)
                    ->where('status', true)
                    ->orderBy('nombre')
                    ->get();
            }
            
            // Obtener tipos de vinculación desde parametros_temas
            $tiposVinculacion = \App\Models\ParametroTema::whereHas('tema', function($q) {
                $q->where('name', 'LIKE', '%TIPOS DE VINCULACION%');
            })->whereHas('parametro', function($query) {
                $query->where('status', true);
            })->where('status', true)
              ->with('parametro')
              ->get()
              ->sortBy(function($pt) {
                  return $pt->parametro->name;
              })
              ->values();
            
            // Obtener niveles académicos desde parametros_temas
            $nivelesAcademicos = \App\Models\ParametroTema::whereHas('tema', function($q) {
                $q->where('name', 'LIKE', '%NIVELES ACADEMICOS%');
            })->whereHas('parametro', function($query) {
                $query->where('status', true);
            })->where('status', true)
              ->with('parametro')
              ->get()
              ->sortBy(function($pt) {
                  return $pt->parametro->name;
              })
              ->values();
            
            // Obtener jornadas de trabajo desde parametros_temas
            // Primero obtener el tema JORNADAS
            $temaJornadas = \App\Models\Tema::where('name', 'JORNADAS')->first();
            
            if ($temaJornadas) {
                $jornadasTrabajo = \App\Models\ParametroTema::where('tema_id', $temaJornadas->id)
                    ->whereHas('parametro', function($query) {
                        $query->where('status', true);
                    })
                    ->where('status', true)
                    ->with(['parametro', 'tema'])
                    ->get()
                    ->sortBy(function($pt) {
                        return $pt->parametro->name ?? '';
                    })
                    ->values();
            } else {
                // Fallback: usar LIKE si no se encuentra el tema exacto
                $jornadasTrabajo = \App\Models\ParametroTema::whereHas('tema', function($q) {
                    $q->where('name', 'LIKE', '%JORNADAS%');
                })->whereHas('parametro', function($query) {
                    $query->where('status', true);
                })->where('status', true)
                  ->with(['parametro', 'tema'])
                  ->get()
                  ->sortBy(function($pt) {
                      return $pt->parametro->name ?? '';
                  })
                  ->values();
            }
            
            // Log para depuración
            Log::info('Jornadas de trabajo cargadas en edit', [
                'cantidad' => $jornadasTrabajo->count(),
                'tema_id' => $temaJornadas->id ?? null,
                'jornadas' => $jornadasTrabajo->map(function($j) {
                    return [
                        'id' => $j->id,
                        'parametro_id' => $j->parametro_id,
                        'nombre' => $j->parametro->name ?? 'Sin nombre',
                        'tema' => $j->tema->name ?? 'Sin tema',
                        'status' => $j->status
                    ];
                })->toArray()
            ]);
            
            // Obtener jornadas asignadas al instructor - mapear desde jornadas_formacion a parametros_temas
            $jornadasAsignadas = [];
            try {
                $jornadasFormacionAsignadas = $instructor->jornadas()->pluck('jornadas_formacion.id')->toArray();
                if (!empty($jornadasFormacionAsignadas)) {
                    // Obtener los nombres de las jornadas desde jornadas_formacion
                    $jornadasFormacion = \App\Models\JornadaFormacion::whereIn('id', $jornadasFormacionAsignadas)->pluck('jornada')->toArray();
                    // Buscar los parametros_temas que corresponden a estos nombres
                    if (!empty($jornadasFormacion)) {
                        $parametrosTemas = \App\Models\ParametroTema::whereHas('tema', function($q) {
                            $q->where('name', 'LIKE', '%JORNADAS%');
                        })->whereHas('parametro', function($query) use ($jornadasFormacion) {
                            $query->whereIn('name', $jornadasFormacion);
                        })->pluck('id')->toArray();
                        $jornadasAsignadas = $parametrosTemas;
                    }
                }
            } catch (\Exception $e) {
                // Si hay error, dejar array vacío
                Log::warning('Error al obtener jornadas asignadas del instructor: ' . $e->getMessage());
                $jornadasAsignadas = [];
            }

            return view(
                'Instructores.edit',
                ['instructor' => $instructor],
                compact('documentos', 'generos', 'regionales', 'especialidades', 'centrosFormacion', 'tiposVinculacion', 'nivelesAcademicos', 'jornadasTrabajo', 'jornadasAsignadas')
            );
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de edición de instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->route('instructor.index')
                ->with('error', 'Error al cargar el formulario de edición. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInstructorRequest $request, Instructor $instructor)
    {
        try {
            DB::beginTransaction();

            $datos = $request->validated();
            
            // Preparar especialidades (array de IDs)
            if ($request->has('especialidades') && is_array($request->input('especialidades'))) {
                $especialidadesIds = array_filter($request->input('especialidades', []));
                
                // Validar que los IDs existan en la base de datos
                $especialidadesValidas = \App\Models\RedConocimiento::whereIn('id', $especialidadesIds)
                    ->pluck('id')
                    ->toArray();
                
                $especialidadesFormateadas = [
                    'principal' => null,
                    'secundarias' => []
                ];
                
                // La primera especialidad es la principal (guardar ID, no nombre)
                if (count($especialidadesValidas) > 0) {
                    $especialidadesFormateadas['principal'] = $especialidadesValidas[0];
                    
                    // Las demás son secundarias (guardar IDs, no nombres)
                    for ($i = 1; $i < count($especialidadesValidas); $i++) {
                        $especialidadesFormateadas['secundarias'][] = $especialidadesValidas[$i];
                    }
                }
                $datos['especialidades'] = $especialidadesFormateadas;
            } else {
                $datos['especialidades'] = [
                    'principal' => null,
                    'secundarias' => []
                ];
            }
            
            // Preparar jornadas (array de IDs) - mapear desde parametros_temas a jornadas_formacion
            $jornadasIds = [];
            if ($request->has('jornadas') && is_array($request->input('jornadas'))) {
                $jornadasRequest = $request->input('jornadas');
                Log::info('Jornadas recibidas en request', [
                    'jornadas_request' => $jornadasRequest,
                    'tipo' => gettype($jornadasRequest),
                    'es_array' => is_array($jornadasRequest)
                ]);
                
                // Convertir a enteros si vienen como strings
                $jornadasRequest = array_map('intval', array_filter($jornadasRequest));
                
                if (!empty($jornadasRequest)) {
                    $parametrosTemas = \App\Models\ParametroTema::whereIn('id', $jornadasRequest)
                        ->with('parametro')
                        ->get();
                    
                    Log::info('Parametros temas encontrados', [
                        'cantidad' => $parametrosTemas->count(),
                        'parametros' => $parametrosTemas->map(function($pt) {
                            return [
                                'id' => $pt->id,
                                'parametro_name' => $pt->parametro->name ?? null
                            ];
                        })->toArray()
                    ]);
                    
                    foreach ($parametrosTemas as $parametroTema) {
                        $nombreJornada = $parametroTema->parametro->name ?? null;
                        if ($nombreJornada) {
                            // Buscar por nombre exacto primero
                            $jornadaFormacion = \App\Models\JornadaFormacion::where('jornada', $nombreJornada)->first();
                            
                            // Si no se encuentra, buscar sin acentos y case-insensitive
                            if (!$jornadaFormacion) {
                                $jornadaFormacion = \App\Models\JornadaFormacion::whereRaw('LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(jornada, "Á", "A"), "É", "E"), "Í", "I"), "Ó", "O"), "Ú", "U")) = LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(?, "Á", "A"), "É", "E"), "Í", "I"), "Ó", "O"), "Ú", "U"))', [$nombreJornada])->first();
                            }
                            
                            // Si aún no se encuentra, buscar por LIKE
                            if (!$jornadaFormacion) {
                                $jornadaFormacion = \App\Models\JornadaFormacion::where('jornada', 'LIKE', "%{$nombreJornada}%")->first();
                            }
                            
                            // Si aún no se encuentra, crear la jornada si no existe
                            if (!$jornadaFormacion) {
                                Log::info('JornadaFormacion no encontrada, creando nueva', [
                                    'nombre_jornada' => $nombreJornada
                                ]);
                                $jornadaFormacion = \App\Models\JornadaFormacion::create([
                                    'jornada' => $nombreJornada
                                ]);
                            }
                            
                            if ($jornadaFormacion) {
                                $jornadasIds[] = $jornadaFormacion->id;
                                Log::info('Jornada mapeada', [
                                    'parametro_tema_id' => $parametroTema->id,
                                    'nombre_jornada' => $nombreJornada,
                                    'jornada_formacion_id' => $jornadaFormacion->id
                                ]);
                            }
                        }
                    }
                }
            }
            
            Log::info('Jornadas IDs finales para sincronizar', [
                'jornadas_ids' => $jornadasIds,
                'cantidad' => count($jornadasIds)
            ]);
            
            // Preparar arrays JSON - campos dinámicos que vienen como arrays
            $camposJsonArray = [
                'titulos_obtenidos',
                'instituciones_educativas',
                'certificaciones_tecnicas',
                'cursos_complementarios',
                'idiomas',
                'habilidades_pedagogicas',
            ];
            
            foreach ($camposJsonArray as $campo) {
                // Log para debuggear
                Log::info("Procesando campo array: {$campo}", [
                    'existe' => isset($datos[$campo]),
                    'es_array' => isset($datos[$campo]) && is_array($datos[$campo]),
                    'valor' => $datos[$campo] ?? 'no existe'
                ]);
                
                if (isset($datos[$campo]) && is_array($datos[$campo])) {
                    if ($campo === 'idiomas') {
                        // Manejo especial para idiomas (array anidado)
                        $idiomasFiltrados = [];
                        foreach ($datos[$campo] as $idioma) {
                            if (is_array($idioma) && isset($idioma['idioma']) && !empty(trim($idioma['idioma'] ?? ''))) {
                                $idiomasFiltrados[] = [
                                    'idioma' => trim($idioma['idioma']),
                                    'nivel' => $idioma['nivel'] ?? null
                                ];
                            }
                        }
                        $datos[$campo] = !empty($idiomasFiltrados) ? $idiomasFiltrados : null;
                    } elseif ($campo === 'habilidades_pedagogicas') {
                        // Manejo especial para habilidades pedagógicas (array simple de strings)
                        $valores = array_filter(
                            array_map(function($item) {
                                return is_string($item) ? trim($item) : (is_scalar($item) ? (string)$item : '');
                            }, $datos[$campo]),
                            function($item) {
                                return $item !== null && $item !== '' && in_array($item, ['virtual', 'presencial', 'dual']);
                            }
                        );
                        $datos[$campo] = !empty($valores) ? array_values($valores) : null;
                    } else {
                        // Manejo estándar para otros arrays (titulos, instituciones, certificaciones, cursos)
                        $valores = array_filter(
                            array_map(function($item) {
                                if (is_string($item)) {
                                    return trim($item);
                                } elseif (is_scalar($item)) {
                                    return (string)$item;
                                }
                                return null;
                            }, $datos[$campo]),
                            function($item) {
                                return $item !== null && $item !== '';
                            }
                        );
                        $datos[$campo] = !empty($valores) ? array_values($valores) : null;
                    }
                    
                    Log::info("Campo procesado: {$campo}", [
                        'resultado' => $datos[$campo]
                    ]);
                } else {
                    // Si no viene en el request o no es array, establecer como null
                    $datos[$campo] = null;
                }
            }
            
            // Convertir áreas de experticia y competencias TIC de string a array si vienen como texto
            if (isset($datos['areas_experticia']) && is_string($datos['areas_experticia'])) {
                $datos['areas_experticia'] = array_filter(array_map('trim', explode("\n", $datos['areas_experticia'])));
                $datos['areas_experticia'] = !empty($datos['areas_experticia']) ? array_values($datos['areas_experticia']) : null;
            }
            
            if (isset($datos['competencias_tic']) && is_string($datos['competencias_tic'])) {
                $datos['competencias_tic'] = array_filter(array_map('trim', explode("\n", $datos['competencias_tic'])));
                $datos['competencias_tic'] = !empty($datos['competencias_tic']) ? array_values($datos['competencias_tic']) : null;
            }
            
            // Validar y limpiar tipo_vinculacion_id
            if (isset($datos['tipo_vinculacion_id']) && !empty($datos['tipo_vinculacion_id'])) {
                $parametroTema = \App\Models\ParametroTema::find($datos['tipo_vinculacion_id']);
                if (!$parametroTema) {
                    $datos['tipo_vinculacion_id'] = null;
                }
            } else {
                $datos['tipo_vinculacion_id'] = null;
            }
            
            // Validar y limpiar nivel_academico_id
            if (isset($datos['nivel_academico_id']) && !empty($datos['nivel_academico_id'])) {
                $parametroTema = \App\Models\ParametroTema::find($datos['nivel_academico_id']);
                if (!$parametroTema) {
                    $datos['nivel_academico_id'] = null;
                }
            } else {
                $datos['nivel_academico_id'] = null;
            }
            
            // Validar centro_formacion_id
            if (isset($datos['centro_formacion_id']) && !empty($datos['centro_formacion_id'])) {
                $centro = \App\Models\CentroFormacion::find($datos['centro_formacion_id']);
                if (!$centro) {
                    $datos['centro_formacion_id'] = null;
                }
            } else {
                $datos['centro_formacion_id'] = null;
            }
            
            // Filtrar solo los campos que están en fillable
            $fillable = $instructor->getFillable();
            $datosActualizar = array_intersect_key($datos, array_flip($fillable));
            
            // Agregar usuario editor
            $datosActualizar['user_edit_id'] = Auth::id();
            
            // Log para debuggear los datos que se van a actualizar
            Log::info('Datos a actualizar en instructor', [
                'instructor_id' => $instructor->id,
                'campos_json' => [
                    'titulos_obtenidos' => $datosActualizar['titulos_obtenidos'] ?? null,
                    'instituciones_educativas' => $datosActualizar['instituciones_educativas'] ?? null,
                    'certificaciones_tecnicas' => $datosActualizar['certificaciones_tecnicas'] ?? null,
                    'cursos_complementarios' => $datosActualizar['cursos_complementarios'] ?? null,
                    'idiomas' => $datosActualizar['idiomas'] ?? null,
                    'habilidades_pedagogicas' => $datosActualizar['habilidades_pedagogicas'] ?? null,
                ]
            ]);
            
            // Actualizar instructor
            $instructor->update($datosActualizar);
            
            // Verificar que se guardaron correctamente
            $instructor->refresh();
            Log::info('Instructor actualizado - valores guardados', [
                'instructor_id' => $instructor->id,
                'titulos_obtenidos' => $instructor->titulos_obtenidos,
                'instituciones_educativas' => $instructor->instituciones_educativas,
                'certificaciones_tecnicas' => $instructor->certificaciones_tecnicas,
                'cursos_complementarios' => $instructor->cursos_complementarios,
                'idiomas' => $instructor->idiomas,
                'habilidades_pedagogicas' => $instructor->habilidades_pedagogicas,
            ]);
            
            // Sincronizar jornadas (many-to-many)
            try {
                if (!empty($jornadasIds)) {
                    $pivotData = [];
                    foreach ($jornadasIds as $jornadaId) {
                        $pivotData[$jornadaId] = [
                            'user_edit_id' => Auth::id(),
                            'updated_at' => now()
                        ];
                    }
                    Log::info('Sincronizando jornadas con pivot data', [
                        'instructor_id' => $instructor->id,
                        'pivot_data' => $pivotData
                    ]);
                    
                    $resultado = $instructor->jornadas()->sync($pivotData);
                    
                    Log::info('Jornadas sincronizadas exitosamente', [
                        'instructor_id' => $instructor->id,
                        'resultado_sync' => $resultado,
                        'jornadas_actuales' => $instructor->jornadas()->pluck('jornadas_formacion.id')->toArray()
                    ]);
                } else {
                    // Si no hay jornadas seleccionadas, eliminar todas
                    Log::info('No hay jornadas seleccionadas, eliminando todas', [
                        'instructor_id' => $instructor->id
                    ]);
                    $instructor->jornadas()->detach();
                }
            } catch (\Exception $e) {
                Log::error('Error al sincronizar jornadas del instructor', [
                    'instructor_id' => $instructor->id,
                    'error' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'trace' => $e->getTraceAsString(),
                    'jornadas_ids' => $jornadasIds
                ]);
                // No lanzar la excepción para que la actualización continúe
            }

            DB::commit();

            Log::info('Instructor actualizado exitosamente', [
                'instructor_id' => $instructor->id,
                'persona_id' => $instructor->persona_id,
            ]);

            return redirect()
                ->route('instructor.index')
                ->with('success', '¡Instructor actualizado exitosamente!');
        } catch (QueryException $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
            
            Log::error('Error al actualizar instructor - QueryException', [
                'instructor_id' => $instructor->id,
                'error' => $errorMessage,
                'error_code' => $errorCode,
                'sql_state' => $e->errorInfo[0] ?? null,
                'sql_code' => $e->errorInfo[1] ?? null,
                'sql_message' => $e->errorInfo[2] ?? null,
                'request_data' => $request->except(['password']),
                'datos_actualizar' => $datosActualizar ?? [],
                'trace' => $e->getTraceAsString()
            ]);
            
            // Mensaje más descriptivo basado en el tipo de error
            $mensajeError = 'Error de base de datos. Por favor, inténtelo de nuevo.';
            if (str_contains($errorMessage, 'foreign key constraint')) {
                $mensajeError = 'Error: Uno de los valores seleccionados no es válido. Verifique las relaciones (regional, centro, tipo vinculación, nivel académico).';
            } elseif (str_contains($errorMessage, 'Integrity constraint violation')) {
                $mensajeError = 'Error: Violación de integridad de datos. Verifique que todos los valores sean válidos.';
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $mensajeError);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar instructor - Exception', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->except(['password']),
                'datos_actualizar' => $datosActualizar ?? [],
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }
    public function ApiUpdate(Request $request)
    {
        try {
            DB::beginTransaction();
            $personaD = Persona::find($request->persona_id);
            $personaD->update([
                'tipo_documento' => $request->tipo_documento,
                'numero_documento' => $request->numero_documento,
                'primer_nombre' => $request->primer_nombre,
                'segundo_nombre' => $request->segundo_nombre,
                'primer_apellido' => $request->primer_apellido,
                'segundo_apellido' => $request->segundo_apellido,
                'fecha_de_nacimiento' => $request->fecha_de_nacimiento,
                'genero' => $request->genero,
                'email' => $request->email,
            ]);
            // actualizar Usuario asociado a la Persona
            // Actualizar Usuario asociado a la Persona
            $user = User::where('persona_id', $request->persona_id)->first();
            if ($user) {
                $user->update([
                    'email' => $request->email,
                    'password' => Hash::make($request->numero_documento),
                ]);
                // Refrescar el modelo del usuario
                $user = $user->fresh();
            }
            DB::commit();
            $user->refresh();
            $token = $user->createToken('Token Name')->plainTextToken; // Generar el token
            $personaD->refresh();
            $persona = [
                "id" => $personaD->id,
                "tipo_documento" => $personaD->tipoDocumento->name,
                "numero_documento" => $personaD->numero_documento,
                "primer_nombre" => $personaD->primer_nombre,
                "segundo_nombre" => $personaD->segundo_nombre,
                "primer_apellido" => $personaD->primer_apellido,
                "segundo_apellido" => $personaD->segundo_apellido,
                "fecha_de_nacimiento" => $personaD->fecha_de_nacimiento,
                "genero" => $personaD->tipoGenero->name,
                "email" => $personaD->email,
                "created_at" => $personaD->created_at,
                "updated_at" => $personaD->updated_at,
                "instructor_id" => $personaD->instructor->id,
                "regional_id" => $personaD->instructor->regional->id,
            ];
            // Retornar la respuesta JSON incluyendo el token
            return response()->json(['user' => $user, 'persona' => $persona, 'token' => $token], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instructor $instructor)
    {
        try {
            $this->instructorService->eliminar($instructor->id);

            return redirect()
                ->route('instructor.index')
                ->with('success', 'Instructor eliminado exitosamente.');
        } catch (QueryException $e) {
            Log::error('Error al eliminar instructor: ' . $e->getMessage());

            if ($e->getCode() == 23000) {
                return redirect()
                    ->back()
                    ->with('error', 'El instructor se encuentra en uso, no se puede eliminar');
            }

            return redirect()
                ->back()
                ->with('error', 'Error de base de datos al eliminar el instructor.');
        } catch (Exception $e) {
            Log::error('Error al eliminar instructor: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
    public function createImportarCSV()
    {
        return view('Instructores.createImportarCSV');
    }
    public function storeImportarCSV(Request $request)
    {
        try {
            // Validar que el archivo subido sea un archivo CSV o TXT
            $request->validate([
                'archivoCSV' => 'required|file|mimes:csv,txt|max:' . self::MAX_IMPORT_FILE_KB,
            ]);

            // Obtener el archivo subido
            $archivo = $request->file('archivoCSV');
            $csvData = file_get_contents($archivo);

            // Eliminar el BOM (Byte Order Mark) si está presente al inicio del archivo
            if (substr($csvData, 0, 3) === "\u{FEFF}") {
                $csvData = substr($csvData, 3);
            }

            // Convertir el contenido del archivo CSV en un array de filas, usando punto y coma como delimitador
            $rows = array_map(function ($row) {
                return str_getcsv($row, ';');
            }, explode("\n", $csvData));

            // Extraer el encabezado (primera fila) del CSV
            $header = array_shift($rows);

            // Eliminar espacios en blanco alrededor de cada elemento del encabezado y convertirlo a mayúsculas
            $header = array_map('trim', $header);
            $header = array_map('strtoupper', $header);

            // Definir el encabezado esperado
            $expectedHeader = ['TITLE', 'ID_PERSONAL', self::CSV_HEADER_EMAIL];

            // Comprobar si el encabezado del CSV coincide con el encabezado esperado
            if ($header !== $expectedHeader) {
                return redirect()
                    ->back()
                    ->with('error', 'El encabezado del archivo CSV no coincide con el formato esperado.');
            }

            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            $errores = [];
            $procesados = 0;

            // Procesar cada fila de datos del CSV
            foreach ($rows as $row) {
                // Verificar que la cantidad de columnas en la fila
                // coincida con la cantidad de columnas en el encabezado
                if (count($row) != count($header)) {
                    $errores[] = $row;
                    continue;
                }

                // Combinar el encabezado con los datos de la fila
                $data = array_combine($header, $row);

                try {
                    // Crear una nueva entrada en la tabla `Persona`
                    $persona = Persona::create([
                        'tipo_documento' => 8,
                        'numero_documento' => $data['ID_PERSONAL'],
                        'primer_nombre' => $data['TITLE'],
                        'genero' => 11,
                        'email' => $data[self::CSV_HEADER_EMAIL],
                    ]);

                    // Crear una nueva entrada en la tabla `User`
                    $user = User::create([
                        'email' => $data[self::CSV_HEADER_EMAIL],
                        'password' => Hash::make($data['ID_PERSONAL']),
                        'persona_id' => $persona->id,
                    ]);

                    // Asignar el rol de 'INSTRUCTOR' al usuario
                    $user->assignRole('INSTRUCTOR');
                    
                    // Enviar email de verificación automáticamente
                    $user->sendEmailVerificationNotification();

                    // Crear una nueva entrada en la tabla `Instructor`
                    Instructor::create([
                        'persona_id' => $persona->id,
                        'regional_id' => 1,
                    ]);

                    // Incrementar el contador de registros procesados con éxito
                    $procesados++;
                } catch (Exception $e) {
                    // Si ocurre un error, agregar la fila a la lista de errores y continuar con la siguiente
                    $errores[] = $data;
                    continue;
                }
            }

            // Confirmar la transacción de base de datos
            DB::commit();

            // Preparar el mensaje de éxito
            $mensaje = 'Instructores creados exitosamente: ' . $procesados;

            // Si hubo errores, agregar una nota al mensaje de éxito
            if (!empty($errores)) {
                $mensaje .= '. Algunos registros no pudieron ser procesados.';
            }

            // Mostrar la vista con los errores y el mensaje de éxito
            return view('Instructores.errorImport', compact('errores'))
                ->with('success', $mensaje);
        } catch (QueryException $e) {
            // Si ocurre un error en la base de datos, revertir la transacción
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Error en la base de datos: ' . $e->getMessage());
        } catch (Exception $e) {
            // Si ocurre un error general, revertir la transacción
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }


    public function deleteWithoudUser($id)
    {
        try {
            DB::beginTransaction();

            // Buscar el instructor por persona_id
            $instructor = Instructor::where('persona_id', $id)->first();

            if (!$instructor) {
                return redirect()
                    ->back()
                    ->with('error', 'No se encontró el instructor.');
            }

            // Eliminar solo el instructor
            $instructor->delete();

            DB::commit();

            Log::info('Instructor sin usuario eliminado exitosamente', [
                'instructor_id' => $instructor->id,
                'persona_id' => $id
            ]);

            return redirect()
                ->back()
                ->with('success', 'Instructor eliminado exitosamente. La persona se mantiene intacta.');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error al eliminar instructor sin usuario', [
                'persona_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al eliminar el instructor.');
        }
    }

    /**
     * Mostrar especialidades del instructor
     */
    public function especialidades(Instructor $instructor)
    {
        $this->authorize('gestionarEspecialidades', $instructor);

        $especialidades = $instructor->especialidades;

        return view('instructores.especialidades', compact('instructor', 'especialidades'));
    }

    /**
     * Gestionar especialidades del instructor
     */
    public function gestionarEspecialidades(Instructor $instructor)
    {
        $this->authorize('gestionarEspecialidades', $instructor);

        // Obtener redes de conocimiento disponibles según la regional del instructor
        $redesConocimiento = RedConocimiento::where('regionals_id', $instructor->regional_id)
            ->where('status', true)
            ->orderBy('nombre')
            ->get();

        // Obtener especialidades actuales del instructor
        $especialidadesActuales = $instructor->especialidades ?? [];

        // Separar especialidades principales y secundarias (ahora son IDs)
        $especialidadPrincipalId = $especialidadesActuales['principal'] ?? null;
        $especialidadesSecundariasIds = $especialidadesActuales['secundarias'] ?? [];
        
        // Obtener los nombres de las especialidades basándose en los IDs
        $especialidadPrincipal = null;
        if ($especialidadPrincipalId) {
            $redConocimiento = RedConocimiento::find($especialidadPrincipalId);
            $especialidadPrincipal = $redConocimiento ? $redConocimiento->nombre : null;
        }
        
        $especialidadesSecundarias = [];
        if (!empty($especialidadesSecundariasIds)) {
            $redesConocimiento = RedConocimiento::whereIn('id', $especialidadesSecundariasIds)->get();
            $especialidadesSecundarias = $redesConocimiento->pluck('nombre')->toArray();
        }

        return view(
            'instructores.gestionar-especialidades',
            compact(
                'instructor',
                'redesConocimiento',
                'especialidadPrincipal',
                'especialidadesSecundarias'
            )
        );
    }

    /**
     * Asignar especialidad al instructor
     */
    public function asignarEspecialidad(Request $request, Instructor $instructor)
    {
        $this->authorize('gestionarEspecialidades', $instructor);

        $request->validate([
            'red_conocimiento_id' => 'required|exists:red_conocimientos,id',
            'tipo' => 'required|in:principal,secundaria'
        ]);

        try {
            DB::beginTransaction();

            // Validar que la red de conocimiento pertenezca a la regional del instructor
            $redConocimiento = RedConocimiento::where('id', $request->red_conocimiento_id)
                ->where('regionals_id', $instructor->regional_id)
                ->where('status', true)
                ->first();

            if (!$redConocimiento) {
                return redirect()
                    ->back()
                    ->with('error', 'La red de conocimiento no está disponible para esta regional');
            }

            $especialidadesActuales = $instructor->especialidades ?? [];
            $redConocimientoId = $redConocimiento->id;

            if ($request->tipo === 'principal') {
                // Verificar que no esté ya asignada como secundaria
                $especialidadesSecundarias = $especialidadesActuales['secundarias'] ?? [];
                if (in_array($redConocimientoId, $especialidadesSecundarias)) {
                    // Remover de secundarias antes de asignar como principal
                    $especialidadesSecundarias = array_filter(
                        $especialidadesSecundarias,
                        function ($espId) use ($redConocimientoId) {
                            return $espId !== $redConocimientoId;
                        }
                    );
                    $especialidadesActuales['secundarias'] = array_values($especialidadesSecundarias);
                }

                // Solo puede haber una especialidad principal (guardar ID, no nombre)
                $especialidadesActuales['principal'] = $redConocimientoId;
                $mensaje = "Especialidad principal '{$redConocimiento->nombre}' asignada exitosamente";
            } else {
                // Agregar especialidad secundaria
                $especialidadesSecundarias = $especialidadesActuales['secundarias'] ?? [];

                // Verificar que no sea la misma especialidad principal
                if ($especialidadesActuales['principal'] == $redConocimientoId) {
                    return redirect()
                        ->back()
                        ->with('warning', 'Esta especialidad ya está asignada como principal');
                }

                // Verificar que no esté ya en secundarias
                if (in_array($redConocimientoId, $especialidadesSecundarias)) {
                    return redirect()
                        ->back()
                        ->with('warning', 'Esta especialidad ya está asignada como secundaria');
                }

                $especialidadesSecundarias[] = $redConocimientoId;
                $especialidadesActuales['secundarias'] = $especialidadesSecundarias;
                $mensaje = "Especialidad secundaria '{$redConocimiento->nombre}' asignada exitosamente";
            }

            $instructor->especialidades = $especialidadesActuales;
            $instructor->save();

            DB::commit();

            Log::info('Especialidad asignada exitosamente', [
                'instructor_id' => $instructor->id,
                'especialidad' => $redConocimiento->nombre,
                'tipo' => $request->tipo,
                'especialidades_actuales' => $especialidadesActuales
            ]);

            return redirect()
                ->back()
                ->with('success', $mensaje);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al asignar especialidad', [
                'instructor_id' => $instructor->id,
                'red_conocimiento_id' => $request->red_conocimiento_id,
                'tipo' => $request->tipo,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->with('error', 'Error al asignar la especialidad. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Remover especialidad del instructor
     */
    public function removerEspecialidad(Request $request, Instructor $instructor)
    {
        $this->authorize('gestionarEspecialidades', $instructor);

        $request->validate([
            'especialidad' => 'required|integer|exists:red_conocimientos,id',
            'tipo' => 'required|in:principal,secundaria'
        ]);

        try {
            DB::beginTransaction();

            $especialidadesActuales = $instructor->especialidades ?? [];
            $especialidadId = (int) $request->especialidad;
            
            // Obtener el nombre de la especialidad para el mensaje
            $redConocimiento = RedConocimiento::find($especialidadId);
            $especialidadNombre = $redConocimiento ? $redConocimiento->nombre : 'N/A';

            if ($request->tipo === 'principal') {
                // Verificar que la especialidad principal existe
                if ($especialidadesActuales['principal'] != $especialidadId) {
                    return redirect()
                        ->back()
                        ->with('error', 'La especialidad principal especificada no coincide');
                }

                $especialidadesActuales['principal'] = null;
                $mensaje = "Especialidad principal '{$especialidadNombre}' removida exitosamente";

                Log::info('Especialidad principal removida', [
                    'instructor_id' => $instructor->id,
                    'especialidad_removida_id' => $especialidadId,
                    'especialidad_removida_nombre' => $especialidadNombre
                ]);
            } else {
                $especialidadesSecundarias = $especialidadesActuales['secundarias'] ?? [];

                // Verificar que la especialidad secundaria existe
                if (!in_array($especialidadId, $especialidadesSecundarias)) {
                    return redirect()
                        ->back()
                        ->with('error', 'La especialidad secundaria especificada no existe');
                }

                // Remover la especialidad de la lista (comparar IDs, no nombres)
                $especialidadesSecundarias = array_filter(
                    $especialidadesSecundarias,
                    function ($espId) use ($especialidadId) {
                        return $espId != $especialidadId;
                    }
                );
                $especialidadesActuales['secundarias'] = array_values($especialidadesSecundarias);
                $mensaje = "Especialidad secundaria '{$especialidadNombre}' removida exitosamente";

                Log::info('Especialidad secundaria removida', [
                    'instructor_id' => $instructor->id,
                    'especialidad_removida_id' => $especialidadId,
                    'especialidad_removida_nombre' => $especialidadNombre,
                    'especialidades_restantes' => $especialidadesSecundarias
                ]);
            }

            $instructor->especialidades = $especialidadesActuales;
            $instructor->save();

            DB::commit();

            return redirect()
                ->back()
                ->with('success', $mensaje);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al remover especialidad', [
                'instructor_id' => $instructor->id,
                'especialidad' => $request->especialidad,
                'tipo' => $request->tipo,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->with('error', 'Error al remover la especialidad. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Dashboard específico para instructores
     */
    public function dashboard(Request $request)
    {
        try {
            $user = Auth::user();
            $instructor = $user->instructor;

            if (!$instructor) {
                return redirect()
                    ->back()
                    ->with('error', 'No se encontró información del instructor');
            }

            // Autorizar acceso
            $this->authorize('viewAny', Instructor::class);

            // Obtener fichas activas
            $fichasActivas = $instructor->instructorFichas()
                ->with(['ficha' => function ($q) {
                    $q->with([
                        'programaFormacion.redConocimiento',
                        'modalidadFormacion',
                        'ambiente.piso.bloque.sede',
                        'jornadaFormacion',
                        'diasFormacion'
                    ]);
                }])
                ->whereHas('ficha', function ($q) {
                    $q->where('status', true)
                        ->where('fecha_fin', '>=', now()->toDateString());
                })
                ->orderBy('fecha_inicio')
                ->get();

            // Obtener fichas próximas (próximos 30 días)
            $fichasProximas = $instructor->instructorFichas()
                ->with(['ficha' => function ($q) {
                    $q->with([
                        'programaFormacion.redConocimiento',
                        'modalidadFormacion',
                        'ambiente.piso.bloque.sede',
                        'jornadaFormacion',
                        'diasFormacion'
                    ]);
                }])
                ->whereHas('ficha', function ($q) {
                    $q->where('status', true)
                        ->where('fecha_inicio', '>=', now()->toDateString())
                        ->where('fecha_inicio', '<=', now()->addDays(30)->toDateString());
                })
                ->orderBy('fecha_inicio')
                ->get();

            // Obtener estadísticas de desempeño
            $estadisticas = $this->obtenerEstadisticasDesempeno($instructor);

            // Obtener eventos del calendario (clases)
            $eventosCalendario = $this->obtenerEventosCalendario($instructor);

            // Obtener notificaciones recientes
            $notificaciones = $this->obtenerNotificacionesRecientes();

            // Obtener resumen de actividades
            $actividadesRecientes = $this->obtenerActividadesRecientes($instructor);

            return view(
                'instructores.dashboard',
                compact(
                    'instructor',
                    'fichasActivas',
                    'fichasProximas',
                    'estadisticas',
                    'eventosCalendario',
                    'notificaciones',
                    'actividadesRecientes'
                )
            );
        } catch (Exception $e) {
            Log::error('Error cargando dashboard del instructor', [
                'instructor_id' => Auth::user()->instructor?->id,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->with('error', 'Error al cargar el dashboard del instructor');
        }
    }

    /**
     * Obtener estadísticas de desempeño del instructor
     */
    private function obtenerEstadisticasDesempeno(Instructor $instructor): array
    {
        $resumenFichas = $this->businessRulesService->obtenerResumenFichas($instructor);
        $horasEsteMes = $this->businessRulesService->sumarHorasDelMes($instructor, now());
        $promedioHorasUltimosMeses = $this->businessRulesService->promedioHorasUltimosMeses($instructor, 6);

        return [
            'fichas_activas' => $resumenFichas['activas'],
            'fichas_proximas' => $resumenFichas['proximas'],
            'fichas_finalizadas' => $resumenFichas['finalizadas'],
            'total_horas' => $resumenFichas['total_horas'],
            'horas_este_mes' => $horasEsteMes,
            'promedio_horas_mes' => $promedioHorasUltimosMeses,
            'anos_experiencia' => $instructor->anos_experiencia ?? 0,
            'especialidades' => count($instructor->especialidades['secundarias'] ?? []) +
                (empty($instructor->especialidades['principal']) ? 0 : 1)
        ];
    }

    /**
     * Obtener eventos del calendario para el instructor
     */
    private function obtenerEventosCalendario(Instructor $instructor): array
    {
        $eventos = [];

        $fichasActivas = $instructor->instructorFichas()
            ->with(['ficha.diasFormacion'])
            ->whereHas('ficha', function ($q) {
                $q->where('status', true)
                    ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->get();

        foreach ($fichasActivas as $instructorFicha) {
            $ficha = $instructorFicha->ficha;

            foreach ($ficha->diasFormacion as $diaFormacion) {
                $fechaInicio = Carbon::parse($ficha->fecha_inicio);
                $fechaFin = Carbon::parse($ficha->fecha_fin);

                // Generar eventos para cada día de formación en el rango
                $fechaActual = $fechaInicio->copy();
                while ($fechaActual->lte($fechaFin)) {
                    if ($this->esDiaFormacion($fechaActual, $diaFormacion->dia_nombre)) {
                        $eventos[] = [
                            'title' => $ficha->programaFormacion->nombre ?? 'Sin programa',
                            'start' => $fechaActual->format('Y-m-d') . 'T' . $diaFormacion->hora_inicio,
                            'end' => $fechaActual->format('Y-m-d') . 'T' . $diaFormacion->hora_fin,
                            'backgroundColor' => $this->obtenerColorPorEspecialidad(
                                $ficha->programaFormacion->redConocimiento->nombre ?? ''
                            ),
                            'borderColor' => $this->obtenerColorPorEspecialidad(
                                $ficha->programaFormacion->redConocimiento->nombre ?? ''
                            ),
                            'extendedProps' => [
                                'ficha_id' => $ficha->id,
                                'ambiente' => $ficha->ambiente->nombre ?? 'Sin ambiente',
                                'sede' => $ficha->ambiente->sede->nombre ?? 'Sin sede',
                                'modalidad' => $ficha->modalidadFormacion->nombre ?? 'Sin modalidad'
                            ]
                        ];
                    }
                    $fechaActual->addDay();
                }
            }
        }

        return $eventos;
    }

    /**
     * Verificar si una fecha corresponde a un día de formación
     */
    private function esDiaFormacion(Carbon $fecha, string $diaNombre): bool
    {
        $diasSemana = [
            'Lunes' => 1,
            'Martes' => 2,
            'Miércoles' => 3,
            'Jueves' => 4,
            'Viernes' => 5,
            'Sábado' => 6,
            'Domingo' => 0
        ];

        return $fecha->dayOfWeek === ($diasSemana[$diaNombre] ?? -1);
    }

    /**
     * Obtener color por especialidad para el calendario
     */
    private function obtenerColorPorEspecialidad(string $especialidad): string
    {
        $colores = [
            'Tecnologías de la Información y las Comunicaciones' => '#3498db',
            'Electrónica' => '#e74c3c',
            'Mecánica Industrial' => '#f39c12',
            'Construcción' => '#2ecc71',
            'Gastronomía' => '#9b59b6',
            'Agropecuaria' => '#27ae60',
            'Comercio' => '#34495e'
        ];

        return $colores[$especialidad] ?? '#95a5a6';
    }

    /**
     * Obtener notificaciones recientes para el instructor
     */
    private function obtenerNotificacionesRecientes(): array
    {
        // Simular notificaciones - en un sistema real vendrían de una tabla de notificaciones
        return [
            [
                'id' => 1,
                'titulo' => 'Nueva ficha asignada',
                'mensaje' => 'Se te ha asignado una nueva ficha de programación web',
                'tipo' => 'success',
                'fecha' => now()->subHours(2),
                'leida' => false
            ],
            [
                'id' => 2,
                'titulo' => 'Recordatorio de clase',
                'mensaje' => 'Tienes una clase programada mañana a las 8:00 AM',
                'tipo' => 'info',
                'fecha' => now()->subHours(5),
                'leida' => false
            ],
            [
                'id' => 3,
                'titulo' => 'Evaluación pendiente',
                'mensaje' => 'Debes completar la evaluación de la ficha 12345',
                'tipo' => 'warning',
                'fecha' => now()->subDays(1),
                'leida' => true
            ]
        ];
    }

    /**
     * Obtener actividades recientes del instructor
     */
    private function obtenerActividadesRecientes(Instructor $instructor): array
    {
        $actividades = [];

        // Obtener fichas recientes
        $fichasRecientes = $instructor->instructorFichas()
            ->with(['ficha.programaFormacion'])
            ->whereHas('ficha')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($fichasRecientes as $instructorFicha) {
            $actividades[] = [
                'tipo' => 'ficha_asignada',
                'titulo' => 'Ficha asignada',
                'descripcion' => 'Se asignó la ficha ' . $instructorFicha->ficha->ficha . ' - ' .
                    ($instructorFicha->ficha->programaFormacion->nombre ?? 'Sin programa'),
                'fecha' => $instructorFicha->created_at,
                'icono' => 'fas fa-clipboard-list'
            ];
        }

        return $actividades;
    }

    /**
     * Ver fichas asignadas al instructor
     */
    public function fichasAsignadas(Request $request, ?Instructor $instructor = null)
    {
        $user = Auth::user();

        // Autorizar acceso usando la política
        if ($instructor) {
            $this->authorize('verFichasAsignadas', $instructor);
            $instructorActual = $instructor;
        } else {
            // Si no se especifica instructor, usar el del usuario autenticado
            $instructorActual = $user->instructor;
            if ($instructorActual) {
                $this->authorize('verFichasAsignadas', $instructorActual);
            }
        }

        if (!$instructorActual) {
            return redirect()->back()->with('error', 'No se encontró información del instructor');
        }

        // Obtener parámetros de filtro
        $filtroEstado = $request->get('estado', 'todas');
        $filtroFechaInicio = $request->get('fecha_inicio');
        $filtroFechaFin = $request->get('fecha_fin');
        $filtroPrograma = $request->get('programa');

        // Construir query base con relaciones
        $query = $instructorActual->instructorFichas()
            ->with([
                'ficha' => function ($q) {
                    $q->with([
                        'programaFormacion.redConocimiento',
                        'modalidadFormacion',
                        'ambiente.piso.bloque.sede',
                        'jornadaFormacion',
                        'diasFormacion'
                    ]);
                }
            ]);

        // Aplicar filtros
        if ($filtroEstado !== 'todas') {
            if ($filtroEstado === 'activas') {
                $query->whereHas('ficha', function ($q) {
                    $q->where('status', true)
                        ->where('fecha_fin', '>=', now()->toDateString());
                });
            } elseif ($filtroEstado === 'finalizadas') {
                $query->whereHas('ficha', function ($q) {
                    $q->where('fecha_fin', '<', now()->toDateString());
                });
            } elseif ($filtroEstado === 'inactivas') {
                $query->whereHas('ficha', function ($q) {
                    $q->where('status', false);
                });
            }
        }

        if ($filtroFechaInicio) {
            $query->whereHas('ficha', function ($q) use ($filtroFechaInicio) {
                $q->where('fecha_inicio', '>=', $filtroFechaInicio);
            });
        }

        if ($filtroFechaFin) {
            $query->whereHas('ficha', function ($q) use ($filtroFechaFin) {
                $q->where('fecha_fin', '<=', $filtroFechaFin);
            });
        }

        if ($filtroPrograma) {
            $query->whereHas('ficha.programaFormacion', function ($q) use ($filtroPrograma) {
                $q->where('nombre', 'like', "%{$filtroPrograma}%");
            });
        }

        // Ordenar por fecha de inicio descendente
        $query->orderBy('fecha_inicio', 'desc');

        // Paginar resultados
        $fichasAsignadas = $query->paginate(15)->withQueryString();

        // Obtener estadísticas
        $resumenFichas = $this->businessRulesService->obtenerResumenFichas($instructorActual);
        $estadisticas = [
            'total' => $resumenFichas['total'],
            'activas' => $resumenFichas['activas'],
            'finalizadas' => $resumenFichas['finalizadas'],
            'total_horas' => $resumenFichas['total_horas']
        ];

        // Obtener programas únicos para el filtro
        $programas = $instructorActual->instructorFichas()
            ->with('ficha.programaFormacion')
            ->get()
            ->pluck('ficha.programaFormacion.nombre')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view(
            'instructores.fichas-asignadas',
            compact(
                'instructorActual',
                'fichasAsignadas',
                'estadisticas',
                'programas',
                'filtroEstado',
                'filtroFechaInicio',
                'filtroFechaFin',
                'filtroPrograma'
            )
        );
    }

    /**
     * Cambiar estado del instructor
     */
    public function cambiarEstado(Request $request, Instructor $instructor)
    {
        $this->authorize('cambiarEstado', $instructor);

        $request->validate([
            'estado' => 'required|in:activo,inactivo'
        ]);

        try {
            $instructor->update(['estado' => $request->estado]);

            $mensaje = $request->estado === 'activo'
                ? 'Instructor activado exitosamente'
                : 'Instructor desactivado exitosamente';

            return redirect()
                ->back()
                ->with('success', $mensaje);
        } catch (Exception $e) {
            Log::error('Error al cambiar estado del instructor: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Error al cambiar el estado del instructor');
        }
    }

    /**
     * Verificar disponibilidad del instructor para una nueva ficha
     */
    public function verificarDisponibilidad(VerificarDisponibilidadRequest $request, Instructor $instructor)
    {
        try {
            $datosFicha = $request->validated();

            $disponibilidad = $this->businessRulesService->verificarDisponibilidad($instructor, $datosFicha);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'disponibilidad' => $disponibilidad
                ]);
            }

            return response()->json($disponibilidad);
        } catch (Exception $e) {
            Log::error('Error verificando disponibilidad del instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al verificar disponibilidad'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al verificar disponibilidad del instructor');
        }
    }

    /**
     * Obtener instructores disponibles para una ficha específica
     */
    public function instructoresDisponibles(InstructoresDisponiblesRequest $request)
    {
        try {
            $criterios = $request->validated();
            $instructoresDisponibles = $this->businessRulesService->obtenerInstructoresDisponibles($criterios);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'instructores' => $instructoresDisponibles,
                    'total' => count($instructoresDisponibles)
                ]);
            }

            return view('instructores.disponibles', compact('instructoresDisponibles', 'criterios'));
        } catch (Exception $e) {
            Log::error('Error obteniendo instructores disponibles', [
                'error' => $e->getMessage(),
                'criterios' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener instructores disponibles'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al obtener instructores disponibles');
        }
    }

    /**
     * Validar reglas SENA para asignación de ficha
     */
    public function validarReglasSENA(Request $request, Instructor $instructor)
    {
        try {
            $request->validate([
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'especialidad_requerida' => 'nullable|string',
                'regional_id' => 'nullable|integer|exists:regionals,id'
            ]);

            $datosFicha = $request->only(['fecha_inicio', 'fecha_fin', 'especialidad_requerida', 'regional_id']);
            $validacion = $this->businessRulesService->validarReglasSENA($instructor, $datosFicha);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'validacion' => $validacion
                ]);
            }

            return response()->json($validacion);
        } catch (Exception $e) {
            Log::error('Error validando reglas SENA', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al validar reglas de negocio'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al validar reglas de negocio');
        }
    }

    /**
     * Obtener estadísticas de carga de trabajo
     */
    public function estadisticasCargaTrabajo(Request $request)
    {
        try {
            $estadisticas = $this->businessRulesService->obtenerEstadisticasCargaTrabajo();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'estadisticas' => $estadisticas
                ]);
            }

            return view('instructores.estadisticas-carga', compact('estadisticas'));
        } catch (Exception $e) {
            Log::error('Error obteniendo estadísticas de carga', [
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener estadísticas'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al obtener estadísticas de carga de trabajo');
        }
    }

    /**
     * Asignar ficha a instructor con validaciones de negocio
     */
    public function asignarFicha(Request $request, Instructor $instructor)
    {
        try {
            $request->validate([
                'ficha_id' => 'required|integer|exists:ficha_caracterizacions,id',
                'total_horas_instructor' => 'required|integer|min:1|max:1000',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
            ]);

            $ficha = FichaCaracterizacion::findOrFail($request->ficha_id);

            // Verificar que la ficha esté activa
            if (!$ficha->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'La ficha seleccionada no está activa'
                ], 400);
            }

            // Validar disponibilidad del instructor
            $datosFicha = [
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'especialidad_requerida' => $ficha->programaFormacion->redConocimiento->nombre ?? null,
                'regional_id' => $ficha->regional_id,
                'horas_semanales' => $request->total_horas_instructor
            ];

            $disponibilidad = $this->businessRulesService->verificarDisponibilidad($instructor, $datosFicha);

            if (!$disponibilidad['disponible']) {
                return response()->json([
                    'success' => false,
                    'message' => 'El instructor no está disponible para esta ficha',
                    'razones' => $disponibilidad['razones'],
                    'conflictos' => $disponibilidad['conflictos'] ?? []
                ], 400);
            }

            // Validar reglas SENA
            $validacionSENA = $this->businessRulesService->validarReglasSENA($instructor, $datosFicha);

            if (!$validacionSENA['valido']) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se cumplen las reglas de negocio del SENA',
                    'errores' => $validacionSENA['errores']
                ], 400);
            }

            // Crear la asignación
            $instructorFicha = $instructor->instructorFichas()->create([
                'ficha_caracterizacion_id' => $ficha->id,
                'total_horas_instructor' => $request->total_horas_instructor,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'status' => true,
                'user_create_id' => Auth::id()
            ]);

            Log::info('Ficha asignada al instructor', [
                'instructor_id' => $instructor->id,
                'ficha_id' => $ficha->id,
                'total_horas' => $request->total_horas_instructor
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ficha asignada exitosamente al instructor',
                'asignacion' => $instructorFicha,
                'advertencias' => $validacionSENA['advertencias'] ?? []
            ]);
        } catch (Exception $e) {
            Log::error('Error asignando ficha al instructor', [
                'instructor_id' => $instructor->id,
                'ficha_id' => $request->ficha_id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al asignar la ficha al instructor'
            ], 500);
        }
    }

    /**
     * Desasignar ficha del instructor
     */
    public function desasignarFicha(Request $request, Instructor $instructor, $fichaId)
    {
        try {
            $instructorFicha = $instructor->instructorFichas()
                ->where('ficha_caracterizacion_id', $fichaId)
                ->first();

            if (!$instructorFicha) {
                return response()->json([
                    'success' => false,
                    'message' => 'La ficha no está asignada a este instructor'
                ], 404);
            }

            // Verificar que la ficha no haya comenzado aún
            if (Carbon::parse($instructorFicha->fecha_inicio)->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede desasignar una ficha que ya ha comenzado'
                ], 400);
            }

            $instructorFicha->update([
                'status' => false,
                'user_edit_id' => Auth::id()
            ]);

            Log::info('Ficha desasignada del instructor', [
                'instructor_id' => $instructor->id,
                'ficha_id' => $fichaId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ficha desasignada exitosamente del instructor'
            ]);
        } catch (Exception $e) {
            Log::error('Error desasignando ficha del instructor', [
                'instructor_id' => $instructor->id,
                'ficha_id' => $fichaId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al desasignar la ficha del instructor'
            ], 500);
        }
    }

    /**
     * Obtiene centros de formación por regional (AJAX)
     */
    public function centrosPorRegional(Request $request)
    {
        try {
            $regionalId = $request->input('regional_id');
            
            Log::info('Solicitud de centros por regional recibida', [
                'regional_id' => $regionalId,
                'tipo' => gettype($regionalId),
                'request_all' => $request->all()
            ]);
            
            if (!$regionalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de regional requerido'
                ], 400);
            }

            // Convertir a entero para asegurar el tipo correcto
            $regionalId = (int) $regionalId;
            
            Log::info('Buscando centros para regional', [
                'regional_id' => $regionalId
            ]);

            $centros = \App\Models\CentroFormacion::where('regional_id', $regionalId)
                ->where('status', true)
                ->orderBy('nombre')
                ->get(['id', 'nombre'])
                ->map(function ($centro) {
                    return [
                        'id' => (int) $centro->id,
                        'nombre' => $centro->nombre
                    ];
                })
                ->values();

            Log::info('Centros obtenidos por regional', [
                'regional_id' => $regionalId,
                'cantidad' => $centros->count(),
                'centros' => $centros->toArray()
            ]);

            return response()->json([
                'success' => true,
                'centros' => $centros
            ]);
        } catch (Exception $e) {
            Log::error('Error al obtener centros por regional', [
                'regional_id' => $request->input('regional_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener centros de formación: ' . $e->getMessage()
            ], 500);
        }
    }
}

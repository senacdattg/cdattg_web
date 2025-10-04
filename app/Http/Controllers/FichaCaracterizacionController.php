<?php

namespace App\Http\Controllers;

use App\Models\FichaCaracterizacion;
use App\Models\ProgramaFormacion;
use App\Models\InstructorFichaCaracterizacion;
use App\Http\Requests\StoreFichaCaracterizacionRequest;
use App\Http\Requests\UpdateFichaCaracterizacionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FichaCaracterizacionController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica middleware de autenticación y permisos.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:VER PROGRAMA DE CARACTERIZACION')->only(['index', 'show', 'create', 'edit']);
        $this->middleware('can:CREAR PROGRAMA DE CARACTERIZACION')->only(['store']);
        $this->middleware('can:EDITAR PROGRAMA DE CARACTERIZACION')->only(['update']);
        $this->middleware('can:ELIMINAR PROGRAMA DE CARACTERIZACION')->only(['destroy']);
    }

    /**
     * Muestra una lista de todas las fichas de caracterización.
     *
     * Este método recupera todas las fichas de caracterización junto con sus
     * relaciones y las pasa a la vista 'fichas.index'. También incluye datos
     * necesarios para los filtros avanzados.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View La vista que muestra la lista de fichas de caracterización.
     */
    public function index(Request $request)
    {
        try {
            Log::info('Acceso al índice de fichas de caracterización', [
                'user_id' => Auth::id(),
                'filters' => $request->all(),
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

            // Aplicar filtros básicos si están presentes
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where('ficha', 'LIKE', "%{$searchTerm}%");
            }

            if ($request->filled('estado')) {
                $query->where('status', $request->input('estado'));
            }

            $fichas = $query->orderBy('id', 'desc')->paginate(10);

            // Obtener datos para filtros avanzados
            $programas = ProgramaFormacion::orderBy('nombre', 'asc')->get();
            $instructores = \App\Models\Instructor::with('persona')->orderBy('id', 'desc')->get();
            $ambientes = \App\Models\Ambiente::with('piso.bloque')->orderBy('title', 'asc')->get();
            $sedes = \App\Models\Sede::orderBy('nombre', 'asc')->get();
            $modalidades = \App\Models\Parametro::where('tema_id', 3)->orderBy('name', 'asc')->get(); // Modalidades de formación
            $jornadas = \App\Models\JornadaFormacion::orderBy('jornada', 'asc')->get();

            Log::info('Fichas de caracterización cargadas exitosamente', [
                'total_fichas' => $fichas->total(),
                'user_id' => Auth::id()
            ]);

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
     * @return \Illuminate\View\View La vista para crear una nueva ficha de caracterización con los programas de formación.
     */
    public function create()
    {
        try {
            Log::info('Acceso al formulario de creación de ficha de caracterización', [
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            $programas = ProgramaFormacion::orderBy('nombre', 'asc')->get();

            Log::info('Programas de formación cargados para creación de ficha', [
                'total_programas' => $programas->count(),
                'user_id' => Auth::id()
            ]);

            return view('fichas.create', compact('programas'));

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
     * @param \App\Http\Requests\StoreFichaCaracterizacionRequest $request La solicitud HTTP que contiene los datos de la ficha.
     *
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta 'fichaCaracterizacion.index' con un mensaje de éxito.
     */
    public function store(StoreFichaCaracterizacionRequest $request)
    {
        try {
            Log::info('Inicio de creación de nueva ficha de caracterización', [
                'user_id' => Auth::id(),
                'request_data' => $request->validated(),
                'timestamp' => now()
            ]);

            DB::beginTransaction();

            $ficha = new FichaCaracterizacion();
            $ficha->fill($request->validated());
            $ficha->user_create_id = Auth::id();

            if ($ficha->save()) {
                DB::commit();
                
                Log::info('Ficha de caracterización creada exitosamente', [
                    'ficha_id' => $ficha->id,
                    'numero_ficha' => $ficha->ficha,
                    'programa_id' => $ficha->programa_formacion_id,
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
     * @return \Illuminate\View\View La vista del formulario de edición con la ficha de caracterización y los programas de formación.
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
            $programas = ProgramaFormacion::orderBy('nombre', 'asc')->get();

            Log::info('Datos cargados para edición de ficha', [
                'ficha_id' => $ficha->id,
                'numero_ficha' => $ficha->ficha,
                'total_programas' => $programas->count(),
                'user_id' => Auth::id()
            ]);

            return view('fichas.edit', compact('ficha', 'programas'));

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
     * @param \App\Http\Requests\UpdateFichaCaracterizacionRequest $request La solicitud HTTP que contiene los datos de la ficha a actualizar.
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
            Log::info('Inicio de eliminación de ficha de caracterización', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);

            // Verificar si la ficha tiene aprendices asignados
            if ($ficha->tieneAprendices()) {
                Log::warning('Intento de eliminar ficha con aprendices asignados', [
                    'ficha_id' => $id,
                    'numero_ficha' => $ficha->ficha,
                    'aprendices_count' => $ficha->contarAprendices(),
                    'user_id' => Auth::id()
                ]);

                return redirect()->route('fichaCaracterizacion.index')
                    ->with('error', 'No se puede eliminar la ficha porque tiene aprendices asignados.');
            }

            // Guardar información de la ficha antes de eliminar para el log
            $fichaInfo = [
                'id' => $ficha->id,
                'numero_ficha' => $ficha->ficha,
                'programa_formacion_id' => $ficha->programa_formacion_id,
            ];

            DB::beginTransaction();

            if ($ficha->delete()) {
                DB::commit();
                
                Log::info('Ficha de caracterización eliminada exitosamente', [
                    'ficha_eliminada' => $fichaInfo,
                    'user_id' => Auth::id()
                ]);

                return redirect()->route('fichaCaracterizacion.index')
                    ->with('success', 'Ficha de caracterización eliminada exitosamente.');
            }

            DB::rollBack();
            throw new \Exception('Error al eliminar la ficha de la base de datos.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Intento de eliminar ficha de caracterización inexistente', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'La ficha de caracterización solicitada no existe.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al eliminar ficha de caracterización', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'Ocurrió un error al eliminar la ficha de caracterización. Por favor, intente nuevamente.');
        }
    }

    /**
     * Búsqueda avanzada de fichas de caracterización con múltiples filtros.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
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
                            'sede' => $ficha->sede->nombre ?? 'N/A',
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
     * @return \Illuminate\View\View La vista que muestra los detalles de la ficha de caracterización.
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
                    'nombre' => $ficha->modalidadFormacion->nombre ?? 'N/A',
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
                            'nombre' => $dia->dia->nombre ?? 'N/A',
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
                        'nombre' => $ficha->modalidadFormacion->nombre ?? 'N/A',
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
                                'nombre' => $dia->dia->nombre ?? 'N/A',
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
     * Valida si una ficha puede ser eliminada según las reglas de negocio.
     *
     * @param string $id El ID de la ficha a validar.
     * @return \Illuminate\Http\JsonResponse Resultado de la validación.
     */
    public function validarEliminacionFicha(string $id)
    {
        try {
            Log::info('Validación de eliminación de ficha', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);
            
            $validaciones = [
                'tiene_aprendices' => $ficha->tieneAprendices(),
                'cantidad_aprendices' => $ficha->contarAprendices(),
                'tiene_asistencias' => $this->fichaTieneAsistencias($ficha->id),
                'puede_eliminar' => false
            ];

            // La ficha puede ser eliminada si no tiene aprendices ni asistencias
            $validaciones['puede_eliminar'] = !$validaciones['tiene_aprendices'] && !$validaciones['tiene_asistencias'];

            $mensaje = $validaciones['puede_eliminar'] 
                ? 'La ficha puede ser eliminada' 
                : 'La ficha no puede ser eliminada porque tiene dependencias';

            Log::info('Validación de eliminación completada', [
                'ficha_id' => $id,
                'validaciones' => $validaciones,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'data' => $validaciones
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Validación de eliminación para ficha inexistente', [
                'ficha_id' => $id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'La ficha solicitada no existe',
                'data' => null
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error en validación de eliminación de ficha', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al validar la eliminación de la ficha',
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
     * Muestra la vista para gestionar instructores de una ficha.
     *
     * @param int $id El ID de la ficha.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function gestionarInstructores(string $id)
    {
        try {
            Log::info('Acceso a gestión de instructores', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'timestamp' => now()
            ]);

            // Buscar la ficha con sus relaciones
            $ficha = FichaCaracterizacion::with([
                'instructor.persona',
                'instructorFicha.instructor.persona',
                'programaFormacion',
                'sede'
            ])->findOrFail($id);

            // Obtener todos los instructores disponibles
            $instructoresDisponibles = \App\Models\Instructor::with('persona')
                ->where('regional_id', $ficha->sede->regional_id ?? null)
                ->get();

            // Obtener instructores ya asignados a esta ficha
            $instructoresAsignados = $ficha->instructorFicha()
                ->with('instructor.persona')
                ->get();

            // Verificar disponibilidad de instructores
            $instructoresConDisponibilidad = $this->verificarDisponibilidadInstructores($instructoresDisponibles, $ficha);

            Log::info('Datos de gestión de instructores cargados', [
                'ficha_id' => $id,
                'total_instructores_disponibles' => $instructoresDisponibles->count(),
                'instructores_asignados' => $instructoresAsignados->count()
            ]);

            return view('fichas.gestionar-instructores', compact(
                'ficha',
                'instructoresDisponibles',
                'instructoresAsignados',
                'instructoresConDisponibilidad'
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
     * @param \Illuminate\Http\Request $request
     * @param string $id El ID de la ficha.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asignarInstructores(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            Log::info('Iniciando asignación de instructores', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'instructores' => $request->instructores ?? [],
                'timestamp' => now()
            ]);

            // Validar datos
            $request->validate([
                'instructores' => 'required|array|min:1',
                'instructores.*.instructor_id' => 'required|exists:instructors,id',
                'instructores.*.fecha_inicio' => 'required|date',
                'instructores.*.fecha_fin' => 'required|date|after_or_equal:instructores.*.fecha_inicio',
                'instructores.*.total_horas_ficha' => 'required|integer|min:1',
                'instructor_principal_id' => 'required|exists:instructors,id'
            ], [
                'instructores.required' => 'Debe seleccionar al menos un instructor.',
                'instructores.*.instructor_id.required' => 'El instructor es requerido.',
                'instructores.*.instructor_id.exists' => 'El instructor seleccionado no existe.',
                'instructores.*.fecha_inicio.required' => 'La fecha de inicio es requerida.',
                'instructores.*.fecha_fin.required' => 'La fecha de fin es requerida.',
                'instructores.*.fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
                'instructores.*.total_horas_ficha.required' => 'El total de horas es requerido.',
                'instructores.*.total_horas_ficha.min' => 'El total de horas debe ser mayor a 0.',
                'instructor_principal_id.required' => 'Debe seleccionar un instructor principal.',
                'instructor_principal_id.exists' => 'El instructor principal seleccionado no existe.'
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);

            // Verificar que el instructor principal esté en la lista de instructores
            $instructorPrincipalId = $request->instructor_principal_id;
            $instructorPrincipalEnLista = collect($request->instructores)
                ->contains('instructor_id', $instructorPrincipalId);

            if (!$instructorPrincipalEnLista) {
                return back()->withErrors([
                    'instructor_principal_id' => 'El instructor principal debe estar en la lista de instructores asignados.'
                ]);
            }

            // Verificar disponibilidad de instructores
            $instructoresIds = collect($request->instructores)->pluck('instructor_id')->toArray();
            $disponibilidad = $this->verificarDisponibilidadInstructores(
                \App\Models\Instructor::whereIn('id', $instructoresIds)->get(),
                $ficha
            );

            foreach ($instructoresIds as $instructorId) {
                if (!isset($disponibilidad[$instructorId]) || !$disponibilidad[$instructorId]['disponible']) {
                    $instructor = \App\Models\Instructor::find($instructorId);
                    return back()->withErrors([
                        'instructores' => "El instructor {$instructor->persona->primer_nombre} {$instructor->persona->primer_apellido} no está disponible en el rango de fechas especificado."
                    ]);
                }
            }

            // Eliminar asignaciones existentes
            $ficha->instructorFicha()->delete();

            // Actualizar instructor principal en la ficha
            $ficha->update([
                'instructor_id' => $instructorPrincipalId,
                'user_edit_id' => Auth::id()
            ]);

            // Crear nuevas asignaciones
            foreach ($request->instructores as $instructorData) {
                $ficha->instructorFicha()->create([
                    'instructor_id' => $instructorData['instructor_id'],
                    'fecha_inicio' => $instructorData['fecha_inicio'],
                    'fecha_fin' => $instructorData['fecha_fin'],
                    'total_horas_ficha' => $instructorData['total_horas_ficha']
                ]);
            }

            DB::commit();

            Log::info('Instructores asignados exitosamente', [
                'ficha_id' => $id,
                'instructor_principal_id' => $instructorPrincipalId,
                'total_instructores' => count($request->instructores),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('fichaCaracterizacion.show', $id)
                ->with('success', 'Instructores asignados exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Error de validación en asignación de instructores', [
                'ficha_id' => $id,
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            throw $e;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al asignar instructores', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return back()->withErrors([
                'error' => 'Error al asignar instructores: ' . $e->getMessage()
            ]);
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
            DB::beginTransaction();

            Log::info('Iniciando desasignación de instructor', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'instructor_id' => $instructorId,
                'timestamp' => now()
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);

            // Verificar que no sea el instructor principal
            if ($ficha->instructor_id == $instructorId) {
                return back()->withErrors([
                    'error' => 'No se puede desasignar al instructor principal. Primero debe asignar otro instructor como principal.'
                ]);
            }

            // Eliminar la asignación
            $asignacion = $ficha->instructorFicha()
                ->where('instructor_id', $instructorId)
                ->first();

            if ($asignacion) {
                $asignacion->delete();

                Log::info('Instructor desasignado exitosamente', [
                    'ficha_id' => $id,
                    'instructor_id' => $instructorId,
                    'user_id' => Auth::id()
                ]);

                DB::commit();
                return back()->with('success', 'Instructor desasignado exitosamente.');
            } else {
                DB::rollBack();
                return back()->withErrors([
                    'error' => 'No se encontró la asignación del instructor.'
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al desasignar instructor', [
                'ficha_id' => $id,
                'instructor_id' => $instructorId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return back()->withErrors([
                'error' => 'Error al desasignar instructor: ' . $e->getMessage()
            ]);
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
        try {
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

        } catch (\Exception $e) {
            Log::error('Error al cargar gestión de días de formación', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return redirect()->route('fichaCaracterizacion.index')
                ->with('error', 'Error al cargar la gestión de días de formación: ' . $e->getMessage());
        }
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
                'dias_permitidos' => [12, 13, 14, 15, 16], // LUNES a VIERNES
                'horario_tipico' => ['08:00', '12:00']
            ],
            2 => [ // TARDE
                'nombre' => 'TARDE',
                'dias_permitidos' => [12, 13, 14, 15, 16], // LUNES a VIERNES
                'horario_tipico' => ['14:00', '18:00']
            ],
            3 => [ // NOCHE
                'nombre' => 'NOCHE',
                'dias_permitidos' => [12, 13, 14, 15, 16], // LUNES a VIERNES
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
            $horaInicio = \Carbon\Carbon::createFromFormat('H:i', $dia->hora_inicio);
            $horaFin = \Carbon\Carbon::createFromFormat('H:i', $dia->hora_fin);
            $horasPorDia = $horaInicio->diffInHours($horaFin);
            
            $horasTotales += $horasPorDia * $duracionEnDias;
        }

        return $horasTotales;
    }
}

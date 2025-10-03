<?php

namespace App\Http\Controllers;

use App\Models\FichaCaracterizacion;
use App\Models\ProgramaFormacion;
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
     * Este método recupera todas las fichas de caracterización junto con su
     * relación 'programaFormacion' y las pasa a la vista 'fichas.index'.
     *
     * @return \Illuminate\View\View La vista que muestra la lista de fichas de caracterización.
     */
    public function index()
    {
        try {
            Log::info('Acceso al índice de fichas de caracterización', [
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            $fichas = FichaCaracterizacion::with('programaFormacion')
                ->orderBy('id', 'desc')
                ->paginate(10);

            Log::info('Fichas de caracterización cargadas exitosamente', [
                'total_fichas' => $fichas->total(),
                'user_id' => Auth::id()
            ]);

            return view('fichas.index', compact('fichas'));

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

    public function search(Request $request)
    {
        try {
            Log::info('Búsqueda de fichas de caracterización', [
                'query' => $request->input('search'),
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            $request->validate([
                'search' => 'required|string|max:255',
            ], [
                'search.required' => 'El término de búsqueda es obligatorio.',
                'search.string' => 'El término de búsqueda debe ser texto.',
                'search.max' => 'El término de búsqueda no puede exceder 255 caracteres.',
            ]);

            $query = $request->input('search');
            $fichas = FichaCaracterizacion::with('programaFormacion')
                ->where('ficha', 'LIKE', "%{$query}%")
                ->orderBy('id', 'desc')
                ->paginate(7);

            Log::info('Búsqueda de fichas completada', [
                'query' => $query,
                'resultados_encontrados' => $fichas->count(),
                'total_resultados' => $fichas->total(),
                'user_id' => Auth::id()
            ]);

            if ($fichas->count() == 0) {
                return back()->with('error', 'No se encontraron fichas que coincidan con la búsqueda: "' . $query . '".');
            }

            return view('fichas.index', compact('fichas'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación en búsqueda de fichas', [
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            Log::error('Error en búsqueda de fichas de caracterización', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'query' => $request->input('search'),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

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
                'instructor',
                'jornadaFormacion',
                'ambiente',
                'modalidadFormacion',
                'sede',
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
}

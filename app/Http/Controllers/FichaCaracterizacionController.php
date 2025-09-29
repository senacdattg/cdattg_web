<?php

namespace App\Http\Controllers;

use App\Models\FichaCaracterizacion;
use App\Models\ProgramaFormacion;
use Illuminate\Http\Request;

class FichaCaracterizacionController extends Controller
{

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

        $fichas = FichaCaracterizacion::with('programaFormacion')->orderBy('id', 'desc')->paginate(10);
        return view('fichas.index', compact('fichas'));
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

        $programas = ProgramaFormacion::orderBy('nombre', 'asc')->get();

        return view('fichas.create', compact('programas'));
    }


    /**
     * Almacena una nueva ficha de caracterización en la base de datos.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos de la ficha.
     *
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta 'fichaCaracterizacion.index' con un mensaje de éxito.
     *
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     */
    public function store(Request $request)
    {
        $request->validate([
            'programa_id' => 'required|exists:programas_formacion,id',
            'numero_ficha' => 'required|numeric|unique:fichas_caracterizacion,ficha|min:1',
        ]);


        $ficha = new FichaCaracterizacion();
        $ficha->programa_formacion_id = $request->input('programa_id');
        $ficha->ficha = $request->input('numero_ficha');

        if ($ficha->save()) {
            return redirect()->route('fichaCaracterizacion.index')->with('success', 'Caracterización creada exitosamente.');
        }

        return back()->with('error', 'Ocurrió un error al crear la caracterización.');
    }


    /**
     * Muestra el formulario de edición para una ficha de caracterización específica.
     *
     * @param string $id El ID de la ficha de caracterización a editar.
     * @return \Illuminate\View\View La vista del formulario de edición con la ficha de caracterización y los programas de formación.
     */
    public function edit(string $id)
    {
        $ficha = FichaCaracterizacion::findOrFail($id);
        $programas = ProgramaFormacion::orderBy('nombre', 'asc')->get();

        return view('fichas.edit', compact('ficha', 'programas'));
    }


    /**
     * Actualiza una ficha de caracterización existente.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos de la ficha a actualizar.
     * @param string $id El ID de la ficha de caracterización que se va a actualizar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la lista de fichas de caracterización con un mensaje de éxito.
     *
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si no se encuentra la ficha de caracterización con el ID proporcionado.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'programa_id' => 'required|exists:programas_formacion,id',
            'numero_ficha' => 'required|numeric|unique:fichas_caracterizacion,ficha,' . $id,
        ]);

        $ficha = FichaCaracterizacion::findOrFail($id);
        $ficha->programa_formacion_id = $request->input('programa_id');
        $ficha->ficha = $request->input('numero_ficha');

        $ficha->save();

        return redirect()->route('fichaCaracterizacion.index')->with('success', 'Caracterización actualizada exitosamente.');
    }


    /**
     * Elimina una ficha de caracterización específica.
     *
     * @param string $id El ID de la ficha de caracterización a eliminar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la lista de fichas de caracterización con un mensaje de éxito.
     */
    public function destroy(string $id)
    {
        $ficha = FichaCaracterizacion::findOrFail($id);
        $ficha->delete();

        return redirect()->route('fichaCaracterizacion.index')->with('success', 'Ficha eliminada exitosamente.');
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|max:255',
        ]);

        $query = $request->input('search');
        $fichas = FichaCaracterizacion::with('programaFormacion')->where('ficha', 'LIKE', "{$query}%")
            ->orWhere('ficha', 'LIKE', "%{$query}")
            ->orWhere('ficha', 'LIKE', "%{$query}%")
            ->orderBy('id', 'desc')
            ->paginate(7);

        if (count($fichas) == 0) {
            return back()->with('error', 'No se encontraron resultados.');
        }

        return view('fichas.index', compact('fichas'));
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
}

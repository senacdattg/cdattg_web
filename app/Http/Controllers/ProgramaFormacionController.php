<?php

namespace App\Http\Controllers;

use App\Services\ProgramaFormacionService;
use App\Models\ProgramaFormacion;
use App\Models\RedConocimiento;
use App\Models\Parametro;
use App\Http\Requests\StoreProgramaFormacionRequest;
use App\Http\Requests\UpdateProgramaFormacionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProgramaFormacionController extends Controller
{
    protected ProgramaFormacionService $programaService;

    public function __construct(ProgramaFormacionService $programaService)
    {
        $this->middleware('auth');
        $this->programaService = $programaService;
        
        $this->middleware('permission:VER PROGRAMAS DE FORMACION')->only('index');
        $this->middleware('permission:VER PROGRAMA DE FORMACION')->only('show');
        $this->middleware('permission:CREAR PROGRAMA DE FORMACION')->only('create', 'store');
        $this->middleware('permission:EDITAR PROGRAMA DE FORMACION')->only('edit', 'update');
        $this->middleware('permission:ELIMINAR PROGRAMA DE FORMACION')->only('destroy');
        $this->middleware('permission:VER PROGRAMAS DE FORMACION')->only('search');
        $this->middleware('permission:CAMBIAR ESTADO PROGRAMA DE FORMACION')->only('cambiarEstado');
    }

    /**
     * Muestra una lista paginada de programas de formación.
     *
     * Este método recupera una lista de programas de formación desde la base de datos,
     * incluyendo las relaciones con 'redConocimiento' y 'nivelFormacion', y los pagina en grupos de 6.
     * Luego, pasa esta lista a la vista 'programas.index'.
     *
     * @return \Illuminate\View\View La vista que muestra la lista de programas de formación.
     */
    public function index()
    {
        $programas = $this->programaService->listar(15);

        return view('programas.index', compact('programas'));
    }

    /**
     * Muestra los detalles de un programa de formación específico.
     *
     * @param string $id El ID del programa de formación a mostrar.
     * @return \Illuminate\View\View La vista que muestra los detalles del programa.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *         Si no se encuentra el programa de formación con el ID proporcionado.
     */
    public function show(string $id)
    {
        $programa = ProgramaFormacion::with([
                'redConocimiento',
                'nivelFormacion',
                'userCreated',
                'userEdited',
                'competencias' => function ($query) {
                    $query->orderBy('nombre');
                }
            ])
            ->findOrFail($id);

        return view('programas.show', compact('programa'));
    }


    /**
     * Muestra el formulario para crear un nuevo programa de formación.
     *
     * Este método obtiene todas las sedes y tipos de programas disponibles.
     * Si no hay sedes o tipos de programas, se asigna null a las variables correspondientes.
     *
     * @return \Illuminate\View\View La vista del formulario de creación de programas de formación.
     */
    public function create()
    {
        $redesConocimiento = RedConocimiento::all();
        $nivelesFormacion = Parametro::whereHas('temas', function ($query) {
            $query->where('temas.id', 6);
        })->get(); // Tema 6 corresponde a NIVELES DE FORMACION

        return view('programas.create', compact('redesConocimiento', 'nivelesFormacion'));
    }


    /**
     * Almacena un nuevo programa de formación en la base de datos.
     *
     * Valida los datos de entrada del formulario y crea un nuevo registro
     * en la tabla 'programas_formacion' con los datos proporcionados.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos del formulario.
     * @return \Illuminate\Http\RedirectResponse Redirige a la página de índice de programas con un mensaje de éxito.
     */
    public function store(StoreProgramaFormacionRequest $request)
    {
        try {
            $datos = [
                'codigo' => $request->input('codigo'),
                'nombre' => $request->input('nombre'),
                'red_conocimiento_id' => $request->input('red_conocimiento_id'),
                'nivel_formacion_id' => $request->input('nivel_formacion_id'),
                'horas_totales' => (int) $request->input('horas_totales'),
                'horas_etapa_lectiva' => (int) $request->input('horas_etapa_lectiva'),
                'horas_etapa_productiva' => (int) $request->input('horas_etapa_productiva'),
                'user_create_id' => Auth::id(),
                'user_edit_id' => Auth::id(),
                'status' => true,
            ];

            $this->programaService->crear($datos);

            return redirect()->route('programa.index')->with('success', 'Programa creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear programa: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al crear programa.');
        }
    }


    /**
     * Muestra el formulario de edición para un programa de formación específico.
     *
     * @param string $id El ID del programa de formación a editar.
     * @return \Illuminate\View\View La vista del formulario de edición con los datos del programa, sedes y tipos de programa.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *         Si no se encuentra el programa de formación con el ID proporcionado.
     */
    public function edit(string $id)
    {
        $programa = ProgramaFormacion::with([
            'competencias' => function ($query) {
                $query->withCount('programasFormacion')
                    ->orderBy('nombre');
            }
        ])->findOrFail($id);

        $redesConocimiento = RedConocimiento::all();
        $nivelesFormacion = Parametro::whereHas('temas', function ($query) {
            $query->where('temas.id', 6);
        })->get(); // Tema 6 corresponde a NIVELES DE FORMACION

        return view('programas.edit', compact('programa', 'redesConocimiento', 'nivelesFormacion'));
    }


    /**
     * Actualiza un programa de formación existente.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos del formulario.
     * @param string $id El ID del programa de formación que se va a actualizar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la página de índice de programas con un mensaje de éxito.
     *
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *         Si no se encuentra el programa de formación con el ID proporcionado.
     */
    public function update(UpdateProgramaFormacionRequest $request, string $id)
    {
        try {
            $programaFormacion = ProgramaFormacion::with('competencias')->findOrFail($id);
            
            // Validaciones de negocio adicionales para actualización
            $validationErrors = $this->validateBusinessRules($request, $programaFormacion);
            if (!empty($validationErrors)) {
                return redirect()->back()->withInput()->withErrors($validationErrors);
            }

            $datosActualizados = [
                'codigo' => $request->input('codigo'),
                'nombre' => $request->input('nombre'),
                'red_conocimiento_id' => $request->input('red_conocimiento_id'),
                'nivel_formacion_id' => $request->input('nivel_formacion_id'),
                'horas_totales' => (int) $request->input('horas_totales'),
                'horas_etapa_lectiva' => (int) $request->input('horas_etapa_lectiva'),
                'horas_etapa_productiva' => (int) $request->input('horas_etapa_productiva'),
                'status' => $request->input('status', $programaFormacion->status),
                'user_edit_id' => Auth::id(),
            ];

            if ($this->programaService->actualizar($programaFormacion, $datosActualizados)) {
                Log::info('Programa de formación actualizado exitosamente', [
                    'programa_id' => $programaFormacion->id,
                    'codigo' => $programaFormacion->codigo,
                    'nombre' => $programaFormacion->nombre,
                    'usuario_id' => Auth::id()
                ]);
                return redirect()->route('programa.index')->with('success', 'Programa de formación actualizado exitosamente.');
            } else {
                return redirect()->back()->with('error', 'Error al actualizar el programa de formación.');
            }
        } catch (\Exception $e) {
            Log::error('Error al actualizar programa de formación', [
                'programa_id' => $id,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Error interno al actualizar el programa de formación.');
        }
    }


    /**
     * Elimina un programa de formación especificado por su ID.
     *
     * @param string $id El ID del programa de formación a eliminar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la página de índice de programas con un mensaje de éxito.
     */
    public function destroy(string $id)
    {
        try {
            $programaFormacion = ProgramaFormacion::findOrFail($id);
            $nombrePrograma = $programaFormacion->nombre;
            
            // Validar que no tenga fichas activas
            $validationErrors = $this->validateDeletionRules($programaFormacion);
            if (!empty($validationErrors)) {
                return redirect()->back()->withErrors($validationErrors);
            }
            
            $programaFormacion->competencias()->detach();
            
            if ($programaFormacion->delete()) {
                Log::info('Programa de formación eliminado exitosamente', [
                    'programa_id' => $id,
                    'nombre' => $nombrePrograma,
                    'usuario_id' => Auth::id()
                ]);
                return redirect()->route('programa.index')->with('success', 'Programa de formación eliminado exitosamente.');
            } else {
                return redirect()->back()->with('error', 'Error al eliminar el programa de formación.');
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar programa de formación', [
                'programa_id' => $id,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Error interno al eliminar el programa de formación.');
        }
    }


    /**
     * Busca programas de formación basados en el término de búsqueda proporcionado.
     *
     * Este método toma una solicitud HTTP que contiene un término de búsqueda y busca
     * programas de formación cuyo nombre coincida con el término de búsqueda. También
     * busca programas de formación que estén asociados con sedes o tipos de programas
     * cuyo nombre coincida con el término de búsqueda.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene el término de búsqueda.
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse La vista que muestra los programas de formación encontrados o respuesta JSON/redirección.
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('search');
            $redConocimientoId = $request->input('red_conocimiento_id');
            $nivelFormacionId = $request->input('nivel_formacion_id');
            $status = $request->input('status');
            $perPage = $request->input('per_page', 6);
            
            // Construir consulta base
            $programasQuery = ProgramaFormacion::with(['redConocimiento', 'nivelFormacion', 'userCreated', 'userEdited']);

            // Aplicar filtros
            if (!empty($query)) {
                $programasQuery->where(function ($q) use ($query) {
                    $q->where('codigo', 'LIKE', "%{$query}%")
                      ->orWhere('nombre', 'LIKE', "%{$query}%")
                      ->orWhereHas('redConocimiento', function ($subQuery) use ($query) {
                          $subQuery->where('nombre', 'LIKE', "%{$query}%");
                      })
                      ->orWhereHas('nivelFormacion', function ($subQuery) use ($query) {
                          $subQuery->where('name', 'LIKE', "%{$query}%");
                      });
                });
            }

            if (!empty($redConocimientoId)) {
                $programasQuery->where('red_conocimiento_id', $redConocimientoId);
            }

            if (!empty($nivelFormacionId)) {
                $programasQuery->where('nivel_formacion_id', $nivelFormacionId);
            }

            if ($status !== null && $status !== '') {
                $programasQuery->where('status', $status);
            }

            // Ordenar y paginar
            $programas = $programasQuery->orderBy('nombre', 'asc')->paginate($perPage);

            // Si es una petición AJAX, devolver JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'programas' => $programas->items(),
                        'pagination' => [
                            'current_page' => $programas->currentPage(),
                            'last_page' => $programas->lastPage(),
                            'per_page' => $programas->perPage(),
                            'total' => $programas->total(),
                            'has_more_pages' => $programas->hasMorePages(),
                        ],
                        'filters' => [
                            'search' => $query,
                            'red_conocimiento_id' => $redConocimientoId,
                            'nivel_formacion_id' => $nivelFormacionId,
                            'status' => $status,
                        ]
                    ]
                ]);
            }

            // Si no hay resultados y hay búsqueda, mostrar mensaje
            if ($programas->count() == 0 && !empty($query)) {
                Log::info('Búsqueda de programas sin resultados', [
                    'query' => $query,
                    'filters' => $request->all(),
                    'usuario_id' => Auth::id()
                ]);
                return redirect()->route('programa.index')->with('error', 'No se encontraron programas de formación con los criterios especificados.');
            }

            Log::info('Búsqueda de programas realizada', [
                'query' => $query,
                'filters' => $request->all(),
                'resultados' => $programas->count(),
                'usuario_id' => Auth::id()
            ]);

            return view('programas.index', compact('programas'));
        } catch (\Exception $e) {
            Log::error('Error en búsqueda de programas', [
                'filters' => $request->all(),
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno en la búsqueda.'
                ], 500);
            }
            
            return redirect()->route('programa.index')->with('error', 'Error interno en la búsqueda.');
        }
    }

    /**
     * Cambiar el estado de un programa de formación.
     *
     * @param string $id El ID del programa de formación.
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de resultado.
     */
    public function cambiarEstado(string $id)
    {
        try {
            $programa = ProgramaFormacion::findOrFail($id);
            $programa->status = !$programa->status;
            
            if ($programa->save()) {
                Log::info('Estado del programa cambiado', [
                    'programa_id' => $id,
                    'nuevo_estado' => $programa->status ? 'activo' : 'inactivo',
                    'usuario_id' => Auth::id()
                ]);
                
                return redirect()->back()->with('success', 'Estado del programa actualizado exitosamente.');
            } else {
                return redirect()->back()->with('error', 'Error al cambiar el estado del programa.');
            }
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del programa', [
                'programa_id' => $id,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);
            
            return redirect()->back()->with('error', 'Error interno al cambiar el estado del programa.');
        }
    }

    /**
     * Obtener programas por red de conocimiento.
     *
     * @param string $redConocimientoId ID de la red de conocimiento.
     * @return \Illuminate\Http\JsonResponse Lista de programas.
     */
    public function getByRedConocimiento(string $redConocimientoId)
    {
        try {
            $programas = ProgramaFormacion::where('red_conocimiento_id', $redConocimientoId)
                ->where('status', true)
                ->orderBy('nombre')
                ->get([
                    'id',
                    'codigo',
                    'nombre',
                    'horas_totales',
                    'horas_etapa_lectiva',
                    'horas_etapa_productiva',
                ]);

            return response()->json([
                'success' => true,
                'data' => $programas
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener programas por red de conocimiento', [
                'red_conocimiento_id' => $redConocimientoId,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los programas.'
            ], 500);
        }
    }

    /**
     * Obtener programas por nivel de formación.
     *
     * @param string $nivelFormacionId ID del nivel de formación.
     * @return \Illuminate\Http\JsonResponse Lista de programas.
     */
    public function getByNivelFormacion(string $nivelFormacionId)
    {
        try {
            $programas = ProgramaFormacion::where('nivel_formacion_id', $nivelFormacionId)
                ->where('status', true)
                ->orderBy('nombre')
                ->get([
                    'id',
                    'codigo',
                    'nombre',
                    'horas_totales',
                    'horas_etapa_lectiva',
                    'horas_etapa_productiva',
                ]);

            return response()->json([
                'success' => true,
                'data' => $programas
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener programas por nivel de formación', [
                'nivel_formacion_id' => $nivelFormacionId,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los programas.'
            ], 500);
        }
    }

    /**
     * Obtener todos los programas activos.
     *
     * @return \Illuminate\Http\JsonResponse Lista de programas activos.
     */
    public function getActivos()
    {
        try {
            $programas = ProgramaFormacion::where('status', true)
                ->with(['redConocimiento', 'nivelFormacion'])
                ->orderBy('nombre')
                ->get([
                    'id',
                    'codigo',
                    'nombre',
                    'red_conocimiento_id',
                    'nivel_formacion_id',
                    'horas_totales',
                    'horas_etapa_lectiva',
                    'horas_etapa_productiva',
                ]);

            return response()->json([
                'success' => true,
                'data' => $programas
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener programas activos', [
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los programas activos.'
            ], 500);
        }
    }

    /**
     * Validar reglas de negocio para creación y actualización de programas.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ProgramaFormacion|null $programa
     * @return array
     */
    private function validateBusinessRules($request, $programa = null)
    {
        $errors = [];

        // 1. Validar código único por regional
        $codigo = $request->input('codigo');
        $redConocimientoId = $request->input('red_conocimiento_id');
        
        if ($codigo && $redConocimientoId) {
            $query = ProgramaFormacion::where('codigo', $codigo)
                ->whereHas('redConocimiento', function ($q) use ($redConocimientoId) {
                    $q->where('id', $redConocimientoId);
                });

            // Excluir el programa actual en caso de actualización
            if ($programa) {
                $query->where('id', '!=', $programa->id);
            }

            if ($query->exists()) {
                $redConocimiento = RedConocimiento::find($redConocimientoId);
                $errors['codigo'] = "El código '{$codigo}' ya existe para la red de conocimiento '{$redConocimiento->nombre}'.";
            }
        }

        // 2. Validar formato del código SENA
        if ($codigo && !$this->validateSenaCodeFormat($codigo)) {
            $errors['codigo'] = 'El código debe tener el formato válido del SENA (6 dígitos numéricos).';
        }

        // 3. Validar nombre único por regional
        $nombre = $request->input('nombre');
        if ($nombre && $redConocimientoId) {
            $query = ProgramaFormacion::where('nombre', $nombre)
                ->whereHas('redConocimiento', function ($q) use ($redConocimientoId) {
                    $q->where('id', $redConocimientoId);
                });

            if ($programa) {
                $query->where('id', '!=', $programa->id);
            }

            if ($query->exists()) {
                $redConocimiento = RedConocimiento::find($redConocimientoId);
                $errors['nombre'] = "El nombre '{$nombre}' ya existe para la red de conocimiento '{$redConocimiento->nombre}'.";
            }
        }

        // 4. Validar compatibilidad entre red de conocimiento y nivel de formación
        $nivelFormacionId = $request->input('nivel_formacion_id');
        if ($redConocimientoId && $nivelFormacionId) {
            if (!$this->validateRedNivelCompatibility($redConocimientoId, $nivelFormacionId)) {
                $errors['nivel_formacion_id'] = 'El nivel de formación seleccionado no es compatible con la red de conocimiento.';
            }
        }

        // 5. Validar reglas específicas del SENA
        if ($nombre) {
            $senaValidation = $this->validateSenaProgramRules($nombre, $nivelFormacionId);
            if (!empty($senaValidation)) {
                $errors = array_merge($errors, $senaValidation);
            }
        }

        // 6. Validar que las horas por etapa no excedan el total
        $horasTotales = (int) $request->input('horas_totales');
        $horasLectiva = (int) $request->input('horas_etapa_lectiva');
        $horasProductiva = (int) $request->input('horas_etapa_productiva');

        if ($horasTotales > 0 && ($horasLectiva + $horasProductiva) !== $horasTotales) {
            $errors['horas_totales'] = 'La suma de las horas lectivas y productivas '
                . 'debe ser igual al total de horas del programa.';
        }

        return $errors;
    }

    /**
     * Validar reglas de eliminación de programas.
     *
     * @param \App\Models\ProgramaFormacion $programa
     * @return array
     */
    private function validateDeletionRules($programa)
    {
        $errors = [];

        // 1. Validar que no tenga fichas activas
        $fichasActivas = $programa->fichasCaracterizacion()
            ->where('status', true)
            ->count();

        if ($fichasActivas > 0) {
            $errors['programa'] = "No se puede eliminar el programa '{$programa->nombre}' porque tiene {$fichasActivas} ficha(s) de caracterización activa(s).";
        }

        // 2. Validar que no tenga aprendices asociados
        $aprendicesCount = $programa->fichasCaracterizacion()
            ->join('aprendices', 'fichas_caracterizacion.id', '=', 'aprendices.ficha_caracterizacion_id')
            ->count();

        if ($aprendicesCount > 0) {
            $errors['programa'] = "No se puede eliminar el programa '{$programa->nombre}' porque tiene {$aprendicesCount} aprendiz(es) asociado(s).";
        }

        return $errors;
    }

    /**
     * Validar formato del código SENA.
     *
     * @param string $codigo
     * @return bool
     */
    private function validateSenaCodeFormat($codigo)
    {
        // Código SENA: 6 dígitos numéricos
        return preg_match('/^\d{6}$/', $codigo);
    }

    /**
     * Validar compatibilidad entre red de conocimiento y nivel de formación.
     *
     * @param int $redConocimientoId
     * @param int $nivelFormacionId
     * @return bool
     */
    private function validateRedNivelCompatibility($redConocimientoId, $nivelFormacionId)
    {
        // Obtener la red de conocimiento
        $redConocimiento = RedConocimiento::find($redConocimientoId);
        $nivelFormacion = Parametro::find($nivelFormacionId);

        if (!$redConocimiento || !$nivelFormacion) {
            return false;
        }

        // Reglas de compatibilidad específicas del SENA
        $compatibilidades = [
            'INFORMÁTICA, DISEÑO Y DESARROLLO DE SOFTWARE' => ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR'],
            'COMERCIO Y VENTAS' => ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO'],
            'GESTIÓN ADMINISTRATIVA Y FINANCIERA' => ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR'],
            'HOTELERÍA Y TURISMO' => ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR'],
            'CONSTRUCCIÓN' => ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO'],
            'MECÁNICA INDUSTRIAL' => ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO'],
        ];

        $nivelesPermitidos = $compatibilidades[$redConocimiento->nombre] ?? ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO'];
        
        return in_array($nivelFormacion->name, $nivelesPermitidos);
    }

    /**
     * Validar reglas específicas del SENA para programas.
     *
     * @param string $nombre
     * @param int $nivelFormacionId
     * @return array
     */
    private function validateSenaProgramRules($nombre, $nivelFormacionId)
    {
        $errors = [];
        $nivelFormacion = Parametro::find($nivelFormacionId);

        if (!$nivelFormacion) {
            return $errors;
        }

        // 1. Validar longitud mínima del nombre
        if (strlen($nombre) < 10) {
            $errors['nombre'] = 'El nombre del programa debe tener al menos 10 caracteres.';
        }

        // 2. Validar que no contenga caracteres especiales no permitidos
        if (preg_match('/[<>{}[\]\\|`~!@#$%^&*()+=]/', $nombre)) {
            $errors['nombre'] = 'El nombre del programa no puede contener caracteres especiales.';
        }

        // 4. Validar formato específico por nivel - REMOVIDO
        // Ya no se requiere que el nombre inicie con el nivel de formación

        return $errors;
    }

    /**
     * Desasocia una competencia específica de un programa.
     */
    public function detachCompetencia(string $programaId, string $competenciaId)
    {
        try {
            $programa = ProgramaFormacion::findOrFail($programaId);

            if (!$programa->competencias()->where('competencias.id', $competenciaId)->exists()) {
                return redirect()->back()->with('warning', 'La competencia seleccionada no está asociada al programa.');
            }

            $programa->competencias()->detach($competenciaId);

            Log::info('Competencia desasociada de programa', [
                'programa_id' => $programaId,
                'competencia_id' => $competenciaId,
                'usuario_id' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'La competencia fue desasociada del programa.');
        } catch (\Exception $e) {
            Log::error('Error al desasociar competencia de programa', [
                'programa_id' => $programaId,
                'competencia_id' => $competenciaId,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'No fue posible desasociar la competencia.');
        }
    }
}

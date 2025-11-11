<?php

namespace App\Http\Controllers;

use App\Services\CompetenciaService;
use App\Repositories\CompetenciaRepository;
use App\Repositories\ResultadosAprendizajeRepository;
use App\Http\Requests\StoreCompetenciaRequest;
use App\Http\Requests\UpdateCompetenciaRequest;
use App\Models\Competencia;
use App\Models\ResultadosAprendizaje;
use App\Models\ProgramaFormacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CompetenciaController extends Controller
{
    protected CompetenciaService $competenciaService;
    protected CompetenciaRepository $competenciaRepo;
    protected ResultadosAprendizajeRepository $resultadosRepo;

    public function __construct(
        CompetenciaService $competenciaService,
        CompetenciaRepository $competenciaRepo,
        ResultadosAprendizajeRepository $resultadosRepo
    ) {
        $this->middleware('auth');
        $this->competenciaService = $competenciaService;
        $this->competenciaRepo = $competenciaRepo;
        $this->resultadosRepo = $resultadosRepo;

        $this->middleware('can:VER COMPETENCIA')->only(['index', 'show']);
        $this->middleware('can:CREAR COMPETENCIA')->only(['create', 'store']);
        $this->middleware('can:EDITAR COMPETENCIA')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR COMPETENCIA')->only('destroy');
        $this->middleware('can:CAMBIAR ESTADO COMPETENCIA')->only('cambiarEstado');
        $this->middleware('can:GESTIONAR RESULTADOS COMPETENCIA')->only(['gestionarResultados', 'asociarResultado', 'asociarResultados', 'desasociarResultado']);
    }

    public function index(Request $request)
    {
        try {
            $query = Competencia::with(['userCreate', 'userEdit', 'programasFormacion']);
            
            // Filtro de búsqueda por código o nombre
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('codigo', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('nombre', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('descripcion', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            // Filtro por estado
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            // Filtro por rango de fechas
            if ($request->filled('fecha_inicio')) {
                $query->whereDate('fecha_inicio', '>=', $request->fecha_inicio);
            }
            
            if ($request->filled('fecha_fin')) {
                $query->whereDate('fecha_fin', '<=', $request->fecha_fin);
            }
            
            // Filtro por duración
            if ($request->filled('duracion_min')) {
                $query->where('duracion', '>=', $request->duracion_min);
            }
            
            if ($request->filled('duracion_max')) {
                $query->where('duracion', '<=', $request->duracion_max);
            }
            
            $query->orderBy('codigo', 'asc');
            
            $competencias = $query->paginate(10)->withQueryString();
            
            return view('competencias.index', compact('competencias'));
            
        } catch (Exception $e) {
            Log::error('Error al obtener lista de competencias: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar las competencias.');
        }
    }

    public function create()
    {
        try {
            $programas = ProgramaFormacion::orderBy('nombre')->get(['id', 'codigo', 'nombre']);

            return view('competencias.create', compact('programas'));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de creación de competencia: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el formulario de creación.');
        }
    }

    public function store(StoreCompetenciaRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            $programasSeleccionados = $request->input('programas', []);

            $data['user_create_id'] = Auth::id();
            $data['user_edit_id'] = Auth::id();
            $data['fecha_inicio'] = now();
            $data['fecha_fin'] = now()->addYear();
            $data['status'] = 1;

            $competencia = Competencia::create($data);

            if (!empty($programasSeleccionados)) {
                $competencia->programasFormacion()->attach(
                    collect($programasSeleccionados)->mapWithKeys(function ($programaId) {
                        return [
                            $programaId => [
                                'user_create_id' => Auth::id(),
                                'user_edit_id' => Auth::id(),
                            ],
                        ];
                    })->toArray()
                );
            }
            
            DB::commit();
            
            Log::info('Competencia creada exitosamente', [
                'competencia_id' => $competencia->id,
                'codigo' => $competencia->codigo,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('competencias.index')
                ->with('success', "Competencia '{$competencia->codigo}' creada exitosamente.");
                
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear competencia: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'data' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la competencia. Intente nuevamente.');
        }
    }

    public function show(Competencia $competencia)
    {
        try {
            $competencia->load(['resultadosCompetencia.rap', 'userCreate', 'userEdit']);
            
            // Contar resultados de aprendizaje asociados
            $cantidadRAPs = $competencia->resultadosCompetencia->count();
            
            return view('competencias.show', compact('competencia', 'cantidadRAPs'));
            
        } catch (Exception $e) {
            Log::error('Error al ver detalle de competencia: ' . $e->getMessage(), [
                'competencia_id' => $competencia->id
            ]);
            
            return redirect()->back()->with('error', 'Error al cargar la competencia.');
        }
    }

    public function edit(Competencia $competencia)
    {
        try {
            $competencia->load([
                'programasFormacion' => function ($query) {
                    $query->orderBy('nombre');
                },
                'resultadosAprendizaje' => function ($query) {
                    $query->orderBy('codigo');
                }
            ]);

            return view('competencias.edit', compact('competencia'));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de edición: ' . $e->getMessage(), [
                'competencia_id' => $competencia->id
            ]);
            
            return redirect()->back()->with('error', 'Error al cargar el formulario de edición.');
        }
    }

    public function update(UpdateCompetenciaRequest $request, Competencia $competencia)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            $data['user_edit_id'] = Auth::id();
            
            $competencia->update($data);
            
            DB::commit();
            
            Log::info('Competencia actualizada exitosamente', [
                'competencia_id' => $competencia->id,
                'codigo' => $competencia->codigo,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('competencias.index')
                ->with('success', "Competencia '{$competencia->codigo}' actualizada exitosamente.");
                
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar competencia: ' . $e->getMessage(), [
                'competencia_id' => $competencia->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la competencia. Intente nuevamente.');
        }
    }

    public function destroy(Competencia $competencia)
    {
        try {
            DB::beginTransaction();
            
            // Validación de negocio: No se puede eliminar competencia con RAPs asociados
            $cantidadRAPs = $competencia->resultadosCompetencia->count();
            if ($cantidadRAPs > 0) {
                Log::warning('Intento de eliminar competencia con RAPs asociados', [
                    'competencia_id' => $competencia->id,
                    'codigo' => $competencia->codigo,
                    'cantidad_raps' => $cantidadRAPs,
                    'user_id' => Auth::id()
                ]);
                
                return redirect()->back()
                    ->with('error', "No se puede eliminar la competencia '{$competencia->codigo}' porque tiene {$cantidadRAPs} resultado(s) de aprendizaje asociado(s). Primero debe desasociar o eliminar los RAPs relacionados.");
            }
            
            $competencia->delete();
            
            DB::commit();
            
            Log::info('Competencia eliminada exitosamente', [
                'competencia_id' => $competencia->id,
                'codigo' => $competencia->codigo,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('competencias.index')
                ->with('success', "Competencia '{$competencia->codigo}' eliminada exitosamente.");
                
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar competencia: ' . $e->getMessage(), [
                'competencia_id' => $competencia->id,
                'codigo' => $competencia->codigo ?? 'N/A',
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error al eliminar la competencia. Intente nuevamente.');
        }
    }

    public function search(Request $request)
    {
        try {
            $query = Competencia::with(['userCreate', 'userEdit', 'programasFormacion']);
            
            // Búsqueda general por código, nombre o descripción
            if ($request->filled('q')) {
                $searchTerm = $request->q;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('codigo', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('nombre', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('descripcion', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            // Filtro específico por código
            if ($request->filled('codigo')) {
                $query->where('codigo', 'LIKE', "%{$request->codigo}%");
            }
            
            // Filtro específico por nombre
            if ($request->filled('nombre')) {
                $query->where('nombre', 'LIKE', "%{$request->nombre}%");
            }
            
            // Filtro por programa de formación
            if ($request->filled('programa_id')) {
                $query->whereHas('programasFormacion', function($q) use ($request) {
                    $q->where('programas_formacion.id', $request->programa_id);
                });
            }
            
            // Filtro por estado
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            // Filtro por rango de fechas de inicio
            if ($request->filled('fecha_inicio_desde')) {
                $query->whereDate('fecha_inicio', '>=', $request->fecha_inicio_desde);
            }
            
            if ($request->filled('fecha_inicio_hasta')) {
                $query->whereDate('fecha_inicio', '<=', $request->fecha_inicio_hasta);
            }
            
            // Filtro por rango de fechas de fin
            if ($request->filled('fecha_fin_desde')) {
                $query->whereDate('fecha_fin', '>=', $request->fecha_fin_desde);
            }
            
            if ($request->filled('fecha_fin_hasta')) {
                $query->whereDate('fecha_fin', '<=', $request->fecha_fin_hasta);
            }
            
            // Filtro por duración
            if ($request->filled('duracion_min')) {
                $query->where('duracion', '>=', $request->duracion_min);
            }
            
            if ($request->filled('duracion_max')) {
                $query->where('duracion', '<=', $request->duracion_max);
            }
            
            // Solo competencias vigentes
            if ($request->filled('vigentes') && $request->vigentes == 1) {
                $query->vigentes();
            }
            
            // Orden
            $orderBy = $request->get('order_by', 'codigo');
            $orderDirection = $request->get('order_direction', 'asc');
            $query->orderBy($orderBy, $orderDirection);
            
            $perPage = $request->get('per_page', 10);
            $competencias = $query->paginate($perPage);
            
            // Formatear datos para respuesta JSON
            $data = $competencias->map(function($competencia) {
                return [
                    'id' => $competencia->id,
                    'codigo' => $competencia->codigo,
                    'nombre' => $competencia->nombre,
                    'descripcion' => $competencia->descripcion,
                    'duracion' => $competencia->duracion,
                    'fecha_inicio' => $competencia->fecha_inicio ? $competencia->fecha_inicio->format('d/m/Y') : null,
                    'fecha_fin' => $competencia->fecha_fin ? $competencia->fecha_fin->format('d/m/Y') : null,
                    'status' => $competencia->status,
                    'estado_texto' => $competencia->status ? 'Activa' : 'Inactiva',
                    'programas_count' => $competencia->programasFormacion->count(),
                    'resultados_count' => $competencia->resultadosAprendizaje()->count(),
                    'created_at' => $competencia->created_at->format('d/m/Y H:i'),
                    'user_create' => $competencia->userCreate ? $competencia->userCreate->name : 'N/A',
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'total' => $competencias->total(),
                    'per_page' => $competencias->perPage(),
                    'current_page' => $competencias->currentPage(),
                    'last_page' => $competencias->lastPage(),
                    'from' => $competencias->firstItem(),
                    'to' => $competencias->lastItem(),
                ],
                'filters' => $request->except(['page', 'per_page'])
            ]);
            
        } catch (Exception $e) {
            Log::error('Error en búsqueda de competencias: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al realizar la búsqueda',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cambiarEstado(Competencia $competencia)
    {
        try {
            $nuevoEstado = $competencia->status === 1 ? 0 : 1;
            $competencia->status = $nuevoEstado;
            $competencia->user_edit_id = Auth::id();
            $competencia->save();
            
            $estadoTexto = $nuevoEstado === 1 ? 'activa' : 'inactiva';
            
            Log::info('Estado de competencia cambiado', [
                'competencia_id' => $competencia->id,
                'codigo' => $competencia->codigo,
                'nuevo_estado' => $nuevoEstado,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('success', "Competencia '{$competencia->codigo}' marcada como {$estadoTexto}.");
                
        } catch (Exception $e) {
            Log::error('Error al cambiar estado de competencia: ' . $e->getMessage(), [
                'competencia_id' => $competencia->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error al cambiar el estado. Intente nuevamente.');
        }
    }

    public function gestionarResultados(Competencia $competencia)
    {
        try {
            // Cargar resultados asignados con sus relaciones
            $resultadosAsignados = $competencia->resultadosAprendizaje()
                ->with(['userCreate', 'userEdit'])
                ->orderBy('codigo')
                ->get();
            
            // Obtener IDs de resultados ya asignados
            $idsAsignados = $resultadosAsignados->pluck('id')->toArray();
            
            // Buscar resultados disponibles (no asignados y activos)
            $resultadosDisponibles = ResultadosAprendizaje::whereNotIn('id', $idsAsignados)
                ->where('status', 1)
                ->orderBy('codigo')
                ->get();
            
            // Estadísticas
            $totalAsignados = $resultadosAsignados->count();
            $totalDisponibles = $resultadosDisponibles->count();
            $duracionTotal = $resultadosAsignados->sum('duracion');
            
            return view('competencias.gestionar-resultados', compact(
                'competencia',
                'resultadosAsignados',
                'resultadosDisponibles',
                'totalAsignados',
                'totalDisponibles',
                'duracionTotal'
            ));
            
        } catch (Exception $e) {
            Log::error('Error al gestionar resultados de competencia: ' . $e->getMessage(), [
                'competencia_id' => $competencia->id
            ]);
            return redirect()->back()->with('error', 'Error al cargar la gestión de resultados.');
        }
    }

    public function asociarResultado(Request $request, Competencia $competencia)
    {
        try {
            $request->validate([
                'resultado_id' => 'required|exists:resultados_aprendizajes,id',
            ]);
            
            DB::beginTransaction();
            
            $resultadoId = $request->resultado_id;
            
            // Validar que el resultado exista y esté activo
            $resultado = ResultadosAprendizaje::findOrFail($resultadoId);
            
            if (!$resultado->status) {
                return redirect()->back()->with('error', 'No se puede asociar un Resultado de Aprendizaje inactivo.');
            }
            
            // Validar que la competencia esté activa
            if (!$competencia->status) {
                return redirect()->back()->with('error', 'No se pueden asociar resultados a una competencia inactiva.');
            }
            
            // Validar que no esté ya asociado
            if ($competencia->resultadosAprendizaje()->where('resultados_aprendizajes.id', $resultadoId)->exists()) {
                return redirect()->back()->with('error', 'Este resultado de aprendizaje ya está asignado a la competencia.');
            }
            
            // Asociar el resultado
            $competencia->resultadosAprendizaje()->attach($resultadoId, [
                'user_create_id' => Auth::id(),
                'user_edit_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::commit();
            
            Log::info('Resultado de aprendizaje asociado a competencia', [
                'competencia_id' => $competencia->id,
                'resultado_id' => $resultadoId,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()->with('success', "Resultado de aprendizaje '{$resultado->codigo}' asociado exitosamente.");
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al asociar resultado: ' . $e->getMessage(), [
                'competencia_id' => $competencia->id,
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Error al asociar el resultado de aprendizaje.');
        }
    }

    public function asociarResultados(Request $request, Competencia $competencia)
    {
        try {
            $request->validate([
                'resultado_ids' => 'required|array|min:1',
                'resultado_ids.*' => 'exists:resultados_aprendizajes,id',
            ]);
            
            DB::beginTransaction();
            
            $resultadoIds = $request->resultado_ids;
            $asociadosExitosamente = [];
            $errores = [];
            
            // Validar que la competencia esté activa
            if (!$competencia->status) {
                return redirect()->back()->with('error', 'No se pueden asociar resultados a una competencia inactiva.');
            }
            
            foreach ($resultadoIds as $resultadoId) {
                try {
                    // Validar que el resultado exista y esté activo
                    $resultado = ResultadosAprendizaje::findOrFail($resultadoId);
                    
                    if (!$resultado->status) {
                        $errores[] = "El resultado '{$resultado->codigo}' está inactivo y no se puede asociar.";
                        continue;
                    }
                    
                    // Validar que no esté ya asociado
                    if ($competencia->resultadosAprendizaje()->where('resultados_aprendizajes.id', $resultadoId)->exists()) {
                        $errores[] = "El resultado '{$resultado->codigo}' ya está asignado a la competencia.";
                        continue;
                    }
                    
                    // Asociar el resultado
                    $competencia->resultadosAprendizaje()->attach($resultadoId, [
                        'user_create_id' => Auth::id(),
                        'user_edit_id' => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    $asociadosExitosamente[] = $resultado->codigo;
                    
                    Log::info('Resultado de aprendizaje asociado a competencia (múltiple)', [
                        'competencia_id' => $competencia->id,
                        'resultado_id' => $resultadoId,
                        'resultado_codigo' => $resultado->codigo,
                        'user_id' => Auth::id()
                    ]);
                    
                } catch (Exception $e) {
                    $errores[] = "Error al asociar resultado ID {$resultadoId}: " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            // Construir mensaje de respuesta
            $mensaje = '';
            if (!empty($asociadosExitosamente)) {
                $mensaje .= "Resultados asociados exitosamente: " . implode(', ', $asociadosExitosamente);
            }
            
            if (!empty($errores)) {
                if (!empty($mensaje)) {
                    $mensaje .= "\n\nErrores encontrados:\n" . implode("\n", $errores);
                } else {
                    $mensaje = "No se pudieron asociar los resultados:\n" . implode("\n", $errores);
                }
                
                return redirect()->back()
                    ->with('warning', $mensaje);
            }
            
            return redirect()->back()
                ->with('success', $mensaje);
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al asociar múltiples resultados: ' . $e->getMessage(), [
                'competencia_id' => $competencia->id,
                'resultado_ids' => $request->resultado_ids ?? [],
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Error al asociar los resultados de aprendizaje.');
        }
    }

    public function desasociarResultado(Competencia $competencia, ResultadosAprendizaje $resultado)
    {
        try {
            if (!$competencia->resultadosAprendizaje()->where('resultados_aprendizajes.id', $resultado->id)->exists()) {
                return redirect()->back()->with('error', 'Este resultado de aprendizaje no está asignado a la competencia.');
            }
            
            $competencia->resultadosAprendizaje()->detach($resultado->id);
            
            return redirect()->back()->with('success', 'Resultado de aprendizaje desasociado exitosamente.');
            
        } catch (Exception $e) {
            Log::error('Error al desasociar resultado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al desasociar el resultado de aprendizaje.');
        }
    }
}

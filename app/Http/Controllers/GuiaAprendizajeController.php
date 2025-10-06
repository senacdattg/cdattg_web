<?php

namespace App\Http\Controllers;

use App\Http\Requests\Storeguia_aprendizajeRequest;
use App\Http\Requests\Updateguia_aprendizajeRequest;
use App\Models\GuiasAprendizaje;
use App\Models\ResultadosAprendizaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class GuiaAprendizajeController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica middleware de autenticación y permisos específicos.
     */
    public function __construct()
    {
        $this->middleware('auth');
        
        $this->middleware('can:VER GUIA APRENDIZAJE')->only(['index', 'show']);
        $this->middleware('can:CREAR GUIA APRENDIZAJE')->only(['create', 'store']);
        $this->middleware('can:EDITAR GUIA APRENDIZAJE')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR GUIA APRENDIZAJE')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = GuiasAprendizaje::with(['resultadosAprendizaje.competencias', 'actividades', 'userCreate', 'userEdit']);
            
            // Aplicar filtros si existen
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('codigo', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('nombre', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('descripcion', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            if ($request->filled('codigo')) {
                $query->where('codigo', 'LIKE', "%{$request->codigo}%");
            }
            
            if ($request->filled('nombre')) {
                $query->where('nombre', 'LIKE', "%{$request->nombre}%");
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('nivel_dificultad')) {
                $query->where('nivel_dificultad', $request->nivel_dificultad);
            }
            
            if ($request->filled('competencia_id')) {
                $query->whereHas('resultadosAprendizaje.competencias', function($q) use ($request) {
                    $q->where('competencias.id', $request->competencia_id);
                });
            }
            
            if ($request->filled('resultado_id')) {
                $query->whereHas('resultadosAprendizaje', function($q) use ($request) {
                    $q->where('resultados_aprendizajes.id', $request->resultado_id);
                });
            }
            
            if ($request->filled('user_create_id')) {
                $query->where('user_create_id', $request->user_create_id);
            }
            
            if ($request->filled('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->fecha_desde);
            }
            
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->fecha_hasta);
            }
            
            // Ordenamiento
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            $guiasAprendizaje = $query->paginate(10)->withQueryString();
            
            // Obtener datos para filtros
            $competencias = \App\Models\Competencia::orderBy('nombre')->get();
            $resultadosAprendizaje = ResultadosAprendizaje::orderBy('codigo')->get();
            $usuarios = \App\Models\User::with('persona')->get();
            
            return view('guias_aprendizaje.index', compact(
                'guiasAprendizaje', 
                'competencias', 
                'resultadosAprendizaje', 
                'usuarios'
            ));
        } catch (Exception $e) {
            Log::error('Error al obtener lista de guías de aprendizaje: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar las guías de aprendizaje.');
        }
    }

    /**
     * Búsqueda avanzada con AJAX.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $query = GuiasAprendizaje::with(['resultadosAprendizaje.competencias', 'userCreate', 'userEdit']);
            
            // Búsqueda general
            if ($request->filled('q')) {
                $searchTerm = $request->q;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('codigo', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('nombre', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('descripcion', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            // Filtros específicos
            if ($request->filled('codigo')) {
                $query->where('codigo', 'LIKE', "%{$request->codigo}%");
            }
            
            if ($request->filled('nombre')) {
                $query->where('nombre', 'LIKE', "%{$request->nombre}%");
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('competencia_id')) {
                $query->whereHas('resultadosAprendizaje.competencias', function($q) use ($request) {
                    $q->where('competencias.id', $request->competencia_id);
                });
            }
            
            if ($request->filled('resultado_id')) {
                $query->whereHas('resultadosAprendizaje', function($q) use ($request) {
                    $q->where('resultados_aprendizajes.id', $request->resultado_id);
                });
            }
            
            // Ordenamiento
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            $guiasAprendizaje = $query->paginate(10);
            
            // Preparar datos para respuesta JSON
            $data = [
                'success' => true,
                'data' => $guiasAprendizaje->items(),
                'pagination' => [
                    'current_page' => $guiasAprendizaje->currentPage(),
                    'last_page' => $guiasAprendizaje->lastPage(),
                    'per_page' => $guiasAprendizaje->perPage(),
                    'total' => $guiasAprendizaje->total(),
                    'from' => $guiasAprendizaje->firstItem(),
                    'to' => $guiasAprendizaje->lastItem(),
                ],
                'links' => $guiasAprendizaje->links()->toHtml(),
            ];
            
            return response()->json($data);
            
        } catch (Exception $e) {
            Log::error('Error en búsqueda de guías de aprendizaje: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al realizar la búsqueda',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            $resultadosAprendizaje = ResultadosAprendizaje::where('status', 1)->get();
            
            return view('guias_aprendizaje.create', compact('resultadosAprendizaje'));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de creación de guía de aprendizaje: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el formulario de creación.');
        }
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param Storeguia_aprendizajeRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Storeguia_aprendizajeRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            $data['user_create_id'] = Auth::id();
            $data['user_edit_id'] = Auth::id();
            
            $guiaAprendizaje = GuiasAprendizaje::create($data);
            
            // Sincronizar resultados de aprendizaje si se proporcionan
            if ($request->has('resultados_aprendizaje')) {
                $guiaAprendizaje->resultadosAprendizaje()->sync($request->resultados_aprendizaje);
            }
            
            DB::commit();
            
            Log::info('Guía de aprendizaje creada exitosamente', [
                'guia_id' => $guiaAprendizaje->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('guias-aprendizaje.index')
                ->with('success', 'Guía de aprendizaje creada exitosamente.');
                
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear guía de aprendizaje: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la guía de aprendizaje. Intente nuevamente.');
        }
    }

    /**
     * Display the specified resource.
     * 
     * @param GuiasAprendizaje $guiaAprendizaje
     * @return \Illuminate\View\View
     */
    public function show(GuiasAprendizaje $guiaAprendizaje)
    {
        try {
            $guiaAprendizaje->load(['resultadosAprendizaje', 'actividades']);
            
            return view('guias_aprendizaje.show', compact('guiaAprendizaje'));
        } catch (Exception $e) {
            Log::error('Error al mostrar guía de aprendizaje: ' . $e->getMessage(), [
                'guia_id' => $guiaAprendizaje->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()->with('error', 'Error al cargar la guía de aprendizaje.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param GuiasAprendizaje $guiaAprendizaje
     * @return \Illuminate\View\View
     */
    public function edit(GuiasAprendizaje $guiaAprendizaje)
    {
        try {
            $resultadosAprendizaje = ResultadosAprendizaje::where('status', 1)->get();
            $guiaAprendizaje->load('resultadosAprendizaje');
            
            return view('guias_aprendizaje.edit', compact('guiaAprendizaje', 'resultadosAprendizaje'));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de edición de guía de aprendizaje: ' . $e->getMessage(), [
                'guia_id' => $guiaAprendizaje->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()->with('error', 'Error al cargar el formulario de edición.');
        }
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param Updateguia_aprendizajeRequest $request
     * @param GuiasAprendizaje $guiaAprendizaje
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Updateguia_aprendizajeRequest $request, GuiasAprendizaje $guiaAprendizaje)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            $data['user_edit_id'] = Auth::id();
            
            $guiaAprendizaje->update($data);
            
            // Sincronizar resultados de aprendizaje si se proporcionan
            if ($request->has('resultados_aprendizaje')) {
                $guiaAprendizaje->resultadosAprendizaje()->sync($request->resultados_aprendizaje);
            }
            
            DB::commit();
            
            Log::info('Guía de aprendizaje actualizada exitosamente', [
                'guia_id' => $guiaAprendizaje->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('guias-aprendizaje.index')
                ->with('success', 'Guía de aprendizaje actualizada exitosamente.');
                
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar guía de aprendizaje: ' . $e->getMessage(), [
                'guia_id' => $guiaAprendizaje->id,
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la guía de aprendizaje. Intente nuevamente.');
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param GuiasAprendizaje $guiaAprendizaje
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(GuiasAprendizaje $guiaAprendizaje)
    {
        try {
            DB::beginTransaction();
            
            // Verificar si la guía tiene actividades asociadas
            if ($guiaAprendizaje->actividades()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar la guía de aprendizaje porque tiene actividades asociadas.');
            }
            
            // Eliminar relaciones con resultados de aprendizaje
            $guiaAprendizaje->resultadosAprendizaje()->detach();
            
            // Eliminar la guía
            $guiaAprendizaje->delete();
            
            DB::commit();
            
            Log::info('Guía de aprendizaje eliminada exitosamente', [
                'guia_id' => $guiaAprendizaje->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('guias-aprendizaje.index')
                ->with('success', 'Guía de aprendizaje eliminada exitosamente.');
                
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar guía de aprendizaje: ' . $e->getMessage(), [
                'guia_id' => $guiaAprendizaje->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error al eliminar la guía de aprendizaje. Intente nuevamente.');
        }
    }

    /**
     * API endpoint para obtener todas las guías de aprendizaje.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiIndex()
    {
        try {
            $guiasAprendizaje = GuiasAprendizaje::with(['resultadosAprendizaje'])
                ->where('status', 1)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $guiasAprendizaje
            ]);
        } catch (Exception $e) {
            Log::error('Error en API de guías de aprendizaje: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las guías de aprendizaje'
            ], 500);
        }
    }

    /**
     * Cambiar el estado de una guía de aprendizaje.
     * 
     * @param GuiasAprendizaje $guiaAprendizaje
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cambiarEstado(GuiasAprendizaje $guiaAprendizaje)
    {
        try {
            $nuevoEstado = $guiaAprendizaje->status === 1 ? 0 : 1;
            $guiaAprendizaje->update([
                'status' => $nuevoEstado,
                'user_edit_id' => Auth::id()
            ]);
            
            Log::info('Estado de guía de aprendizaje cambiado', [
                'guia_id' => $guiaAprendizaje->id,
                'nuevo_estado' => $nuevoEstado,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('success', 'Estado cambiado exitosamente');
                
        } catch (Exception $e) {
            Log::error('Error al cambiar estado de guía de aprendizaje: ' . $e->getMessage(), [
                'guia_id' => $guiaAprendizaje->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error al cambiar el estado. Intente nuevamente.');
        }
    }

    /**
     * Gestionar resultados de aprendizaje de una guía.
     * 
     * @param GuiasAprendizaje $guiaAprendizaje
     * @return \Illuminate\View\View
     */
    public function gestionarResultados(GuiasAprendizaje $guiaAprendizaje)
    {
        try {
            // Obtener resultados ya asignados a la guía
            $resultadosAsignados = $guiaAprendizaje->resultadosAprendizaje()
                ->with(['competencias'])
                ->get();
            
            // Obtener todos los resultados disponibles (excluyendo los ya asignados)
            $resultadosDisponibles = ResultadosAprendizaje::whereNotIn('id', $resultadosAsignados->pluck('id'))
                ->with(['competencias'])
                ->orderBy('codigo')
                ->get();
            
            // Agrupar por competencia para mejor organización
            $resultadosPorCompetencia = $resultadosDisponibles->groupBy(function ($resultado) {
                return $resultado->competencias->first()->nombre ?? 'Sin Competencia';
            });
            
            return view('guias_aprendizaje.gestionar_resultados', compact(
                'guiaAprendizaje',
                'resultadosAsignados',
                'resultadosDisponibles',
                'resultadosPorCompetencia'
            ));
            
        } catch (Exception $e) {
            Log::error('Error al gestionar resultados de guía: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar la gestión de resultados.');
        }
    }

    /**
     * Asociar un resultado de aprendizaje a una guía.
     * 
     * @param Request $request
     * @param GuiasAprendizaje $guiaAprendizaje
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asociarResultado(Request $request, GuiasAprendizaje $guiaAprendizaje)
    {
        try {
            $request->validate([
                'resultado_id' => 'required|exists:resultados_aprendizajes,id',
                'es_obligatorio' => 'boolean',
            ]);
            
            $resultadoId = $request->resultado_id;
            $esObligatorio = $request->boolean('es_obligatorio', true);
            
            // Verificar que el resultado no esté ya asignado
            if ($guiaAprendizaje->resultadosAprendizaje()->where('resultados_aprendizajes.id', $resultadoId)->exists()) {
                return redirect()->back()->with('error', 'Este resultado ya está asignado a la guía.');
            }
            
            // Validar competencia si hay resultados ya asignados
            $resultadosExistentes = $guiaAprendizaje->resultadosAprendizaje()->with('competencias')->get();
            if ($resultadosExistentes->isNotEmpty()) {
                $competenciaExistente = $resultadosExistentes->first()->competencias->first();
                $nuevoResultado = ResultadosAprendizaje::with('competencias')->find($resultadoId);
                $competenciaNueva = $nuevoResultado->competencias->first();
                
                if ($competenciaExistente && $competenciaNueva && $competenciaExistente->id !== $competenciaNueva->id) {
                    return redirect()->back()->with('error', 'Los resultados deben pertenecer a la misma competencia.');
                }
            }
            
            // Asociar el resultado
            $guiaAprendizaje->resultadosAprendizaje()->attach($resultadoId, [
                'es_obligatorio' => $esObligatorio,
                'user_create_id' => Auth::id(),
                'user_edit_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return redirect()->back()->with('success', 'Resultado asociado exitosamente.');
            
        } catch (Exception $e) {
            Log::error('Error al asociar resultado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al asociar el resultado.');
        }
    }

    /**
     * Desasociar un resultado de aprendizaje de una guía.
     * 
     * @param GuiasAprendizaje $guiaAprendizaje
     * @param ResultadosAprendizaje $resultado
     * @return \Illuminate\Http\RedirectResponse
     */
    public function desasociarResultado(GuiasAprendizaje $guiaAprendizaje, ResultadosAprendizaje $resultado)
    {
        try {
            // Verificar que el resultado esté asignado
            if (!$guiaAprendizaje->resultadosAprendizaje()->where('resultados_aprendizajes.id', $resultado->id)->exists()) {
                return redirect()->back()->with('error', 'Este resultado no está asignado a la guía.');
            }
            
            // Desasociar el resultado
            $guiaAprendizaje->resultadosAprendizaje()->detach($resultado->id);
            
            return redirect()->back()->with('success', 'Resultado desasociado exitosamente.');
            
        } catch (Exception $e) {
            Log::error('Error al desasociar resultado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al desasociar el resultado.');
        }
    }

    /**
     * Cambiar el estado de obligatoriedad de un resultado.
     * 
     * @param Request $request
     * @param GuiasAprendizaje $guiaAprendizaje
     * @param ResultadosAprendizaje $resultado
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cambiarObligatoriedad(Request $request, GuiasAprendizaje $guiaAprendizaje, ResultadosAprendizaje $resultado)
    {
        try {
            $request->validate([
                'es_obligatorio' => 'required|boolean',
            ]);
            
            $esObligatorio = $request->boolean('es_obligatorio');
            
            // Actualizar el estado de obligatoriedad en el pivot
            $guiaAprendizaje->resultadosAprendizaje()->updateExistingPivot($resultado->id, [
                'es_obligatorio' => $esObligatorio,
                'user_edit_id' => Auth::id(),
                'updated_at' => now(),
            ]);
            
            $mensaje = $esObligatorio ? 'Resultado marcado como obligatorio' : 'Resultado marcado como opcional';
            return redirect()->back()->with('success', $mensaje);
            
        } catch (Exception $e) {
            Log::error('Error al cambiar obligatoriedad: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cambiar la obligatoriedad del resultado.');
        }
    }
}

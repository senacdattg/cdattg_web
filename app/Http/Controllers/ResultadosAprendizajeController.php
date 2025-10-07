<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResultadosAprendizajeRequest;
use App\Http\Requests\UpdateResultadosAprendizajeRequest;
use App\Models\ResultadosAprendizaje;
use App\Models\Competencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ResultadosAprendizajeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
        $this->middleware('can:VER RESULTADO APRENDIZAJE')->only(['index', 'show']);
        $this->middleware('can:CREAR RESULTADO APRENDIZAJE')->only(['create', 'store']);
        $this->middleware('can:EDITAR RESULTADO APRENDIZAJE')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR RESULTADO APRENDIZAJE')->only('destroy');
    }

    public function index(Request $request)
    {
        try {
            $query = ResultadosAprendizaje::with(['competencias', 'userCreate', 'userEdit']);
            
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('codigo', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('nombre', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            if ($request->filled('competencia_id')) {
                $query->whereHas('competencias', function($q) use ($request) {
                    $q->where('competencias.id', $request->competencia_id);
                });
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            $query->orderBy('codigo', 'asc');
            
            $resultadosAprendizaje = $query->paginate(10)->withQueryString();
            
            return view('resultados_aprendizaje.index', compact('resultadosAprendizaje'));
            
        } catch (Exception $e) {
            Log::error('Error al obtener lista de resultados de aprendizaje: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar los resultados de aprendizaje.');
        }
    }

    public function create()
    {
        try {
            $competencias = Competencia::orderBy('nombre')->get();
            
            return view('resultados_aprendizaje.create', compact('competencias'));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de creación de resultado de aprendizaje: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el formulario de creación.');
        }
    }

    public function store(StoreResultadosAprendizajeRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            $data['user_create_id'] = Auth::id();
            $data['user_edit_id'] = Auth::id();
            
            $resultadoAprendizaje = ResultadosAprendizaje::create($data);
            
            if ($request->filled('competencia_id')) {
                $resultadoAprendizaje->competencias()->attach($request->competencia_id, [
                    'user_create_id' => Auth::id(),
                    'user_edit_id' => Auth::id(),
                ]);
            }
            
            DB::commit();
            
            Log::info('Resultado de aprendizaje creado exitosamente', [
                'resultado_id' => $resultadoAprendizaje->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('resultados-aprendizaje.index')
                ->with('success', 'Resultado de aprendizaje creado exitosamente.');
                
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear resultado de aprendizaje: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el resultado de aprendizaje. Intente nuevamente.');
        }
    }

    public function show(ResultadosAprendizaje $resultadoAprendizaje)
    {
        try {
            $resultadoAprendizaje->load(['competencias', 'guiasAprendizaje', 'userCreate', 'userEdit']);
            
            return view('resultados_aprendizaje.show', compact('resultadoAprendizaje'));
        } catch (Exception $e) {
            Log::error('Error al mostrar resultado de aprendizaje: ' . $e->getMessage(), [
                'resultado_id' => $resultadoAprendizaje->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()->with('error', 'Error al cargar el resultado de aprendizaje.');
        }
    }

    public function edit(ResultadosAprendizaje $resultadoAprendizaje)
    {
        try {
            $competencias = Competencia::orderBy('nombre')->get();
            $resultadoAprendizaje->load('competencias');
            
            return view('resultados_aprendizaje.edit', compact('resultadoAprendizaje', 'competencias'));
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de edición de resultado de aprendizaje: ' . $e->getMessage(), [
                'resultado_id' => $resultadoAprendizaje->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()->with('error', 'Error al cargar el formulario de edición.');
        }
    }

    public function update(UpdateResultadosAprendizajeRequest $request, ResultadosAprendizaje $resultadoAprendizaje)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            $data['user_edit_id'] = Auth::id();
            
            $resultadoAprendizaje->update($data);
            
            if ($request->has('competencia_id')) {
                $syncData = [];
                if ($request->competencia_id) {
                    $syncData[$request->competencia_id] = [
                        'user_edit_id' => Auth::id(),
                    ];
                }
                $resultadoAprendizaje->competencias()->sync($syncData);
            }
            
            DB::commit();
            
            Log::info('Resultado de aprendizaje actualizado exitosamente', [
                'resultado_id' => $resultadoAprendizaje->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('resultados-aprendizaje.index')
                ->with('success', 'Resultado de aprendizaje actualizado exitosamente.');
                
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar resultado de aprendizaje: ' . $e->getMessage(), [
                'resultado_id' => $resultadoAprendizaje->id,
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el resultado de aprendizaje. Intente nuevamente.');
        }
    }

    public function destroy(ResultadosAprendizaje $resultadoAprendizaje)
    {
        try {
            DB::beginTransaction();
            
            if ($resultadoAprendizaje->guiasAprendizaje()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el resultado de aprendizaje porque tiene guías asociadas.');
            }
            
            $resultadoAprendizaje->competencias()->detach();
            $resultadoAprendizaje->delete();
            
            DB::commit();
            
            Log::info('Resultado de aprendizaje eliminado exitosamente', [
                'resultado_id' => $resultadoAprendizaje->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('resultados-aprendizaje.index')
                ->with('success', 'Resultado de aprendizaje eliminado exitosamente.');
                
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar resultado de aprendizaje: ' . $e->getMessage(), [
                'resultado_id' => $resultadoAprendizaje->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error al eliminar el resultado de aprendizaje. Intente nuevamente.');
        }
    }

    public function search(Request $request)
    {
        try {
            $query = ResultadosAprendizaje::with(['competencias', 'userCreate', 'userEdit']);
            
            if ($request->filled('q')) {
                $searchTerm = $request->q;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('codigo', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('nombre', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            if ($request->filled('competencia_id')) {
                $query->whereHas('competencias', function($q) use ($request) {
                    $q->where('competencias.id', $request->competencia_id);
                });
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            $query->orderBy('codigo', 'asc');
            
            $resultadosAprendizaje = $query->paginate(10);
            
            return response()->json([
                'success' => true,
                'data' => $resultadosAprendizaje
            ]);
            
        } catch (Exception $e) {
            Log::error('Error en búsqueda de resultados de aprendizaje: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al realizar la búsqueda',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cambiarEstado(ResultadosAprendizaje $resultadoAprendizaje)
    {
        try {
            $nuevoEstado = $resultadoAprendizaje->status === 1 ? 0 : 1;
            $resultadoAprendizaje->update([
                'status' => $nuevoEstado,
                'user_edit_id' => Auth::id()
            ]);
            
            Log::info('Estado de resultado de aprendizaje cambiado', [
                'resultado_id' => $resultadoAprendizaje->id,
                'nuevo_estado' => $nuevoEstado,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('success', 'Estado cambiado exitosamente');
                
        } catch (Exception $e) {
            Log::error('Error al cambiar estado de resultado de aprendizaje: ' . $e->getMessage(), [
                'resultado_id' => $resultadoAprendizaje->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error al cambiar el estado. Intente nuevamente.');
        }
    }

    public function gestionarCompetencias(ResultadosAprendizaje $resultadoAprendizaje)
    {
        try {
            $competenciasAsignadas = $resultadoAprendizaje->competencias()->get();
            
            $competenciasDisponibles = Competencia::whereNotIn('id', $competenciasAsignadas->pluck('id'))
                ->orderBy('nombre')
                ->get();
            
            return view('resultados_aprendizaje.gestionar_competencias', compact(
                'resultadoAprendizaje',
                'competenciasAsignadas',
                'competenciasDisponibles'
            ));
            
        } catch (Exception $e) {
            Log::error('Error al gestionar competencias de resultado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar la gestión de competencias.');
        }
    }

    public function asociarCompetencia(Request $request, ResultadosAprendizaje $resultadoAprendizaje)
    {
        try {
            $request->validate([
                'competencia_id' => 'required|exists:competencias,id',
            ]);
            
            $competenciaId = $request->competencia_id;
            
            if ($resultadoAprendizaje->competencias()->where('competencias.id', $competenciaId)->exists()) {
                return redirect()->back()->with('error', 'Esta competencia ya está asignada al resultado.');
            }
            
            $resultadoAprendizaje->competencias()->attach($competenciaId, [
                'user_create_id' => Auth::id(),
                'user_edit_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return redirect()->back()->with('success', 'Competencia asociada exitosamente.');
            
        } catch (Exception $e) {
            Log::error('Error al asociar competencia: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al asociar la competencia.');
        }
    }

    public function desasociarCompetencia(ResultadosAprendizaje $resultadoAprendizaje, Competencia $competencia)
    {
        try {
            if (!$resultadoAprendizaje->competencias()->where('competencias.id', $competencia->id)->exists()) {
                return redirect()->back()->with('error', 'Esta competencia no está asignada al resultado.');
            }
            
            $resultadoAprendizaje->competencias()->detach($competencia->id);
            
            return redirect()->back()->with('success', 'Competencia desasociada exitosamente.');
            
        } catch (Exception $e) {
            Log::error('Error al desasociar competencia: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al desasociar la competencia.');
        }
    }
}

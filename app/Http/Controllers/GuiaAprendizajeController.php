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
    public function index()
    {
        try {
            $guiasAprendizaje = GuiasAprendizaje::with(['resultadosAprendizaje', 'actividades'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return view('guias_aprendizaje.index', compact('guiasAprendizaje'));
        } catch (Exception $e) {
            Log::error('Error al obtener lista de guías de aprendizaje: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar las guías de aprendizaje.');
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
}

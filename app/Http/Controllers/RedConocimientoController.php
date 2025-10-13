<?php

namespace App\Http\Controllers;

use App\Services\RedConocimientoService;
use App\Http\Requests\StoreRedConocimientoRequest;
use App\Http\Requests\UpdateRedConocimientoRequest;
use App\Models\RedConocimiento;
use App\Models\Regional;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class RedConocimientoController extends Controller
{
    protected RedConocimientoService $redService;

    /**
     * Constructor: aplica middleware de autenticación y permisos.
     */
    public function __construct(RedConocimientoService $redService)
    {
        $this->middleware('auth');
        $this->redService = $redService;

        $this->middleware('can:VER RED CONOCIMIENTO')->only(['index', 'show']);
        $this->middleware('can:CREAR RED CONOCIMIENTO')->only(['create', 'store']);
        $this->middleware('can:EDITAR RED CONOCIMIENTO')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR RED CONOCIMIENTO')->only('destroy');
    }

    /**
     * Muestra el listado de redes de conocimiento.
     */
    public function index()
    {
        try {
            $redesConocimiento = $this->redService->listar(10);
            $regionales = Regional::where('status', 1)->get();
            return view('red_conocimiento.index', compact('redesConocimiento', 'regionales'));
        } catch (\Exception $e) {
            Log::error('Error al listar redes de conocimiento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar redes de conocimiento.');
        }
    }

    /**
     * Muestra el formulario para crear una nueva red de conocimiento.
     */
    public function create()
    {
        try {
            $regionales = Regional::where('status', 1)->get();
            return view('red_conocimiento.create', compact('regionales'));
        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de creación: ' . $e->getMessage());
            return redirect()->route('red-conocimiento.index')
                ->withErrors(['error' => 'Ocurrió un error al cargar el formulario.']);
        }
    }

    /**
     * Almacena una nueva red de conocimiento.
     */
    public function store(StoreRedConocimientoRequest $request)
    {
        try {
            $datos = $request->validated();
            $datos['user_create_id'] = Auth::id();
            $datos['user_edit_id'] = Auth::id();

            $this->redService->crear($datos);
            
            return redirect()->route('red-conocimiento.index')
                ->with('success', '¡Red de conocimiento creada exitosamente!');
        } catch (QueryException $e) {
            Log::error('Error al crear red de conocimiento: ' . $e->getMessage());
            
            if ($e->getCode() == 23000) {
                return redirect()->back()->withInput()
                    ->with('error', 'Ya existe una red con este nombre.');
            }
            
            return redirect()->back()->withInput()
                ->with('error', 'Error al crear red de conocimiento.');
        }
    }

    /**
     * Muestra los detalles de una red de conocimiento específica.
     */
    public function show(RedConocimiento $redConocimiento)
    {
        try {
            $redConocimiento->load('regional');
            return view('red_conocimiento.show', compact('redConocimiento'));
        } catch (\Exception $e) {
            Log::error('Error al mostrar red de conocimiento: ' . $e->getMessage());
            return redirect()->route('red-conocimiento.index')
                ->withErrors(['error' => 'Ocurrió un error al cargar los detalles.']);
        }
    }

    /**
     * Muestra el formulario para editar una red de conocimiento.
     */
    public function edit(RedConocimiento $redConocimiento)
    {
        try {
            $regionales = Regional::where('status', 1)->get();
            return view('red_conocimiento.edit', compact('redConocimiento', 'regionales'));
        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de edición: ' . $e->getMessage());
            return redirect()->route('red-conocimiento.index')
                ->withErrors(['error' => 'Ocurrió un error al cargar el formulario de edición.']);
        }
    }

    /**
     * Actualiza una red de conocimiento existente.
     */
    public function update(UpdateRedConocimientoRequest $request, RedConocimiento $redConocimiento)
    {
        try {
            $datos = $request->validated();
            $datos['user_edit_id'] = Auth::id();

            $this->redService->actualizar($redConocimiento->id, $datos);
            
            return redirect()->route('red-conocimiento.show', $redConocimiento->id)
                ->with('success', 'Red de conocimiento actualizada exitosamente.');
        } catch (QueryException $e) {
            Log::error('Error al actualizar red de conocimiento: ' . $e->getMessage());
            
            if ($e->getCode() == 23000) {
                return redirect()->back()->withInput()
                    ->with('error', 'Ya existe una red con este nombre.');
            }
            
            return redirect()->back()->withInput()
                ->with('error', 'Error al actualizar red de conocimiento.');
        }
    }

    /**
     * Elimina una red de conocimiento.
     */
    public function destroy(RedConocimiento $redConocimiento)
    {
        try {
            $this->redService->eliminar($redConocimiento->id);
            
            return redirect()->route('red-conocimiento.index')
                ->with('success', 'Red de conocimiento eliminada exitosamente.');
        } catch (QueryException $e) {
            Log::error('Error al eliminar red de conocimiento: ' . $e->getMessage());
            
            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->with('error', 'La red está en uso, no se puede eliminar.');
            }
            
            return redirect()->back()->with('error', 'Error al eliminar red.');
        }
    }

    /**
     * Cambiar el estado de una red de conocimiento.
     */
    public function cambiarEstado(RedConocimiento $redConocimiento)
    {
        try {
            $this->redService->cambiarEstado($redConocimiento->id);
            
            $mensaje = $redConocimiento->status === 0 
                ? 'Red de conocimiento activada exitosamente.' 
                : 'Red de conocimiento desactivada exitosamente.';
                
            return redirect()->back()->with('success', $mensaje);
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Error al cambiar estado.');
        }
    }
}

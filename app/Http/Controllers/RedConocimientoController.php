<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRedConocimientoRequest;
use App\Http\Requests\UpdateRedConocimientoRequest;
use App\Models\RedConocimiento;
use App\Models\Regional;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class RedConocimientoController extends Controller
{
    /**
     * Constructor: aplica middleware de autenticación y permisos.
     */
    public function __construct()
    {
        $this->middleware('auth');

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
            $redesConocimiento = RedConocimiento::with('regional')->paginate(10);
            return view('red_conocimiento.index', compact('redesConocimiento'));
        } catch (\Exception $e) {
            Log::error('Error al listar redes de conocimiento: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al cargar las redes de conocimiento.']);
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
            DB::beginTransaction();

            $data = $request->validated();
            $data['user_create_id'] = Auth::id();
            $data['user_edit_id'] = Auth::id();

            RedConocimiento::create($data);

            DB::commit();
            
            return redirect()->route('red-conocimiento.index')
                ->with('success', '¡Red de conocimiento creada exitosamente!');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al crear red de conocimiento: ' . $e->getMessage());
            
            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'Ya existe una red de conocimiento con este nombre.']);
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al crear la red de conocimiento.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al crear red de conocimiento: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error inesperado al crear la red de conocimiento.']);
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
            DB::beginTransaction();

            $data = $request->validated();

            if ($redConocimiento->user_edit_id !== Auth::id()) {
                $data['user_edit_id'] = Auth::id();
            }

            $redConocimiento->update($data);

            DB::commit();
            
            return redirect()->route('red-conocimiento.show', $redConocimiento->id)
                ->with('success', 'Red de conocimiento actualizada exitosamente.');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al actualizar red de conocimiento: ' . $e->getMessage());
            
            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'Ya existe una red de conocimiento con este nombre.']);
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al actualizar la red de conocimiento.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al actualizar red de conocimiento: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error inesperado al actualizar la red de conocimiento.']);
        }
    }

    /**
     * Elimina una red de conocimiento.
     */
    public function destroy(RedConocimiento $redConocimiento)
    {
        try {
            DB::beginTransaction();
            
            $redConocimiento->delete();
            
            DB::commit();
            
            return redirect()->route('red-conocimiento.index')
                ->with('success', 'Red de conocimiento eliminada exitosamente.');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al eliminar red de conocimiento: ' . $e->getMessage());
            
            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->withErrors(['error' => 'La red de conocimiento está siendo usada y no es posible eliminarla.']);
            }
            
            return redirect()->back()
                ->withErrors(['error' => 'Ocurrió un error al eliminar la red de conocimiento.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al eliminar red de conocimiento: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Ocurrió un error inesperado al eliminar la red de conocimiento.']);
        }
    }

    /**
     * Cambiar el estado de una red de conocimiento.
     */
    public function cambiarEstado(RedConocimiento $redConocimiento)
    {
        try {
            DB::beginTransaction();
            
            // Cambiar el estado (1 -> 0 o 0 -> 1)
            $nuevoEstado = $redConocimiento->status === 1 ? 0 : 1;
            
            $redConocimiento->update([
                'status' => $nuevoEstado,
                'user_edit_id' => Auth::id(),
            ]);
            
            DB::commit();
            
            $mensaje = $nuevoEstado === 1 
                ? 'Red de conocimiento activada exitosamente.' 
                : 'Red de conocimiento desactivada exitosamente.';
                
            return redirect()->back()
                ->with('success', $mensaje);
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al cambiar estado de red de conocimiento: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Ocurrió un error al cambiar el estado de la red de conocimiento.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al cambiar estado de red de conocimiento: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Ocurrió un error inesperado al cambiar el estado.']);
        }
    }
}

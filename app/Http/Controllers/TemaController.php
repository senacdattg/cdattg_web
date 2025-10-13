<?php

namespace App\Http\Controllers;

use App\Services\TemaService;
use App\Models\Tema;
use App\Models\Parametro;
use App\Http\Requests\StoreTemaRequest;
use App\Http\Requests\UpdateTemaRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TemaController extends Controller
{
    protected TemaService $temaService;

    /**
     * Display a listing of the resource.
     */
    public function __construct(TemaService $temaService)
    {
        $this->middleware('auth');
        $this->temaService = $temaService;

        $this->middleware('can:VER TEMA')->only(['index', 'show']);
        $this->middleware('can:CREAR TEMA')->only(['create', 'store']);
        $this->middleware('can:EDITAR TEMA')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR TEMA')->only('destroy');
    }

    public function index()
    {
        $temas = $this->temaService->listar(10);
        return view('temas.index', compact('temas'));
    }

    public function store(StoreTemaRequest $request)
    {
        try {
            $datos = $request->validated();
            $datos['user_create_id'] = Auth::id();
            $datos['user_edit_id'] = Auth::id();

            $this->temaService->crear($datos);

            return redirect()->back()->with('success', '¡Tema creado exitosamente!');
        } catch (\Exception $e) {
            Log::error('Error al crear tema: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al crear el tema.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Tema $tema)
    {
        return view('temas.show', compact('tema'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tema $tema)
    {
        $parametros = parametro::where('status', 1)->get();
        return view('temas.edit', compact('tema', 'parametros'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTemaRequest $request, Tema $tema)
    {
        try {
            $datos = $request->validated();
            $datos['user_edit_id'] = Auth::id();

            $this->temaService->actualizar($tema->id, $datos);

            return redirect()->route('tema.show', $tema->id)->with('success', 'Tema actualizado exitosamente');
        } catch (QueryException $e) {
            Log::error('Error al actualizar tema: ' . $e->getMessage());
            
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'El nombre del tema ya existe.');
            }
            return redirect()->back()->with('error', 'Error al actualizar el tema.');
        }
    }

    public function updateParametrosTemas(Request $request)
    {
        try {
            $data = $request->validate([
                'tema_id' => 'required|integer|exists:temas,id',
                'parametros' => 'nullable|array',
                'parametros.*' => 'integer|exists:parametros,id',
                'estados' => 'nullable|array',
            ]);

            $parametros = $data['parametros'] ?? [];
            $estados = $data['estados'] ?? array_fill(0, count($parametros), 1);

            $this->temaService->actualizarParametros($data['tema_id'], $parametros, $estados);

            return redirect()->back()->with('success', 'Parámetros actualizados exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar parámetros: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar parámetros.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tema $tema)
    {
        try {
            $this->temaService->eliminar($tema->id);

            return redirect()->route('tema.index')->with('success', 'Tema eliminado exitosamente');
        } catch (QueryException $e) {
            Log::error('Error al eliminar tema: ' . $e->getMessage());
            
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'El tema está en uso, no se puede eliminar');
            }
            return redirect()->back()->with('error', 'Error al eliminar tema.');
        }
    }

    public function cambiarEstado(Tema $tema)
    {
        try {
            $nuevoEstado = $tema->status === 1 ? 0 : 1;
            $tema->update([
                'status' => $nuevoEstado,
                'user_edit_id' => Auth::id(), // Actualiza el usuario que realiza el cambio
            ]);
            return redirect()->back()->with('success', 'Estado cambiado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al cambiar el estado del tema: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo cambiar el estado');
        }
    }

    public function cambiarEstadoParametro(Tema $tema, Parametro $parametro)
    {
        try {
            DB::transaction(function () use ($tema, $parametro) {
                // Obtenemos el registro pivote con los datos cargados
                $parametroAdjunto = $tema->parametros()->where('parametros.id', $parametro->id)->first();

                if (!$parametroAdjunto) {
                    throw new \Exception('El parámetro no está vinculado al tema');
                }

                // Accedemos al valor del campo 'status' desde el pivot
                $nuevoEstado = $parametroAdjunto->pivot->status == 1 ? 0 : 1;

                // Actualizamos el registro del pivot usando el método updateExistingPivot()
                $tema->parametros()->updateExistingPivot($parametro->id, [
                    'status'       => $nuevoEstado,
                    'user_edit_id' => Auth::id(),
                    'updated_at'   => now(), // Forzamos la actualización del timestamp en el pivot
                ]);

                // Actualizamos el tema (opcional, para registrar la acción)
                $tema->update(['user_edit_id' => Auth::id()]);
                $tema->touch();
            });

            return redirect()->back()->with('success', 'Estado cambiado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al cambiar el estado del parámetro: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo cambiar el estado');
        }
    }


    public function eliminarParametro(Tema $tema, Parametro $parametro)
    {
        try {
            DB::transaction(function () use ($tema, $parametro) {
                // Desvincular el parámetro del tema
                $tema->parametros()->detach($parametro->id);
                // Actualizar el timestamp y registrar el usuario que realiza la acción
                $tema->touch();
                $tema->update(['user_edit_id' => Auth::id()]);
            });
            return redirect()->back()->with('success', 'Parámetro eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar el parámetro: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo eliminar el parámetro');
        }
    }
}

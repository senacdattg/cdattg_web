<?php

namespace App\Http\Controllers;

use App\Services\ParametroService;
use App\Models\Parametro;
use App\Http\Requests\StoreParametroRequest;
use App\Http\Requests\UpdateParametroRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class ParametroController extends Controller
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->middleware('auth');
        $this->parametroService = $parametroService;

        $this->middleware('can:VER PARAMETRO')->only(['index', 'show']);
        $this->middleware('can:CREAR PARAMETRO')->only(['create', 'store']);
        $this->middleware('can:EDITAR PARAMETRO')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR PARAMETRO')->only('destroy');
    }

    public function index()
    {
        $parametros = $this->parametroService->listar(10);
        return view('parametros.index', compact('parametros'));
    }

    public function apiIndex()
    {
        $parametros = $this->parametroService->obtenerTodos();
        return response()->json($parametros);
    }

    public function cambiarEstado(Parametro $parametro)
    {
        try {
            $this->parametroService->cambiarEstado($parametro->id);
            return redirect()->back()->with('success', 'Estado cambiado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cambiar estado.');
        }
    }

    public function store(StoreParametroRequest $request)
    {
        try {
            $datos = $request->validated();
            $datos['user_create_id'] = Auth::id();
            $datos['user_edit_id'] = Auth::id();

            $this->parametroService->crear($datos);

            return redirect()->back()->with('success', '¡Parámetro creado exitosamente!');
        } catch (\Exception $e) {
            Log::error('Error al crear parámetro: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al crear parámetro.');
        }
    }

    public function show(Parametro $parametro)
    {
        return view('parametros.show', compact('parametro'));
    }

    public function edit(Parametro $parametro)
    {
        return view('parametros.edit', compact('parametro'));
    }

    public function update(UpdateParametroRequest $request, Parametro $parametro)
    {
        try {
            $datos = $request->validated();
            $datos['user_edit_id'] = Auth::id();

            $this->parametroService->actualizar($parametro->id, $datos);

            return redirect()->route('parametro.show', $parametro->id)
                ->with('success', 'Parámetro actualizado exitosamente');
        } catch (QueryException $e) {
            Log::error('Error al actualizar parámetro: ' . $e->getMessage());
            
            if ($e->getCode() == 23000) {
                return redirect()->back()->withInput()->with('error', 'El nombre del parámetro ya existe.');
            }
            return redirect()->back()->withInput()->with('error', 'Error al actualizar parámetro.');
        }
    }

    public function destroy(Parametro $parametro)
    {
        try {
            $this->parametroService->eliminar($parametro->id);

            return redirect()->route('parametro.index')->with('success', 'Parámetro eliminado exitosamente');
        } catch (QueryException $e) {
            Log::error('Error al eliminar parámetro: ' . $e->getMessage());
            
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'El parámetro está en uso, no se puede eliminar.');
            }
            return redirect()->back()->with('error', 'Error al eliminar parámetro.');
        }
    }

    public function apiGetTipoDocumentos()
    {
        // Obtener el Tema con ID 2 y sus parámetros activos
        $consultaDocumentos = Tema::with(['parametros' => function ($query) {
            $query->wherePivot('status', 1);
        }])->findOrFail(2);

        $documentos = $consultaDocumentos->parametros->map(function ($param) {
            return [
                'id' => $param->id,
                'name' => $param->name,
            ];
        })->toArray();

        return response()->json($documentos, 200);
    }

    public function apiGetGeneros()
    {
        // Obtener el Tema con ID 3 y sus parámetros activos
        $consultaGeneros = Tema::with(['parametros' => function ($query) {
            $query->wherePivot('status', 1);
        }])->findOrFail(3);

        $generos = $consultaGeneros->parametros->map(function ($param) {
            return [
                'id' => $param->id,
                'name' => $param->name,
            ];
        })->toArray();

        return response()->json($generos, 200);
    }
}

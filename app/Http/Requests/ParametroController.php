<?php

namespace App\Http\Controllers;

use App\Models\Parametro;
use App\Http\Requests\StoreParametroRequest;
use App\Http\Requests\UpdateParametroRequest;
use App\Models\Tema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class ParametroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('can:VER PARAMETRO')->only(['index', 'show']);
        $this->middleware('can:CREAR PARAMETRO')->only(['create', 'store']);
        $this->middleware('can:EDITAR PARAMETRO')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR PARAMETRO')->only('destroy');
    }

    public function index()
    {
        $parametros = Parametro::paginate(10);
        return view('parametros.index', compact('parametros'));
    }

    public function apiIndex()
    {
        $parametros = Parametro::all();
        return response()->json($parametros);
    }

    public function cambiarEstado(Parametro $parametro)
    {
        $nuevoEstado = $parametro->status === 1 ? 0 : 1;
        $parametro->update(['status' => $nuevoEstado]);
        return redirect()->back()->with('success', 'Estado cambiado exitosamente');
    }

    public function store(StoreParametroRequest $request)
    {
        $data = $request->validated();
        $data['user_create_id'] = Auth::id();
        $data['user_edit_id'] = Auth::id();

        try {
            Parametro::create($data);
            return redirect()->back()->with('success', '¡Parámetro creado exitosamente!');
        } catch (\Exception $e) {
            Log::error('Error al crear parámetro: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Ocurrió un error al crear el parámetro.']);
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
            DB::beginTransaction();

            // Obtener los datos validados del request
            $data = $request->validated();

            // Si el usuario que actualizó anteriormente es distinto al usuario actual, actualiza el campo
            if ($parametro->user_edit_id !== Auth::id()) {
                $data['user_edit_id'] = Auth::id();
            }

            // Actualizar el parámetro con los nuevos datos
            $parametro->update($data);

            DB::commit();
            return redirect()->route('parametro.show', $parametro->id)
                ->with('success', 'Parámetro actualizado exitosamente');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al actualizar parámetro: ' . $e->getMessage());
            if ($e->getCode() == 23000) {
                return redirect()->back()->withErrors(['error' => 'El nombre asignado al parámetro ya existe.']);
            }
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al actualizar el parámetro.']);
        }
    }

    public function destroy(Parametro $parametro)
    {
        try {
            DB::beginTransaction();
            $parametro->delete();
            DB::commit();
            return redirect()->route('parametro.index')->with('success', 'Parámetro eliminado exitosamente');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al eliminar parámetro: ' . $e->getMessage());
            if ($e->getCode() == 23000) {
                return redirect()->back()->withErrors(['error' => 'El parámetro está siendo usado y no es posible eliminarlo.']);
            }
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al eliminar el parámetro.']);
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

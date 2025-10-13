<?php

namespace App\Http\Controllers;

use App\Services\AmbienteService;
use App\Http\Requests\StoreAmbienteRequest;
use App\Http\Requests\UpdateAmbienteRequest;
use App\Models\Ambiente;
use App\Models\Regional;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AmbienteController extends Controller
{
    protected AmbienteService $ambienteService;

    public function __construct(AmbienteService $ambienteService)
    {
        $this->middleware('auth');
        $this->ambienteService = $ambienteService;

        $this->middleware('can:VER AMBIENTE')->only('index');
        $this->middleware('can:VER AMBIENTE')->only('show');
        $this->middleware('can:CREAR AMBIENTE')->only(['create', 'store']);
        $this->middleware('can:EDITAR AMBIENTE')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR AMBIENTE')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ambientes = $this->ambienteService->listar(10);
        return view('ambiente.index', compact('ambientes'));
    }

    public function cargarAmbientes($piso_id)
    {
        $resultado = $this->ambienteService->obtenerPorPiso($piso_id);
        return response()->json($resultado);
    }

    public function apiCargarAmbientes(Request $request)
    {
        $resultado = $this->ambienteService->obtenerPorRegional($request->regional_id);

        if (!$resultado['success']) {
            return response()->json(['error' => $resultado['message'] ?? 'Error'], 404);
        }

        return response()->json($resultado['ambientes'], 200);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regionales = Regional::where('status', 1)->get();
        return view('ambiente.create', compact('regionales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAmbienteRequest $request)
    {
        try {
            $datos = [
                'title' => $request->input('title'),
                'piso_id' => $request->input('piso_id'),
                'user_create_id' => Auth::id(),
                'user_edit_id' => Auth::id(),
            ];

            $this->ambienteService->crear($datos);

            return redirect()->route('ambiente.index')->with('success', '¡Registro Exitoso!');
        } catch (\Exception $e) {
            Log::error('Error al crear ambiente: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear ambiente.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ambiente $ambiente)
    {
        return view('ambiente.show', ['ambiente' => $ambiente]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ambiente $ambiente)
    {
        $regionales = Regional::where('status', 1)->get();
        return view('ambiente.edit', ['regionales' => $regionales, 'ambiente' => $ambiente]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAmbienteRequest $request, Ambiente $ambiente)
    {
        try {
            $datos = [
                'title' => $request->title,
                'piso_id' => $request->piso_id,
                'user_edit_id' => Auth::id(),
                'status' => $request->status,
            ];

            $this->ambienteService->actualizar($ambiente->id, $datos);

            return redirect()->route('ambiente.show', $ambiente->id)->with('success', 'Ambiente actualizado con éxito.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar ambiente: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al actualizar ambiente.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ambiente $ambiente)
    {
        try {
            $this->ambienteService->eliminar($ambiente->id);

            return redirect()->back()->with('success', 'Ambiente eliminado con éxito!');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'El ambiente está siendo usado y no puede ser eliminado!');
            }
            return redirect()->back()->with('error', 'Error al eliminar ambiente.');
        }
    }

    public function cambiarEstado(Ambiente $ambiente)
    {
        try {
            $this->ambienteService->cambiarEstado($ambiente->id);

            return redirect()->back()->with('success', 'Estado cambiado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo cambiar el estado del ambiente.');
        }
    }
}

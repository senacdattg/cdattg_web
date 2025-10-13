<?php

namespace App\Http\Controllers;

use App\Services\PisoService;
use App\Models\Piso;
use App\Models\Regional;
use App\Http\Requests\StorePisoRequest;
use App\Http\Requests\UpdatePisoRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PisoController extends Controller
{
    protected PisoService $pisoService;

    public function __construct(PisoService $pisoService)
    {
        $this->middleware('auth');
        $this->pisoService = $pisoService;

        $this->middleware('can:VER PISO')->only('index');
        $this->middleware('can:VER PISO')->only('show');
        $this->middleware('can:CREAR PISO')->only(['create', 'store']);
        $this->middleware('can:EDITAR PISO')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR PISO')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pisos = $this->pisoService->listar(10);
        return view('piso.index', compact('pisos'));
    }

    public function cargarPisos($bloque_id)
    {
        $pisos = $this->pisoService->obtenerPorBloque($bloque_id);
        return response()->json(['success' => true, 'pisos' => $pisos]);
    }

    public function apiCargarPisos(Request $request)
    {
        $pisos = $this->pisoService->obtenerPorBloque($request->bloque_id);
        return response()->json($pisos, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regionales = Regional::where('status', 1)->get();
        return view('piso.create', compact('regionales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePisoRequest $request)
    {
        try {
            $datos = [
                'piso' => $request->input('piso'),
                'bloque_id' => $request->input('bloque_id'),
                'user_create_id' => Auth::id(),
                'user_edit_id' => Auth::id(),
            ];

            $this->pisoService->crear($datos);

            return redirect()->route('piso.index')->with('success', '¡Registro Exitoso!');
        } catch (\Exception $e) {
            Log::error('Error al crear piso: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear piso.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Piso $piso)
    {
        return view('piso.show', ['piso' => $piso]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Piso $piso)
    {
        $regionales = Regional::where('status', 1)->get();
        return view('piso.edit', ['piso' => $piso, 'regionales' => $regionales]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePisoRequest $request, Piso $piso)
    {
        try {
            $datos = [
                'piso' => $request->piso,
                'bloque_id' => $request->bloque_id,
                'status' => $request->status,
            ];

            $this->pisoService->actualizar($piso->id, $datos);

            return redirect()->route('piso.show', $piso->id)->with('success', 'Piso actualizado con éxito!');
        } catch (\Exception $e) {
            Log::error('Error al actualizar piso: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al actualizar piso.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Piso $piso)
    {
        try {
            $this->pisoService->eliminar($piso->id);

            return redirect()->back()->with('success', 'Piso eliminado exitosamente');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'El piso está en uso, no se puede eliminar.');
            }
            return redirect()->back()->with('error', 'Error al eliminar piso.');
        }
    }

    public function cambiarEstado(Piso $piso)
    {
        try {
            $this->pisoService->cambiarEstado($piso->id);

            return redirect()->back()->with('success', 'Estado cambiado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar estado.');
        }
    }
}

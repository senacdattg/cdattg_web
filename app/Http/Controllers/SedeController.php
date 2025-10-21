<?php

namespace App\Http\Controllers;

use App\Services\SedeService;
use App\Models\Sede;
use App\Models\Regional;
use App\Http\Requests\StoreSedeRequest;
use App\Http\Requests\UpdateSedeRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SedeController extends Controller
{
    protected SedeService $sedeService;

    public function __construct(SedeService $sedeService)
    {
        $this->middleware('auth');
        $this->sedeService = $sedeService;

        $this->middleware('can:VER SEDE')->only('index');
        $this->middleware('can:VER SEDE')->only('show');
        $this->middleware('can:CREAR SEDE')->only(['create', 'store']);
        $this->middleware('can:EDITAR SEDE')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR SEDE')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sedes = $this->sedeService->listar(10);
        return view('sede.index', compact('sedes'));
    }

    public function cargarSedesByMunicipio($municipio_id)
    {
        $sedes = $this->sedeService->obtenerPorMunicipio($municipio_id);
        return response()->json(['success' => true, 'sedes' => $sedes]);
    }

    public function cargarSedesByRegional($regional_id)
    {
        $sedes = $this->sedeService->obtenerPorRegional($regional_id);
        return response()->json(['success' => true, 'sedes' => $sedes]);
    }

    public function apiCargarSedes(Request $request)
    {
        $sedes = $this->sedeService->obtenerPorMunicipio($request->municipio_id);
        return response()->json($sedes, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regionales = Regional::where('status', 1)->get();
        $departamentos = \App\Models\Departamento::all();
        $municipios = \App\Models\Municipio::all();
        return view('sede.create', compact('regionales', 'departamentos', 'municipios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSedeRequest $request)
    {
        try {
            $datos = [
                'sede' => $request->input('sede'),
                'direccion' => $request->input('direccion'),
                'municipio_id' => $request->input('municipio_id'),
                'user_create_id' => Auth::id(),
                'user_edit_id' => Auth::id(),
                'regional_id' => $request->input('regional_id'),
            ];

            $this->sedeService->crear($datos);

            return redirect()->route('sede.index')->with('success', '¡Registro Exitoso!');
        } catch (\Exception $e) {
            Log::error('Error al crear sede: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear sede.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sede $sede)
    {
        return view('sede.show', ['sede' => $sede]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sede $sede)
    {
        $regionales = Regional::where('status', 1)->get();
        $departamentos = \App\Models\Departamento::all();
        $municipios = \App\Models\Municipio::all();
        return view('sede.edit', compact('sede', 'regionales', 'departamentos', 'municipios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSedeRequest $request, Sede $sede)
    {
        try {
            $datos = [
                'sede' => $request->sede,
                'direccion' => $request->direccion,
                'user_edit_id' => Auth::id(),
                'status' => $request->status,
                'municipio_id' => $request->municipio_id,
                'regional_id' => $request->regional_id,
            ];

            $this->sedeService->actualizar($sede->id, $datos);

            return redirect()->route('sede.show', $sede->id)->with('success', 'Sede actualizada con éxito!');
        } catch (\Exception $e) {
            Log::error('Error al actualizar sede: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al actualizar sede.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sede $sede)
    {
        try {
            $this->sedeService->eliminar($sede->id);

            return redirect()->route('sede.index')->with('success', 'Sede eliminada exitosamente');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'La sede está en uso, no se puede eliminar');
            }
            return redirect()->back()->with('error', 'Error al eliminar sede.');
        }
    }

    public function cambiarEstadoSede(Sede $sede)
    {
        try {
            $this->sedeService->cambiarEstado($sede->id);
            return redirect()->back()->with('success', 'Estado cambiado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cambiar estado.');
        }
    }
}

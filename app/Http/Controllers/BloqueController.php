<?php

namespace App\Http\Controllers;

use App\Services\BloqueService;
use App\Models\Bloque;
use App\Models\Sede;
use App\Http\Requests\StoreBloqueRequest;
use App\Http\Requests\UpdateBloqueRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BloqueController extends Controller
{
    protected BloqueService $bloqueService;

    public function __construct(BloqueService $bloqueService)
    {
        $this->middleware('auth');
        $this->bloqueService = $bloqueService;

        $this->middleware('can:VER BLOQUE')->only('index');
        $this->middleware('can:VER BLOQUE')->only('show');
        $this->middleware('can:CREAR BLOQUE')->only(['create', 'store']);
        $this->middleware('can:EDITAR BLOQUE')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR BLOQUE')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bloques = $this->bloqueService->listar(10);
        return view('bloque.index', compact('bloques'));
    }

    public function cargarBloques($sede_id)
    {
        $bloques = $this->bloqueService->obtenerPorSede($sede_id);
        return response()->json(['success' => true, 'bloques' => $bloques]);
    }

    public function apiCargarBloques(Request $request)
    {
        $bloques = $this->bloqueService->obtenerPorSede($request->sede_id);
        return response()->json($bloques, 200);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sedes = Sede::where('status', 1)->get();
        return view('bloque.create', compact('sedes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBloqueRequest $request)
    {
        try {
            $datos = [
                'bloque' => $request->input('bloque'),
                'sede_id' => $request->input('sede_id'),
                'user_create_id' => Auth::id(),
                'user_edit_id' => Auth::id(),
            ];

            $this->bloqueService->crear($datos);

            return redirect()->route('bloque.index')->with('success', '¡Registro Exitoso!');
        } catch (\Exception $e) {
            Log::error('Error al crear bloque: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear bloque.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Bloque $bloque)
    {
        return view('bloque.show', ['bloque' => $bloque]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bloque $bloque)
    {
        $sedes = Sede::where('status', 1)->get();
        return view('bloque.edit', ['bloque' => $bloque, 'sedes' => $sedes]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBloqueRequest $request, Bloque $bloque)
    {
        try {
            $datos = [
                'bloque' => $request->bloque,
                'sede_id' => $request->sede_id,
                'status' => $request->status,
                'user_edit_id' => Auth::id(),
            ];

            $this->bloqueService->actualizar($bloque->id, $datos);

            return redirect()->route('bloque.show', $bloque->id)->with('success', 'Bloque actualizado con éxito.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar bloque: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al actualizar bloque.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bloque $bloque)
    {
        try {
            $this->bloqueService->eliminar($bloque->id);

            return redirect()->route('bloque.index')->with('success', 'Bloque eliminado exitosamente');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'El bloque está en uso, no se puede eliminar.');
            }
            return redirect()->back()->with('error', 'Error al eliminar bloque.');
        }
    }

    public function cambiarEstado(Bloque $bloque)
    {
        try {
            $this->bloqueService->cambiarEstado($bloque->id);

            return redirect()->back()->with('success', 'Estado cambiado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar estado.');
        }
    }
}

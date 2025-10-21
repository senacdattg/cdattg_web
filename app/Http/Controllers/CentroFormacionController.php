<?php

namespace App\Http\Controllers;

use App\Models\CentroFormacion;
use App\Models\Regional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class CentroFormacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('can:VER CENTRO DE FORMACION')->only(['index', 'show']);
        $this->middleware('can:CREAR CENTRO DE FORMACION')->only(['create', 'store']);
        $this->middleware('can:EDITAR CENTRO DE FORMACION')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR CENTRO DE FORMACION')->only('destroy');
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $centros = CentroFormacion::with('regional')->paginate(10);
        $regionales = Regional::where('status', 1)->get();

        return view('centros.index', compact('centros', 'regionales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regionales = Regional::where('status', 1)->get();
        return view('centros.create', compact('regionales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'regional_id' => 'required|exists:regionals,id',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'web' => 'nullable|url|max:255',
        ]);

        try {
            DB::beginTransaction();

            CentroFormacion::create([
                'nombre' => $request->nombre,
                'regional_id' => $request->regional_id,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'web' => $request->web,
                'status' => 1,
                'user_create_id' => Auth::id(),
                'user_update_id' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('centros.index')
                ->with('success', 'Centro de formación creado con éxito');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al crear centro de formación: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Error al momento de crear el centro de formación']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $centro = CentroFormacion::with('regional')->findOrFail($id);
        return view('centros.show', compact('centro'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $centro = CentroFormacion::with('regional')->findOrFail($id);
        $regionales = Regional::where('status', 1)->get();
        return view('centros.edit', compact('centro', 'regionales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'regional_id' => 'required|exists:regionals,id',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'web' => 'nullable|url|max:255',
            'status' => 'required|in:0,1',
        ]);

        try {
            DB::beginTransaction();

            $centro = CentroFormacion::findOrFail($id);
            $centro->update([
                'nombre' => $request->nombre,
                'regional_id' => $request->regional_id,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'web' => $request->web,
                'status' => $request->status,
                'user_update_id' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('centros.show', $centro->id)
                ->with('success', 'Centro de formación actualizado con éxito');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al actualizar centro de formación: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'Error al momento de actualizar el centro de formación');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $centro = CentroFormacion::findOrFail($id);
            $centro->delete();
            
            DB::commit();
            return redirect()->route('centros.index')
                ->with('success', 'Centro de formación eliminado exitosamente');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al eliminar centro de formación: ' . $e->getMessage());

            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->with('error', 'El centro de formación se encuentra en uso, no se puede eliminar');
            }

            return redirect()->back()
                ->with('error', 'Error al eliminar el centro de formación');
        }
    }

    /**
     * Cambiar el estado del centro de formación
     */
    public function cambiarEstado($id)
    {
        try {
            $centro = CentroFormacion::findOrFail($id);
            $nuevoStatus = $centro->status === 1 ? 0 : 1;
            
            $centro->update([
                'status' => $nuevoStatus,
                'user_update_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->with('success', 'Estado actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error("Error al cambiar el estado del centro de formación (ID: {$id}): " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'No se pudo actualizar el estado');
        }
    }
}

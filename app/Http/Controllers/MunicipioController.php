<?php

namespace App\Http\Controllers;

use App\Services\UbicacionService;
use App\Models\Municipio;
use App\Http\Requests\StoreMunicipioRequest;
use App\Http\Requests\UpdateMunicipioRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class MunicipioController extends Controller
{
    protected UbicacionService $ubicacionService;

    public function __construct(UbicacionService $ubicacionService)
    {
        $this->ubicacionService = $ubicacionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departamento_id = Auth::user()->persona->departamento_id;

        $municipios = Municipio::where('departamento_id', $departamento_id)->paginate(10);
        return view('municipios.index', compact('municipios'));
    }

    public function cargarMunicipios($departamento_id)
    {
        $municipios = $this->ubicacionService->obtenerMunicipiosPorDepartamento($departamento_id);
        return response()->json(['success' => true, 'municipios' => $municipios]);
    }

    public function apiCargarMunicipios(Request $request)
    {
        $municipios = $this->ubicacionService->obtenerMunicipiosPorDepartamento($request->departamento_id);
        return response()->json($municipios, 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMunicipioRequest $municipio)
    {
        $data = $municipio->validated();
        $data['user_create_id'] = Auth::id();
        $data['user_edit_id'] = Auth::id();

        try {
            Municipio::create($data);
            return redirect()->back()->with('success', '¡Municipio creado exitosamente!');
        } catch (\Exception $e) {
            Log::error('Error al crear municipio: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Ocurrió un error al crear el municipio.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Municipio $municipio)
    {
        return view('municipios.show', compact('municipio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Municipio $municipio)
    {
        return view('municipios.edit', compact('municipio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMunicipioRequest $request, Municipio $municipio)
    {
        try {
            DB::beginTransaction();

            // Obtener los datos validados del request
            $data = $request->validated();

            // Si el usuario que actualizó anteriormente es distinto al usuario actual, actualiza el campo
            if ($municipio->user_edit_id !== Auth::id()) {
                $data['user_edit_id'] = Auth::id();
            }

            // Actualizar el parámetro con los nuevos datos
            $municipio->update($data);

            DB::commit();
            return redirect()->route('municipio.show', $municipio)
                ->with('success', 'Municipio actualizado exitosamente');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al actualizar municipio: ' . $e->getMessage());
            if ($e->getCode() == 23000) {
                return redirect()->back()->withErrors(['error' => 'El nombre asignado al municipio ya existe.']);
            }
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al actualizar el municipio.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Municipio $municipio)
    {
        try {
            DB::beginTransaction();
            $municipio->delete();
            DB::commit();
            return redirect()->route('municipio.index')
                ->with('success', 'Municipio eliminado exitosamente');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al eliminar municipio: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al eliminar el municipio.']);
        }
    }

    public function cambiarEstado(Municipio $municipio)
    {
        try {
            DB::beginTransaction();
            $municipio->update([
                'status' => $municipio->status === 1 ? 0 : 1,
            ]);
            DB::commit();
            return redirect()->route('municipio.index')
                ->with('success', 'Municipio actualizado exitosamente');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al actualizar municipio: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al actualizar el municipio.']);
        }

    }

    public function getByDepartamento($departamentoId)
    {
        $municipios = $this->ubicacionService->obtenerMunicipiosPorDepartamento($departamentoId);
        return response()->json($municipios);
    }
}

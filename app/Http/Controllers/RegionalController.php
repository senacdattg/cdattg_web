<?php

namespace App\Http\Controllers;

use App\Models\Regional;
use App\Http\Requests\StoreRegionalRequest;
use App\Http\Requests\UpdateRegionalRequest;
use App\Models\Tema;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Departamento;

class RegionalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('can:VER REGIONAL')->only(['index', 'show']);
        $this->middleware('can:CREAR REGIONAL')->only(['create', 'store', 'edit', 'update']);
        $this->middleware('can:ELIMINAR REGIONAL')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departamentos = Departamento::all();
        $regionales = Regional::with('departamento')->paginate(10);

        return view('regional.index', compact('regionales', 'departamentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRegionalRequest $request)
    {
        // Los datos ya han sido validados en el Form Request
        $data = $request->validated();

        // Agregar los campos adicionales requeridos
        $data['user_create_id'] = Auth::id();
        $data['user_edit_id']   = Auth::id();
        $data['status']         = 1;

        try {
            DB::beginTransaction();

            $regional = Regional::create($data);

            DB::commit();
            return redirect()->route('regional.index')
                ->with('success', 'Regional creada con éxito');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al crear regional: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Error al momento de crear la regional']);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Regional $regional)
    {
        return view('regional.show', ['regional' => $regional]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Regional $regional)
    {

        return view('regional.edit', ['regional' => $regional]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRegionalRequest $request, Regional $regional)
    {
        // Obtener los datos validados
        $data = $request->validated();

        // Asegurar que se actualice el usuario que editó
        $data['user_edit_id'] = Auth::id();

        try {
            DB::transaction(function () use ($regional, $data) {
                $regional->update($data);
            });

            return redirect()->route('regional.show', $regional->id)->with('success', 'Regional actualizada con éxito');
        } catch (QueryException $e) {

            DB::rollBack();
            Log::error("Error al actualizar la regional ID {$regional->id}: " . $e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Error al momento de actualizar la regional: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Regional $regional)
    {
        try {
            DB::beginTransaction();
            $regional->delete();
            DB::commit();

            return redirect()->route('regional.index')->with('success', 'Regional eliminada exitosamente');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al eliminar regional (QueryException): ' . $e->getMessage());

            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'La regional se encuentra en uso en estos momentos, no se puede eliminar');
            }

            return redirect()->back()->with('error', 'Error al eliminar la regional: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al eliminar regional: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Ocurrió un error inesperado al eliminar la regional');
        }
    }

    public function cambiarEstadoRegional(Regional $regional)
    {
        try {
            $nuevoStatus = $regional->status === 1 ? 0 : 1;
            $regional->update([
                'status'       => $nuevoStatus,
                'user_edit_id' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Estado actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error("Error al cambiar el estado de la regional (ID: {$regional->id}): " . $e->getMessage());

            return redirect()->back()->with('error', 'No se pudo actualizar el estado');
        }
    }
}

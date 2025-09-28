<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistroActividadesRequest;
use App\Http\Requests\UpdateRegistroActividadesRequest;
use App\Services\RegistroActividadesServices;
use App\Models\InstructorFichaCaracterizacion;
use App\Models\ResultadosAprendizaje;
use App\Models\Evidencias;
use App\Models\EvidenciaGuiaAprendizaje;
use Illuminate\Support\Facades\Auth;

class RegistroActividadesController extends Controller
{

    protected $registroActividadesServices;

    public function __construct(RegistroActividadesServices $registroActividadesServices)
    {
        $this->registroActividadesServices = $registroActividadesServices;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(InstructorFichaCaracterizacion $caracterizacion)
    {
        $actividades = $this->registroActividadesServices->getActividades($caracterizacion);
        $guiaAprendizajeActual = $this->registroActividadesServices->getGuiasAprendizaje($caracterizacion);
        $rapActual = $caracterizacion->ficha->programaFormacion->competenciaActual()->rapActual();
        return view('registro_actividades.index', compact('caracterizacion', 'actividades', 'rapActual', 'guiaAprendizajeActual'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRegistroActividadesRequest $request, InstructorFichaCaracterizacion $caracterizacion)
    {
            $fecha_evidencia = $request->fecha_evidencia;
            $data = [
                'nombre' => $request->nombre,
                'fecha_evidencia' => $request->fecha_evidencia,
                'id_estado' => 25,
            ];

            $data['user_create_id'] = Auth::id();
            $data['user_edit_id'] = Auth::id();

            $this->registroActividadesServices->crearEvidencia($data, $caracterizacion);

            // Redirigir con mensaje de éxito
            return redirect()->route('registro-actividades.index', ['caracterizacion' => $caracterizacion])
                ->with('success', 'Registro de actividad creado exitosamente.');

    }

    /**
     * Display the specified resource.
     */
    public function show(RegistroActividades $registroActividades)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InstructorFichaCaracterizacion $caracterizacion, Evidencias $actividad)
    {
        $actividades = $this->registroActividadesServices->getActividades($caracterizacion);
        $rapActual = $caracterizacion->ficha->programaFormacion->competenciaActual()->rapActual();
        return view('registro_actividades.edit', compact('actividad', 'caracterizacion', 'actividades', 'rapActual'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRegistroActividadesRequest $request, InstructorFichaCaracterizacion $caracterizacion, Evidencias $actividad)
    {
        try {
            // Actualizar directamente la evidencia
            $actividad->update([
                'nombre' => $request->nombre,
                'fecha_evidencia' => $request->fecha_evidencia,
                'user_edit_id' => Auth::id(),
            ]);

            // Redirigir con mensaje de éxito
            return redirect()->route('registro-actividades.index', ['caracterizacion' => $caracterizacion])
                ->with('success', 'Registro de actividad actualizado exitosamente.');
        } catch (\Exception $e) {
            // Manejar errores y redirigir con mensaje de error
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error al actualizar el registro de actividad: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegistroActividades $registroActividades)
    {
        //
    }
}

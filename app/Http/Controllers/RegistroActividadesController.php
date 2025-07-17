<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistroActividadesRequest;
use App\Http\Requests\UpdateRegistroActividadesRequest;
use App\Models\RegistroActividades;
use App\Models\Caracterizacion;
use App\Models\InstructorFichaCaracterizacion;

class RegistroActividadesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InstructorFichaCaracterizacion $caracterizacion)
    {
        return view('registro_actividades.index', compact('caracterizacion'));
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
    public function store(StoreRegistroActividadesRequest $request)
    {
        //
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
    public function edit(RegistroActividades $registroActividades)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRegistroActividadesRequest $request, RegistroActividades $registroActividades)
    {
        try {
            // Obtener los datos validados del request
            $data = $request->validated();

            // Actualizar el registro con los nuevos datos
            $registroActividades->update($data);

            // Redirigir con mensaje de éxito
            return redirect()->route('registro-actividades.index', ['caracterizacion' => $registroActividades->caracterizacion_id])
                ->with('success', 'Registro de actividad actualizado exitosamente.');
        } catch (\Exception $e) {
            // Manejar errores y redirigir con mensaje de error
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error al actualizar el registro de actividad.');
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

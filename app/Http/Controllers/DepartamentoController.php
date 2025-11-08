<?php

namespace App\Http\Controllers;

use App\Services\UbicacionService;
use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
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
        //
    }

    /**
     * Carga departamentos activos
     */
    public function cargarDepartamentos()
    {
        $departamentos = \App\Models\Departamento::where('status', 1)->get();
        return response()->json(['success' => true, 'departamentos' => $departamentos], 200);
    }

    public function apiCargarDepartamentos()
    {
        $departamentos = \App\Models\Departamento::where('status', 1)->get();
        return response()->json($departamentos, 200);
    }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Departamento $departamento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Departamento $departamento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Departamento $departamento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Departamento $departamento)
    {
        //
    }

    public function getByPais($paisId)
    {
        $departamentos = $this->ubicacionService->obtenerDepartamentosPorPais($paisId);
        return response()->json([
            'success' => true,
            'data' => $departamentos,
        ]);
    }
}

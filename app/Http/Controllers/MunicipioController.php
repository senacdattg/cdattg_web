<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Http\Requests\StoreMunicipioRequest;
use App\Http\Requests\UpdateMunicipioRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MunicipioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departamento_id = Auth::user()->persona->departamento_id;
        
        $municipios = Municipio::where('departamento_id', $departamento_id)->paginate(10);
        return view('municipios.index', compact('municipios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function cargarMunicipios($departamento_id)
    {
        // DB::enableQueryLog();
        $municipios = Municipio::where('departamento_id', $departamento_id)
            ->where('status', 1)->get();
        return response()->json(['success' => true, 'municipios' => $municipios]);
        // dd(DB::getQueryLog());
    }
    public function apiCargarMunicipios(Request $request)
    {

        // return response()->json(["message" => True]);
        $departamento_id = $request->departamento_id;
        // DB::enableQueryLog();
        $municipios = Municipio::where('departamento_id', $departamento_id)
            ->where('status', 1)->get();
        return response()->json($municipios, 200);
        // dd(DB::getQueryLog());
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
    public function show(Municipio $municipio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Municipio $municipio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Municipio $municipio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Municipio $municipio)
    {
        //
    }

    public function cambiarEstado(Municipio $municipio)
    {

    }

    public function getByDepartamento($departamentoId)
    {
        $municipios = Municipio::where('departamento_id', $departamentoId)
            ->where('status', 1)
            ->get();
        return response()->json($municipios);
    }
}

<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\Orden;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ParametroTema;

class OrdenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ordenes = Orden::with(['tipoOrden'])->get();
        return view('inventario.ordenes,index', compact('ordenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiposOrdenes = ParametroTema::with(['parametro','tema'])
        ->whereHas('tema', fn($q) => $q->where('name', 'TIPOS DE ORDEN'))
        ->where('status', 1)
        ->get();

        return view('inventario.ordenes.create', compact('tiposOrdenes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion_orden' => 'required|string',
            'tipo_orden_id' => 'required|exists:parametros_temas_id'
        ]);

        $validated['user_create_id'] = Auth::id();
        $validated['user_update_id'] = Auth::id();

        Orden::create($validated);

        return redirect()->route('ordenes.create')->with('success', 'Orden creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orden = Orden::with(['tipoOrden'])->findOrFail($id);
        return view('inventario.ordenes.show', compact('orden'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $orden = Orden::findOrFail($id);
        return view('invetario.ordenes.edit', compact('orden'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

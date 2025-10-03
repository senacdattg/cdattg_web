<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\ContratoConvenio;
use App\Models\Inventario\Proveedor;
use App\Models\ParametroTema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContratoConvenioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $contratosConvenios = ContratoConvenio::with(['proveedor', 'estado'])->get();
        return view('inventario.contratos-convenios.index', compact('contratosConvenios'));
    }

    public function create()
    {
        $proveedores = Proveedor::all();

        $estados = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS DE CONTRATO/CONVENIO'))
            ->where('status', 1)
            ->get();

        return view('inventario.contratos-convenios.create', compact('proveedores', 'estados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:contratos_convenios,name',
            'codigo' => 'nullable|unique:contratos_convenios,codigo',
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'estado_id' => 'required|exists:parametros_temas,id'
        ]);
        
        $validated['user_create_id'] = Auth::id();
        $validated['user_update_id'] = Auth::id();

        ContratoConvenio::create($validated);

        return redirect()->route('contratos-convenios.index')
            ->with('success', 'Contrato/Convenio creado exitosamente.');
    }

    public function show(ContratoConvenio $contratoConvenio)
    {
        return view('inventario.contratos-convenios.show', compact('contratoConvenio'));
    }

    public function edit(string $id)
    {
        $contratoConvenio = ContratoConvenio::with(['proveedor', 'estado'])->findOrFail($id);

        $proveedores = Proveedor::all();

        $estados = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS DE CONTRATO/CONVENIO'))
            ->where('status', 1)
            ->get();

        return view('inventario.contratos-convenios.edit', compact('contratoConvenio', 'proveedores', 'estados'));
    }

    public function update(Request $request, string $id)
    {
        $contratoConvenio = ContratoConvenio::findOrFail($id);

        // Validar los datos
        $validated = $request->validate([
            'name' => 'required|unique:contratos_convenios,name,' . $id,
            'codigo' => 'nullable|unique:contratos_convenios,codigo,' . $id,
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'estado_id' => 'required|exists:parametros_temas,id'
        ]);

        $validated['user_update_id'] = Auth::id();

        $contratoConvenio->update($validated);

        return redirect()->route('contratos-convenios.index')
            ->with('success', 'Contrato/Convenio actualizado exitosamente.');
    }

    public function destroy(ContratoConvenio $contratoConvenio)
    {
        try {
            $contratoConvenio->delete();
            return redirect()->route('contratos-convenios.index')
                ->with('success', 'Contrato/Convenio eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar el Contrato/Convenio porque está en uso.');
        }
    }
}
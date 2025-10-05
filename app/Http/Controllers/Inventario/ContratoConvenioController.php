<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\ContratoConvenio;
use App\Models\Inventario\Proveedor;
use App\Models\ParametroTema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContratoConvenioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $contratosConvenios = ContratoConvenio::with(['proveedor', 'estado'])->latest()->get();
        return view('inventario.contratos_convenios.index', compact('contratosConvenios'));
    }

    public function create()
    {
        $proveedores = Proveedor::orderBy('proveedor')->get();
        $estados = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS DE CONTRATO/CONVENIO'))
            ->where('status', 1)
            ->orderBy('name');

        return view('inventario.contratos_convenios.create', compact('proveedores', 'estados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:contratos_convenios,nombre',
            'tipo' => 'required|in:Contrato,Convenio',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'vigencia' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'archivo' => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp'
        ]);

        if ($request->hasFile('archivo')) {
            $validated['archivo'] = $request->file('archivo')->store('contratos_convenios', 'public');
        }

        $validated['user_create_id'] = Auth::id();
        $validated['user_update_id'] = Auth::id();

        $contrato = ContratoConvenio::create($validated);

        return redirect()->route('inventario.contratos-convenios.show', $contrato)
            ->with('success', 'Contrato/Convenio creado exitosamente.');
    }

    public function show(ContratoConvenio $contratoConvenio)
    {
        return view('inventario.contratos_convenios.show', compact('contratoConvenio'));
    }

    public function edit(ContratoConvenio $contratoConvenio)
    {
        $proveedores = Proveedor::orderBy('proveedor')->get();
        $estados = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS DE CONTRATO/CONVENIO'))
            ->where('status', 1)
            ->orderBy('name');

        return view('inventario.contratos_convenios.edit', compact('contratoConvenio', 'proveedores', 'estados'));
    }

    public function update(Request $request, ContratoConvenio $contratoConvenio)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:contratos_convenios,nombre,' . $contratoConvenio->id,
            'tipo' => 'required|in:Contrato,Convenio',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'vigencia' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'archivo' => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp'
        ]);

        if ($request->hasFile('archivo')) {
            if ($contratoConvenio->archivo) {
                Storage::disk('public')->delete($contratoConvenio->archivo);
            }
            $validated['archivo'] = $request->file('archivo')->store('contratos_convenios', 'public');
        }

        $validated['user_update_id'] = Auth::id();

        $contratoConvenio->update($validated);

        return redirect()->route('inventario.contratos-convenios.show', $contratoConvenio)
            ->with('success', 'Contrato/Convenio actualizado exitosamente.');
    }

    public function destroy(ContratoConvenio $contratoConvenio)
    {
        try {
            if ($contratoConvenio->archivo) {
                Storage::disk('public')->delete($contratoConvenio->archivo);
            }
            $contratoConvenio->delete();
            return redirect()->route('inventario.contratos-convenios.index')
                ->with('success', 'Contrato/Convenio eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar el Contrato/Convenio porque está en uso.');
        }
    }
}
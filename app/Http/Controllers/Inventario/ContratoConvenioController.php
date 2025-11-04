<?php

namespace App\Http\Controllers\Inventario;

use App\Models\Inventario\ContratoConvenio;
use App\Models\Inventario\Proveedor;
use App\Models\ParametroTema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContratoConvenioController extends InventarioController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:VER CONTRATO')->only('index', 'show');
        $this->middleware('can:CREAR CONTRATO')->only('create', 'store');
        $this->middleware('can:EDITAR CONTRATO')->only('edit', 'update');
        $this->middleware('can:ELIMINAR PRODUCTO')->only('destroy');
    }

    public function index()
    {
        $contratosConvenios = ContratoConvenio::with([
            'proveedor', 
            'estado.parametro', 
            'userCreate.persona', 
            'userUpdate.persona'
        ])->latest()->paginate(10);
        
        $estados = ParametroTema::with(['parametro', 'tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS'))
            ->where('parametros_temas.status', 1)
            ->get();
        
        return view('inventario.contratos_convenios.index', compact('contratosConvenios', 'estados'));
    }

    public function create()
    {
        $proveedores = Proveedor::orderBy('proveedor')->get();
        return view('inventario.contratos_convenios.create', compact('proveedores'));
    }

    public function show(ContratoConvenio $contratoConvenio)
    {
        $contratoConvenio->load([
            'proveedor',
            'productos',
            'estado.parametro',
            'userCreate.persona',
            'userUpdate.persona'
        ]);
        return view('inventario.contratos_convenios.show', compact('contratoConvenio'));
    }

    public function edit(ContratoConvenio $contratoConvenio)
    {
        $proveedores = Proveedor::orderBy('proveedor')->get();
        return view('inventario.contratos_convenios.edit', compact('contratoConvenio', 'proveedores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:contratos_convenios,name',
            'codigo' => 'nullable|string|max:50',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado_id' => 'required|exists:parametros_temas,id',
        ]);

        $contrato = new ContratoConvenio($validated);
        $this->setUserIds($contrato);
        $contrato->save();

        return redirect()->route('inventario.contratos-convenios.index')
            ->with('success', 'Contrato/Convenio creado exitosamente.');
    }

    public function update(Request $request, ContratoConvenio $contratoConvenio)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:contratos_convenios,name,' . $contratoConvenio->id,
            'codigo' => 'nullable|string|max:50',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado_id' => 'required|exists:parametros_temas,id',
        ]);

        $contratoConvenio->fill($validated);
        $this->setUserIds($contratoConvenio, true);
        $contratoConvenio->save();

        return redirect()->route('inventario.contratos-convenios.index')
            ->with('success', 'Contrato/Convenio actualizado exitosamente.');
    }

    public function destroy(ContratoConvenio $contratoConvenio)
    {
        try {
            $contratoConvenio->delete();
            return redirect()->route('inventario.contratos-convenios.index')
                ->with('success', 'Contrato/Convenio eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar el Contrato/Convenio porque está en uso.');
        }
    }
}
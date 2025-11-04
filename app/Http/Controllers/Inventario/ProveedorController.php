<?php

namespace App\Http\Controllers\Inventario;

use App\Models\Inventario\Proveedor;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Http\Request;

class ProveedorController extends InventarioController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:VER PROVEEDOR')->only('index', 'show');
        $this->middleware('can:CREAR PROVEEDOR')->only('create', 'store');
        $this->middleware('can:EDITAR PROVEEDOR')->only('edit', 'update');
        $this->middleware('can:ELIMINAR PROVEEDOR')->only('destroy');
    }

    public function index()
    {
        $proveedores = Proveedor::with([
                'userCreate.persona',
                'userUpdate.persona',
                'estado.parametro',
                'departamento',
                'municipio'
            ])
            ->withCount('contratosConvenios')
            ->latest()
            ->paginate(10);
        return view('inventario.proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        $departamentos = Departamento::orderBy('departamento')->get();
        $municipios = Municipio::with('departamento')->orderBy('municipio')->get();
        return view('inventario.proveedores.create', compact('departamentos', 'municipios'));
    }

    public function show(Proveedor $proveedor)
    {
        $proveedor->load([
            'contratosConvenios',
            'userCreate.persona',
            'userUpdate.persona',
            'estado.parametro',
            'departamento',
            'municipio'
        ]);
        return view('inventario.proveedores.show', compact('proveedor'));
    }

    public function edit(Proveedor $proveedor)
    {
        $departamentos = Departamento::orderBy('departamento')->get();
        $municipios = Municipio::with('departamento')->orderBy('municipio')->get();
        return view('inventario.proveedores.edit', compact('proveedor', 'departamentos', 'municipios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor' => 'required|unique:proveedores,proveedor',
            'nit' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:10',
            'direccion' => 'nullable|string|max:255',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'municipio_id' => 'nullable|exists:municipios,id',
            'contacto' => 'nullable|string|max:100',
            'estado_id' => 'nullable|exists:parametros_temas,id'
        ]);

        $proveedor = new Proveedor($validated);
        $this->setUserIds($proveedor);
        $proveedor->save();

        return redirect()->route('inventario.proveedores.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    public function update(Request $request, string $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $validated = $request->validate([
            'proveedor' => 'required|unique:proveedores,proveedor,' . $proveedor->id,
            'nit' => 'nullable|string|max:50|unique:proveedores,nit,' . $proveedor->id,
            'email' => 'nullable|email|max:255|unique:proveedores,email,' . $proveedor->id,
            'telefono' => 'nullable|string|max:10',
            'direccion' => 'nullable|string|max:255',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'municipio_id' => 'nullable|exists:municipios,id',
            'contacto' => 'nullable|string|max:100',
            'estado_id' => 'nullable|exists:parametros_temas,id'
        ]);

        $proveedor->fill($validated);
        $this->setUserIds($proveedor, true);
        $proveedor->save();

        return redirect()->route('inventario.proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        try {
            $proveedor->delete();
            return redirect()->route('inventario.proveedores.index')
                ->with('success', 'Proveedor eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar el proveedor porque está en uso.');
        }
    }

    /**
     * Obtener municipios por departamento (API)
     */
    public function getMunicipiosPorDepartamento($departamentoId)
    {
        $municipios = Municipio::where('departamento_id', $departamentoId)
            ->orderBy('municipio')
            ->get()
            ->map(function($municipio) {
                return [
                    'id' => $municipio->id,
                    'municipio' => $municipio->municipio,
                    'departamento' => $municipio->departamento->departamento ?? ''
                ];
            });

        return response()->json($municipios);
    }
}
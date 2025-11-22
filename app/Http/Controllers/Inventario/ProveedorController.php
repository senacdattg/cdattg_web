<?php

namespace App\Http\Controllers\Inventario;

use App\Models\Inventario\Proveedor;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Http\Requests\Inventario\ProveedorRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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

    public function index(Request $request) : View
    {
        $search = $request->input('search');

        $proveedoresQuery = Proveedor::with([
                'userCreate.persona',
                'userUpdate.persona',
                'estado.parametro',
                'departamento',
                'municipio'
            ])
            ->withCount('contratosConvenios')
            ->latest();

        if (!empty($search)) {
            $proveedoresQuery->where(function ($query) use ($search) {
                $query->where('proveedor', 'LIKE', "%{$search}%")
                    ->orWhere('nit', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('telefono', 'LIKE', "%{$search}%")
                    ->orWhere('contacto', 'LIKE', "%{$search}%")
                    ->orWhereHas('departamento', function ($departamentoQuery) use ($search) {
                        $departamentoQuery->where('departamento', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('municipio', function ($municipioQuery) use ($search) {
                        $municipioQuery->where('municipio', 'LIKE', "%{$search}%");
                    });
            });
        }

        $proveedores = $proveedoresQuery
            ->paginate(10)
            ->appends($request->only('search'));

        $proveedores->withPath(route('inventario.proveedores.index'));

        return view('inventario.proveedores.index', compact('proveedores'));
    }

    public function create() : View
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

    public function edit(Proveedor $proveedor) : View
    {
        $departamentos = Departamento::orderBy('departamento')->get();
        $municipios = Municipio::with('departamento')->orderBy('municipio')->get();
        return view('inventario.proveedores.edit', compact('proveedor', 'departamentos', 'municipios'));
    }

    public function store(ProveedorRequest $request) : RedirectResponse
    {
        $validated = $request->validated();

        $proveedor = new Proveedor($validated);
        $this->setUserIds($proveedor);
        $proveedor->save();

        return redirect()->route('inventario.proveedores.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    public function update(ProveedorRequest $request, string $id) : RedirectResponse
    {
        $proveedor = Proveedor::findOrFail($id);

        $validated = $request->validated();

        $proveedor->fill($validated);
        $this->setUserIds($proveedor, true);
        $proveedor->save();

        return redirect()->route('inventario.proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Proveedor $proveedor) : RedirectResponse
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
    public function getMunicipiosPorDepartamento($departamentoId) : JsonResponse
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
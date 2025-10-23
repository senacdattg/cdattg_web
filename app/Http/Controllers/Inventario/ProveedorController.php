<?php

namespace App\Http\Controllers\Inventario;

use App\Models\Inventario\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends InventarioController
{

    public function index()
    {
        $proveedores = Proveedor::with(['userCreate.persona', 'userUpdate.persona'])
            ->withCount('contratosConvenios')
            ->latest()
            ->get();
        return view('inventario.proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('inventario.proveedores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor' => 'required|unique:proveedores,proveedor'
        ]);

        $proveedor = new Proveedor($validated);
        $this->setUserIds($proveedor);
        $proveedor->save();

        return redirect()->route('inventario.proveedores.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    public function show(Proveedor $proveedor)
    {
        return view('inventario.proveedores.show', compact('proveedor'));
    }

    public function edit(Proveedor $proveedor)
    {
        return view('inventario.proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, string $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $validated = $request->validate([
            'proveedor' => 'required|unique:proveedores,proveedor,' . $proveedor->id,
            'nit' => 'nullable|unique:proveedores,nit,' . $proveedor->id,
            'email' => 'nullable|email|unique:proveedores,email,' . $proveedor->id
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
}
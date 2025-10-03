<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProveedorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $proveedores = Proveedor::all();
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
        
        $validated['user_create_id'] = Auth::id();
        $validated['user_update_id'] = Auth::id();

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')
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

        $validated['user_update_id'] = Auth::id();

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        try {
            $proveedor->delete();
            return redirect()->route('proveedores.index')
                ->with('success', 'Proveedor eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar el proveedor porque está en uso.');
        }
    }
}
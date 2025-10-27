<?php

namespace App\Http\Controllers\Inventario;

use App\Models\Inventario\Marca;
use Illuminate\Http\Request;

class MarcaController extends InventarioController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:VER PRODUCTO')->only('index');
        $this->middleware('can:CREAR PRODUCTO')->only('store');
        $this->middleware('can:EDITAR PRODUCTO')->only('update');
        $this->middleware('can:ELIMINAR PRODUCTO')->only('destroy');
    }

    public function index()
    {
        $marcas = Marca::with(['userCreate.persona', 'userUpdate.persona'])
            ->withCount('productos')
            ->latest()
            ->get();
        return view('inventario.marcas.index', compact('marcas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:marcas,nombre'
        ]);

        $marca = new Marca($validated);
        $this->setUserIds($marca);
        $marca->save();

        return redirect()->route('inventario.marcas.index')
            ->with('success', 'Marca creada exitosamente.');
    }

    public function update(Request $request, string $id)
    {
        $marca = Marca::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|unique:marcas,nombre,' . $marca->id
        ]);

        $marca->fill($validated);
        $this->setUserIds($marca, true);
        $marca->save();

        return redirect()->route('inventario.marcas.index')
            ->with('success', 'Marca actualizada exitosamente.');
    }

    public function destroy(Marca $marca)
    {
        try {
            $marca->delete();
            return redirect()->route('inventario.marcas.index')
                ->with('success', 'Marca eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar la marca porque está en uso.');
        }
    }
}
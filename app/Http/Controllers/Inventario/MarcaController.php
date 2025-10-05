<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarcaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $marcas = Marca::with(['userCreate.persona', 'userUpdate.persona'])
            ->withCount('productos')
            ->latest()
            ->get();
        return view('inventario.marcas.index', compact('marcas'));
    }

    public function create()
    {
        return view('inventario.marcas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:marcas,nombre'
        ]);
        
        $validated['user_create_id'] = Auth::id();
        $validated['user_update_id'] = Auth::id();

        Marca::create($validated);

        return redirect()->route('inventario.marcas.index')
            ->with('success', 'Marca creada exitosamente.');
    }

    public function show(Marca $marca)
    {
        return view('inventario.marcas.show', compact('marca'));
    }

    public function edit(Marca $marca)
    {
        return view('inventario.marcas.edit', compact('marca'));
    }

    public function update(Request $request, string $id)
    {
        $marca = Marca::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|unique:marcas,nombre,' . $marca->id
        ]);

        $validated['user_update_id'] = Auth::id();

        $marca->update($validated);

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
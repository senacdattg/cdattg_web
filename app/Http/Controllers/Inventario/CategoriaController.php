<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categorias = Categoria::with(['userCreate.persona', 'userUpdate.persona'])
            ->withCount('productos')
            ->latest()
            ->get();
        return view('inventario.categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('inventario.categorias.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:categorias,nombre'
        ]);
        
        $validated['user_create_id'] = Auth::id();
        $validated['user_update_id'] = Auth::id();

        Categoria::create($validated);

        return redirect()->route('inventario.categorias.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function show(Categoria $categoria)
    {
        return view('inventario.categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria)
    {
        return view('inventario.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, string $id)
    {
        $categoria = Categoria::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|unique:categorias,nombre,' . $categoria->id
        ]);

        $validated['user_update_id'] = Auth::id();

        $categoria->update($validated);

        return redirect()->route('inventario.categorias.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Categoria $categoria)
    {
        try {
            $categoria->delete();
            return redirect()->route('inventario.categorias.index')
                ->with('success', 'Categoría eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar la categoría porque está en uso.');
        }
    }
}
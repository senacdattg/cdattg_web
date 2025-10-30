<?php

namespace App\Http\Controllers\Inventario;

use App\Models\Inventario\Marca;
use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\Tema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class MarcaController extends InventarioController
{
    protected $temaMarcas;

    public function __construct()
    {
        parent::__construct();

        $this->middleware('can:VER MARCA')->only('index', 'show');
        $this->middleware('can:CREAR MARCA')->only('create', 'store');
        $this->middleware('can:EDITAR MARCA')->only('edit', 'update');
        $this->middleware('can:ELIMINAR MARCA')->only('destroy');

        $this->temaMarcas = Tema::where('name', 'MARCAS')->first();
    }

    public function index()
    {
        if (!$this->temaMarcas) {
            return back()->with('error', 'No existe el tema "MARCAS" en la base de datos.');
        }

        $marcas = $this->temaMarcas->parametros()
            ->with(['userCreate.persona', 'userUpdate.persona'])
            ->wherePivot('status', 1)
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
            'nombre' => 'required|string|unique:parametros,name',
        ]);

        try {
            $marca = new Marca([
                'name'           => $validated['nombre'],
                'status'         => 1,
                'user_create_id' => Auth::id(),
                'user_edit_id'   => Auth::id(),
            ]);
            $marca->save();

            // Se asocia al tema "MARCAS"
            $marca->asociarATemaMarcas();

            return redirect()->route('inventario.marcas.index')
                ->with('success', 'Marca creada exitosamente.');
        } catch (QueryException $e) {
            return back()->with('error', 'Error al crear la marca: ' . $e->getMessage());
        }
    }

    public function edit(Parametro $marca)
    {
        return view('inventario.marcas.edit', [
            'title' => 'Editar marca',
            'icon' => 'fas fa-tag',
            'action' => route('inventario.marcas.update', $marca->id),
            'method' => 'PUT',
            'submitText' => 'Actualizar marca',
            'cancelRoute' => route('inventario.marcas.index'),
            'marca' => $marca
        ]);
    }


    public function update(Request $request, Parametro $marca)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:parametros,name,' . $marca->id,
        ]);

        $marca->update([
            'name'         => strtoupper($validated['nombre']),
            'user_edit_id' => Auth::id(),
        ]);

        return redirect()->route('inventario.marcas.index')
            ->with('success', 'Marca actualizada exitosamente.');
    }

    public function destroy(Parametro $marca)
    {
        try {
            // Desvincular del tema "MARCAS"
            ParametroTema::where('parametro_id', $marca->id)
                ->where('tema_id', $this->temaMarcas->id)
                ->delete();

            $marca->delete();

            return redirect()->route('inventario.marcas.index')
                ->with('success', 'Marca eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar la marca porque está en uso.');
        }
    }

    public function show(Parametro $marca)
    {
        $marca->load(['userCreate.persona', 'userUpdate.persona']);
        return view('inventario.marcas.show', [
            'title' => 'Detalle de la marca',
            'icon' => 'fas fa-eye',
            'marca' => $marca   
        ]);
    }

}

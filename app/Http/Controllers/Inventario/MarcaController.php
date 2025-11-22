<?php

namespace App\Http\Controllers\Inventario;

use App\Models\Inventario\Marca;
use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\Tema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Inventario\MarcaCategoriaRequest;

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

    public function index(Request $request) : View|RedirectResponse
    {
        if (!$this->temaMarcas) {
            return back()->with('error', 'No existe el tema "MARCAS" en la base de datos.');
        }

        $search = $request->input('search');

        $marcasQuery = $this->temaMarcas->parametros()
            ->with(['userCreate.persona', 'userUpdate.persona'])
            ->wherePivot('status', 1);

        if (!empty($search)) {
            $marcasQuery->where(function ($query) use ($search) {
                $query->where('parametros.name', 'LIKE', "%{$search}%");
            });
        }

        $marcas = $marcasQuery
            ->paginate(10)
            ->appends($request->only('search'));

        $marcas->withPath(route('inventario.marcas.index'));

        // Cargar conteo de productos manualmente para cada marca
        $marcas->each(function($marca) {
            $marca->productos_count = \App\Models\Inventario\Producto::where('marca_id', $marca->id)->count();
        });

        return view('inventario.marcas.index', compact('marcas'));
    }

    public function create() : View
    {
        return view('inventario.marcas.create');
    }


    public function store(MarcaCategoriaRequest $request) : RedirectResponse
    { 
        $validated = $request->validated();

        try {
            $marca = new Marca([
                'name'           => $validated['name'],
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

    public function edit(Parametro $marca) : View
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


    public function update(MarcaCategoriaRequest $request, Parametro $marca) : RedirectResponse
    {
        $validated = $request->validated();

        $marca->update([
            'name'         => strtoupper($validated['name']),
            'user_edit_id' => Auth::id(),
        ]);

        return redirect()->route('inventario.marcas.index')
            ->with('success', 'Marca actualizada exitosamente.');
    }

    public function destroy(Parametro $marca) : RedirectResponse
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

<?php

namespace App\Http\Controllers\Inventario;

use App\Models\Inventario\categoria;
use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\Tema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Inventario\CategoriaRequest;
use App\Http\Requests\Inventario\MarcaCategoriaRequest;

class CategoriaController extends InventarioController
{
    protected $temacategorias;

    public function __construct()
    {
        parent::__construct();

        $this->middleware('can:VER CATEGORIA')->only('index', 'show');
        $this->middleware('can:CREAR CATEGORIA')->only('create', 'store');
        $this->middleware('can:EDITAR CATEGORIA')->only('edit', 'update');
        $this->middleware('can:ELIMINAR CATEGORIA')->only('destroy');

        $this->temacategorias = Tema::where('name', 'CATEGORIAS')->first();
    }

    public function index(Request $request) : View|RedirectResponse
    {
        if (!$this->temacategorias) {
            return back()->with('error', 'No existe el tema "CATEGORIAS" en la base de datos.');
        }

        $search = $request->input('search');

        $categoriasQuery = $this->temacategorias->parametros()
            ->with(['userCreate.persona', 'userUpdate.persona'])
            ->wherePivot('status', 1);

        if (!empty($search)) {
            $categoriasQuery->where(function ($query) use ($search) {
                $query->where('parametros.name', 'LIKE', "%{$search}%");
            });
        }

        $categorias = $categoriasQuery
            ->paginate(10)
            ->appends($request->only('search'));

        $categorias->withPath(route('inventario.categorias.index'));

        // Cargar conteo de productos manualmente para cada categoría
        $categorias->each(function ($categoria) {
            $categoria->productos_count = \App\Models\Inventario\Producto::where(
                'categoria_id',
                $categoria->id
            )->count();
        });

        return view('inventario.categorias.index', compact('categorias'));
    }

    public function create() : View
    {
        return view('inventario.categorias.create');
    }


    public function store(MarcaCategoriaRequest $request) : RedirectResponse
    {
        $validated = $request->validated();

        try {
            $categoria = new categoria([
                'name'           => $validated['name'],
                'status'         => 1,
                'user_create_id' => Auth::id(),
                'user_edit_id'   => Auth::id(),
            ]);
            $categoria->save();

            // Se asocia al tema "CATEGORIAS"
            $categoria->asociarATemaCategorias();

            return redirect()->route('inventario.categorias.index')
                ->with('success', 'Categoria creada exitosamente.');
        } catch (QueryException $e) {
            return back()->with('error', 'Error al crear la categoria: ' . $e->getMessage());
        }
    }

    public function edit(Parametro $categoria) : View
    {
        return view('inventario.categorias.edit', [
            'title' => 'Editar categoria',
            'icon' => 'fas fa-tag',
            'action' => route('inventario.categorias.update', $categoria->id),
            'method' => 'PUT',
            'submitText' => 'Actualizar categoria',
            'cancelRoute' => route('inventario.categorias.index'),
            'categoria' => $categoria
        ]);
    }


    public function update(MarcaCategoriaRequest $request, Parametro $categoria) : RedirectResponse
    {
        $validated = $request->validated();

        $categoria->update([
            'name'         => strtoupper($validated['name']),
            'user_edit_id' => Auth::id(),
        ]);

        return redirect()->route('inventario.categorias.index')
            ->with('success', 'categoria actualizada exitosamente.');
    }

    public function destroy(Parametro $categoria) : RedirectResponse
    {
        try {
            // Desvincular del tema "CATEGORIAS"
            ParametroTema::where('parametro_id', $categoria->id)
                ->where('tema_id', $this->temacategorias->id)
                ->delete();

            $categoria->delete();

            return redirect()->route('inventario.categorias.index')
                ->with('success', 'categoria eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar la categoria porque está en uso.');
        }
    }

    public function show(Parametro $categoria) : View
    {
        $categoria->load(['userCreate.persona', 'userUpdate.persona']);
        return view('inventario.categorias.show', [
            'title' => 'Detalle de la categoria',
            'icon' => 'fas fa-eye',
            'categoria' => $categoria
        ]);
    }

}

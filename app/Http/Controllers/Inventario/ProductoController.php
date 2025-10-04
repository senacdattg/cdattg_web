<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\Producto;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ParametroTema;
use App\Models\Inventario\Categoria;
use App\Models\Inventario\Marca;
use App\Models\Inventario\ContratoConvenio;
use App\Models\Ambiente;


class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Producto::with(['tipoProducto', 'unidadMedida', 'estado'])->get();
        return view('inventario.productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        $tiposProductos = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'TIPOS DE PRODUCTO'))
            ->where('status', 1)
            ->get();

        $unidadesMedida = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'UNIDADES DE MEDIDA'))
            ->where('status', 1)
            ->get();

        $estados = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS DE PRODUCTO'))
            ->where('status', 1)
            ->get();
        
        $categorias = Categoria::all();

        $marcas = Marca::all();

        $contratosConvenios = ContratoConvenio::all();

        $ambientes = Ambiente::all();

        return view('inventario.productos.create', compact('tiposProductos', 'unidadesMedida', 'estados', 'categorias', 'marcas', 'contratosConvenios', 'ambientes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'producto' => 'required|unique:productos',
            'tipo_producto_id' => 'required|exists:parametros_temas,id',
            'descripcion' => 'required|string',
            'peso' => 'required|numeric|min:0',
            'unidad_medida_id' => 'required|exists:parametros_temas,id',
            'cantidad' => 'required|integer|min:0',
            'codigo_barras' => 'required|string',
            'estado_producto_id' => 'required|exists:parametros_temas,id',
            'categoria_id' => 'required|exists:categorias,id',
            'marca_id' => 'required|exists:marcas,id',
            'contrato_convenio_id' => 'required|exists:contratos_convenios,id',
            'ambiente_id' => 'required|exists:ambientes,id',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png'
        ]);

        if ($request->hasFile('imagen')){
            $nombreArchivo = time() . '.' . $request->imagen->extension();
            $request->imagen->move(public_path('img/inventario'), $nombreArchivo);
            $validated['imagen'] = 'img/inventario/' . $nombreArchivo;
        }   

        $validated['user_create_id'] = Auth::id();
        $validated['user_update_id'] = Auth::id();

        Producto::create($validated);

        return redirect()->route('productos.create')->with('success', 'Producto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $producto = Producto::with(['tipoProducto', 'unidadMedida', 'estado', 'categoria', 'marca', 'contratoConvenio', 'ambiente'])->findOrFail($id);
        return view('inventario.productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Obtener el producto con sus relaciones
        $producto = Producto::with(['tipoProducto', 'unidadMedida', 'estado'])->findOrFail($id);

        // Obtener tipos de productos
        $tiposProductos = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'TIPOS DE PRODUCTO'))
            ->where('status', 1)
            ->get();

        // Obtener unidades de medida
        $unidadesMedida = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'UNIDADES DE MEDIDA'))
            ->where('status', 1)
            ->get();

        // Obtener estados de producto
        $estados = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS DE PRODUCTO'))
            ->where('status', 1)
            ->get();

        $categorias = Categoria::all();

        $marcas = Marca::all();

        $contratosConvenios = ContratoConvenio::all();

        $ambientes = Ambiente::all();
    
        return view('inventario.productos.edit', compact(
            'producto',
            'tiposProductos',
            'unidadesMedida',
            'estados',
            'categorias',
            'marcas'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Buscar el producto
        $producto = Producto::findOrFail($id);

        // Validar los datos
        $validated = $request->validate([
            'producto' => 'required|unique:productos,producto,' . $id,
            'tipo_producto_id' => 'required|exists:parametros_temas,id',
            'descripcion' => 'required|string',
            'peso' => 'required|numeric|min:0',
            'unidad_medida_id' => 'required|exists:parametros_temas,id',
            'cantidad' => 'required|integer|min:0',
            'codigo_barras' => 'required|string',
            'estado_producto_id' => 'required|exists:parametros_temas,id',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png'
        ]);

        // Manejar la imagen si se sube una nueva
        if ($request->hasFile('imagen')) {
            if ($producto->imagen && file_exists(public_path($producto->imagen))) {
                unlink(public_path($producto->imagen));
            }
            
            // Guardar la nueva imagen
            $nombreArchivo = time() . '.' . $request->imagen->extension();
            $request->imagen->move(public_path('img/inventario'), $nombreArchivo);
            $validated['imagen'] = 'img/inventario/' . $nombreArchivo;
        }

        // Actualizar el usuario que modifica
        $validated['user_update_id'] = Auth::id();

        // Actualizar el producto
        $producto->update($validated);

        // Redireccionar con mensaje de éxito
        return redirect()->route('productos.show', $producto->id)
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $producto = Producto::findOrFail($id);
        
        // Eliminar la imagen si existe
        if ($producto->imagen && file_exists(public_path($producto->imagen))) {
            unlink(public_path($producto->imagen));
        }
        
        $producto->delete();
        
        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente');
    }
  

}

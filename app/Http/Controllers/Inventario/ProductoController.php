<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\Producto;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ParametroTema;



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
            ->whereHas('tema', fn($q) => $q->where('name', 'TIPO DE PRODUCTO'))
            ->where('status', 1)
            ->get();

        $unidadesMedida = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'UNIDAD DE MEDIDA'))
            ->where('status', 1)
            ->get();

        $estados = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS'))
            ->where('status', 1)
            ->get();

        return view('inventario.productos.create', compact('tiposProductos', 'unidadesMedida', 'estados'));
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
            'unidad_medida_id' => 'required|exists:parametros_temas,id',
            'estado_id' => 'required|exists:parametros_temas,id',
        ]);

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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $producto = Producto::findOrFail($id);
        return view('inventario.productos.edit', compact('producto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\ParametroTema;
use App\Models\Parametro;
use App\Models\Tema;
use App\Models\Inventario\ContratoConvenio;
use App\Models\Ambiente;
use App\Models\Inventario\Proveedor;
use App\Notifications\StockBajoNotification;


class ProductoController extends InventarioController
{
    private const THEME_PRODUCT_STATES = 'ESTADOS DE PRODUCTO';
    private const RULE_REQUIRED_PARAMETRO_TEMA = 'required|exists:parametros_temas,id';
    private const RULE_REQUIRED_PARAMETRO = 'required|exists:parametros,id';
    private const DEFAULT_PRODUCT_IMAGE = 'img/inventario/producto-default.png';


    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        
        // Middlewares de permisos de inventario
        $this->middleware('can:VER PRODUCTO')->only(['index', 'show']);
        $this->middleware('can:VER CATALOGO PRODUCTO')->only(['catalogo']);
        $this->middleware('can:BUSCAR PRODUCTO')->only(['buscar']);
        $this->middleware('can:CREAR PRODUCTO')->only(['create', 'store']);
        $this->middleware('can:EDITAR PRODUCTO')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR PRODUCTO')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Producto::with([
            'tipoProducto.parametro',
            'unidadMedida.parametro',
            'estado.parametro',
            'contratoConvenio',
            'ambiente',
            'proveedor'
        ]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('producto', 'LIKE', "%{$search}%")
                  ->orWhere('codigo_barras', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion', 'LIKE', "%{$search}%");
            });
        }

        $productos = $query
            ->paginate(10)
            ->appends($request->only('search'));

        $productos->withPath(route('inventario.productos.index'));

        // Cargar marca y categoria directamente para cada producto
        $productos->each(function($producto) {
            if ($producto->marca_id) {
                $producto->marca = Parametro::find($producto->marca_id);
            }
            if ($producto->categoria_id) {
                $producto->categoria = Parametro::find($producto->categoria_id);
            }
        });
        
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
            ->whereHas('tema', fn($q) => $q->where('name', self::THEME_PRODUCT_STATES))
            ->where('status', 1)
            ->get();

        $categorias = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'CATEGORIAS'))
            ->where('status', 1)
            ->get();

        $marcas = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'MARCAS'))
            ->where('status', 1)
            ->get();
        
        $contratosConvenios = ContratoConvenio::all();

        $ambientes = Ambiente::all();

        $proveedores = Proveedor::all();

        return view('inventario.productos.create', compact('tiposProductos', 'unidadesMedida', 'estados', 'categorias', 'marcas', 'contratosConvenios', 'ambientes', 'proveedores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'producto' => 'required|unique:productos',
            'tipo_producto_id' => self::RULE_REQUIRED_PARAMETRO_TEMA,
            'descripcion' => 'required|string',
            'peso' => 'required|numeric|min:0',
            'unidad_medida_id' => self::RULE_REQUIRED_PARAMETRO_TEMA,
            'cantidad' => 'required|integer|min:1',
            'codigo_barras' => ['nullable','string'],
            'estado_producto_id' => self::RULE_REQUIRED_PARAMETRO_TEMA,
            'categoria_id' => self::RULE_REQUIRED_PARAMETRO,
            'marca_id' => self::RULE_REQUIRED_PARAMETRO,
            'contrato_convenio_id' => 'required|exists:contratos_convenios,id',
            'ambiente_id' => 'required|exists:ambientes,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_vencimiento' => 'nullable|date',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png| max:2048'
        ]);

        if ($request->hasFile('imagen')) {
            $nombreArchivo = time() . '.' . $request->imagen->extension();
            $request->imagen->move(public_path('img/inventario'), $nombreArchivo);
            $validated['imagen'] = 'img/inventario/' . $nombreArchivo;
        } else {
            // Si no se sube imagen, se subirá la imagen por defecto
            $validated['imagen'] = self::DEFAULT_PRODUCT_IMAGE;
        }

        // Normalizar/autoasignar código de barras (11 dígitos incrementales si no llega uno válido)
        $validated['codigo_barras'] = $this->resolveBarcodeForCreate($request->input('codigo_barras'));

        $validated['user_create_id'] = Auth::id();
        $validated['user_update_id'] = Auth::id();

        Producto::create($validated);

        return redirect()->route('inventario.productos.index')->with('success', 'Producto creado correctamente.');
    }

    private function resolveBarcodeForCreate(?string $raw): string
    {
        $digits = preg_replace('/\D/', '', (string) $raw);
        if (strlen($digits) === 11) {
            return $digits;
        }
        return $this->generateNextBarcode();
    }

    private function generateNextBarcode(): string
    {
        return DB::transaction(function () {
            $max = DB::table('productos')->whereNotNull('codigo_barras')->max('codigo_barras');
            $onlyDigits = preg_replace('/\D/', '', (string) $max);
            $num = $onlyDigits === '' ? 0 : (int) $onlyDigits;
            $next = $num + 1;
            $code = str_pad((string)$next, 11, '0', STR_PAD_LEFT);

            // Asegurar no colisiona (reintento simple)
            for ($i = 0; $i < 3; $i++) {
                $exists = DB::table('productos')->where('codigo_barras', $code)->exists();
                if (!$exists) {
                    return $code;
                }
                $code = str_pad((string)($next + $i + 1), 11, '0', STR_PAD_LEFT);
            }
            return $code;
        }, 3);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $producto = Producto::with([
            'tipoProducto.parametro',
            'unidadMedida.parametro',
            'estado.parametro',
            'categoria',
            'marca',
            'contratoConvenio',
            'ambiente',
            'proveedor',
        ])->findOrFail($id);
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

        // Obtener categorías
        $categorias = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'CATEGORIAS'))
            ->where('status', 1)
            ->get();

        // Obtener marcas
        $marcas = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'MARCAS'))
            ->where('status', 1)
            ->get();

        $contratosConvenios = ContratoConvenio::all();

        $ambientes = Ambiente::all();
    
        return view('inventario.productos.edit', compact(
            'producto',
            'tiposProductos',
            'unidadesMedida',
            'estados',
            'categorias',
            'marcas',
            'contratosConvenios',
            'ambientes'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $producto = Producto::findOrFail($id);
        $validated = $this->validateUpdateRequest($request, $id);
        $this->processImageForUpdate($request, $producto, $validated);
        $this->normalizeBarcodeForUpdate($request, $validated);
        $validated['user_update_id'] = Auth::id();

        $cantidadAnterior = $producto->cantidad;
        $producto->update($validated);

        $this->notifyIfStockLow($producto, $cantidadAnterior);

        // Redireccionar con mensaje de éxito
        return redirect()->route('inventario.productos.show', $producto->id)
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $producto = Producto::findOrFail($id);
        
        // Se elimina la imagen si no es la de defecto
        if ($producto->imagen && 
            $producto->imagen !== self::DEFAULT_PRODUCT_IMAGE && 
            file_exists(public_path($producto->imagen))) {
            unlink(public_path($producto->imagen));
        }
        
        $producto->delete();
        
        return redirect()->route('inventario.productos.index')
            ->with('success', 'Producto eliminado correctamente');
    }
    
    public function buscarPorCodigo($codigo)
    {
        $producto = Producto::where('codigo_barras', $codigo)->first();

        if ($producto) {
            return response()->json($producto);
        } else {
            return response()->json(null, 404);
        }
    }

    /**
     * Mostrar catálogo de productos estilo ecommerce
     */
    public function catalogo()
    {
        // Obtener el ParametroTema del estado "AGOTADO"
        // Primero obtener el Parámetro con ID 43 (AGOTADO)
        $parametroAgotado = Parametro::find(43);

        $query = Producto::with([
            'tipoProducto.parametro',
            'unidadMedida.parametro',
            'estado.parametro',
            'contratoConvenio',
            'ambiente'
        ])
        ->where('cantidad', '>', 0) // Solo productos con stock
        ->orderBy('producto', 'asc');

        // Excluir productos con estado "AGOTADO"
        if ($parametroAgotado) {
            // Buscar el ParametroTema para este parámetro
            $estadoAgotadoTema = ParametroTema::where('parametro_id', 43)
                ->whereHas('tema', function($query) {
                    $query->where('name', self::THEME_PRODUCT_STATES);
                })
                ->first();
            
            if ($estadoAgotadoTema) {
                $query->where('estado_producto_id', '!=', $estadoAgotadoTema->id);
            }
        }

        $productos = $query->paginate(12);

        // Cargar marca y categoria directamente para cada producto
        $productos->each(function($producto) {
            if ($producto->marca_id) {
                $producto->marca = Parametro::find($producto->marca_id);
            }
            if ($producto->categoria_id) {
                $producto->categoria = Parametro::find($producto->categoria_id);
            }
        });

        // Obtener categorías desde ParametroTema
        $temaCategorias = Tema::where('name', 'CATEGORIAS')->first();
        $categorias = $temaCategorias ? $temaCategorias->parametros()->wherePivot('status', 1)->get() : collect();

        // Obtener marcas desde ParametroTema
        $temaMarcas = Tema::where('name', 'MARCAS')->first();
        $marcas = $temaMarcas ? $temaMarcas->parametros()->wherePivot('status', 1)->get() : collect();

        return view('inventario.productos.card', compact('productos', 'categorias', 'marcas'));
    }

    /**
     * Buscar productos por término de búsqueda (AJAX)
     */
    public function buscar(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $brandId = $request->input('brand_id');

        $query = Producto::with([
            'tipoProducto.parametro',
            'unidadMedida.parametro',
            'estado.parametro',
            'contratoConvenio',
            'ambiente'
        ])
        ->where('cantidad', '>', 0)
        ->orderBy('producto', 'asc');

        // Excluir productos con estado "AGOTADO"
        $parametroAgotado = Parametro::find(43);
        if ($parametroAgotado) {
            $estadoAgotadoTema = ParametroTema::where('parametro_id', 43)
                ->whereHas('tema', function($query) {
                    $query->where('name', self::THEME_PRODUCT_STATES);
                })
                ->first();

            if ($estadoAgotadoTema) {
                $query->where('estado_producto_id', '!=', $estadoAgotadoTema->id);
            }
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('producto', 'LIKE', "%{$search}%")
                  ->orWhere('codigo_barras', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion', 'LIKE', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('categoria_id', $categoryId);
        }

        if ($brandId) {
            $query->where('marca_id', $brandId);
        }

        $productos = $query->get();

        $productos->each(function($producto) {
            if ($producto->marca_id) {
                $producto->marca = Parametro::find($producto->marca_id);
            }
            if ($producto->categoria_id) {
                $producto->categoria = Parametro::find($producto->categoria_id);
            }
            $producto->imagen_url = $producto->imagen ? asset($producto->imagen) : null;
        });

        return response()->json([
            'success' => true,
            'productos' => $productos
        ]);
    }

    private function validateUpdateRequest(Request $request, string $id): array
    {
        return $request->validate([
            'producto' => 'required|unique:productos,producto,' . $id,
            'tipo_producto_id' => self::RULE_REQUIRED_PARAMETRO_TEMA,
            'descripcion' => 'required|string',
            'peso' => 'required|numeric|min:0',
            'unidad_medida_id' => self::RULE_REQUIRED_PARAMETRO_TEMA,
            'cantidad' => 'required|integer|min:0',
            'codigo_barras' => ['nullable', 'string'],
            'estado_producto_id' => self::RULE_REQUIRED_PARAMETRO_TEMA,
            'categoria_id' => self::RULE_REQUIRED_PARAMETRO,
            'marca_id' => self::RULE_REQUIRED_PARAMETRO,
            'contrato_convenio_id' => 'required|exists:contratos_convenios,id',
            'ambiente_id' => 'required|exists:ambientes,id',
            'fecha_vencimiento' => 'nullable|date',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png'
        ]);
    }

    private function processImageForUpdate(Request $request, Producto $producto, array &$validated): void
    {
        if (!$request->hasFile('imagen')) {
            return;
        }

        $this->deleteExistingImage($producto);
        $validated['imagen'] = $this->storeUploadedImage($request);
    }

    private function deleteExistingImage(Producto $producto): void
    {
        if (!$producto->imagen ||
            $producto->imagen === self::DEFAULT_PRODUCT_IMAGE ||
            !file_exists(public_path($producto->imagen))) {
            return;
        }

        unlink(public_path($producto->imagen));
    }

    private function storeUploadedImage(Request $request): string
    {
        $nombreArchivo = time() . '.' . $request->imagen->extension();
        $request->imagen->move(public_path('img/inventario'), $nombreArchivo);

        return 'img/inventario/' . $nombreArchivo;
    }

    private function normalizeBarcodeForUpdate(Request $request, array &$validated): void
    {
        if (!$request->has('codigo_barras')) {
            return;
        }

        $raw = $request->input('codigo_barras');

        if ($raw === null || $raw === '') {
            unset($validated['codigo_barras']);
            return;
        }

        $digits = preg_replace('/\D/', '', (string) $raw);

        $validated['codigo_barras'] = strlen($digits) === 11
            ? $digits
            : $this->generateNextBarcode();
    }

    private function notifyIfStockLow(Producto $producto, int $cantidadAnterior): void
    {
        if ($cantidadAnterior == $producto->cantidad || $producto->cantidad > 10) {
            return;
        }

        $superadmins = User::role('SUPER ADMINISTRADOR')->get();

        if ($superadmins->isEmpty()) {
            return;
        }

        foreach ($superadmins as $admin) {
            $admin->notify(new StockBajoNotification($producto, $producto->cantidad, 10));
        }
    }

    /**
     * Agregar producto al carrito (AJAX)
     */
    public function agregarAlCarrito(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        $producto = Producto::findOrFail($validated['producto_id']);

        // Verificar stock disponible
        if ($producto->cantidad < $validated['cantidad']) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente',
                'stock_disponible' => $producto->cantidad
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->producto,
                'stock' => $producto->cantidad
            ]
        ]);
    }

    /**
     * Obtener detalles del producto para modal 
     */
    public function detalles(string $id)
    {
        $producto = Producto::with([
            'tipoProducto.parametro',
            'unidadMedida.parametro',
            'estado.parametro',
            'contratoConvenio',
            'ambiente',
            'proveedor',
        ])->findOrFail($id);

        // Cargar marca y categoria DIRECTAMENTE desde Parametro sin usar la relación del modelo
        if ($producto->marca_id) {
            $producto->setRelation('marca', Parametro::find($producto->marca_id));
        }
        if ($producto->categoria_id) {
            $producto->setRelation('categoria', Parametro::find($producto->categoria_id));
        }

        return view('inventario.productos._detalles-modal', compact('producto'));
    }

    /**
     * Vista imprimible de la etiqueta con código de barras SENA (JS en cliente)
     */
    public function etiqueta(string $id)
    {
        $producto = Producto::findOrFail($id);
        return view('inventario.productos.etiqueta', compact('producto'));
    }
}

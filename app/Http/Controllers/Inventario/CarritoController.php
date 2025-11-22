<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\Inventario\CarritoRequest;

class CarritoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        
        // Middlewares de permisos de carrito
        $this->middleware('can:VER CARRITO')->only(['index']);
        $this->middleware('can:AGREGAR CARRITO')->only(['agregar', 'store']);
        $this->middleware('can:ACTUALIZAR CARRITO')->only(['actualizar', 'update']);
        $this->middleware('can:ELIMINAR CARRITO')->only(['eliminar', 'destroy']);
        $this->middleware('can:VACIAR CARRITO')->only(['vaciar']);
    }

    // Vista del carrito
    public function index() : View
    {
        return view('inventario.carrito.carrito');
    }

    // Agregar productos al carrito (crear orden)
    public function agregar(CarritoRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $availabilityResponse = $this->checkItemsAvailability($validated['items']);

            if ($availabilityResponse) {
                return $availabilityResponse;
            }

            // La creación de la orden se completa en el formulario de préstamos/salidas.
            return response()->json([
                'success' => true,
                'message' => 'Solicitud procesada correctamente',
                'orden_id' => null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    // Actualizar cantidad de un producto en el carrito
    
    public function actualizar(CarritoRequest $request, $id) : JsonResponse
    {
        $validated = $request->validated();

        try {
            $producto = Producto::findOrFail($id);

            if ($producto->cantidad < $validated['cantidad']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente',
                    'stock_disponible' => $producto->cantidad
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cantidad actualizada',
                'producto' => [
                    'id' => $producto->id,
                    'nombre' => $producto->producto,
                    'stock' => $producto->cantidad
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    //Eliminar producto del carrito
    
    public function eliminar($id) : JsonResponse
    {
        try {
            // Esta es una operación del lado del cliente (localStorage)
            // Solo validamos que el producto existe
            Producto::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del carrito'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }


    public function vaciar()
    {
        // Esta es una operación del lado del cliente (localStorage)
        return response()->json([
            'success' => true,
            'message' => 'Carrito vaciado correctamente'
        ]);
    }

    // Obtener contenido del carrito
    public function contenido(Request $request) : JsonResponse
    {
        try {
            $items = $request->input('items', []);
            $productos = [];

            foreach ($items as $item) {
                $producto = Producto::with([
                    'categoria',
                    'marca',
                    'estado.parametro'
                ])->find($item['id']);

                if ($producto) {
                    $productos[] = [
                        'id' => $producto->id,
                        'nombre' => $producto->producto,
                        'codigo' => $producto->codigo_barras,
                        'imagen' => $producto->imagen,
                        'stock' => $producto->cantidad,
                        'categoria' => $producto->categoria->name ?? 'Sin categoría',
                        'marca' => $producto->marca->name ?? 'Sin marca',
                        'descripcion' => $producto->descripcion
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'productos' => $productos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener contenido: ' . $e->getMessage()
            ], 500);
        }
    }

    private function checkItemsAvailability(array $items): ?JsonResponse
    {
        $erroresStock = [];

        foreach ($items as $item) {
            $producto = Producto::find($item['producto_id']);

            if (!$producto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            if ($producto->cantidad < $item['cantidad']) {
                $erroresStock[] = [
                    'producto' => $producto->producto,
                    'solicitado' => $item['cantidad'],
                    'disponible' => $producto->cantidad
                ];
            }
        }

        if (!empty($erroresStock)) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente para algunos productos',
                'errores' => $erroresStock
            ], 400);
        }

        return null;
    }
}


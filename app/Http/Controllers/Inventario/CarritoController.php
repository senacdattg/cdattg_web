<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventario\Producto;
use App\Models\User;


class CarritoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Vista del carrito
     */
    public function index()
    {
        return view('inventario.carrito.carrito');
    }

    /**
     * Agregar productos al carrito (crear orden)
     */
    public function agregar(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'notas' => 'nullable|string|max:1000'
        ]);

        try {
            // Verificar stock de todos los productos
            $erroresStock = [];
            foreach ($validated['items'] as $item) {
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

            // Si hay errores de stock, devolver error
            if (!empty($erroresStock)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente para algunos productos',
                    'errores' => $erroresStock
                ], 400);
            }

            // Aquí se debe crear la orden en la base de datos
            // Por ahora solo devolvemos éxito
            // En una implementación completa, se crearía un registro en una tabla de órdenes

            return response()->json([
                'success' => true,
                'message' => 'Solicitud procesada correctamente',
                'orden_id' => null // Se debería retornar el ID de la orden creada
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar cantidad de un producto en el carrito
     */
    public function actualizar(Request $request, $id)
    {
        $validated = $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

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

    /**
     * Eliminar producto del carrito
     */
    public function eliminar($id)
    {
        try {
            // Esta es una operación del lado del cliente (localStorage)
            // Solo validamos que el producto existe
            $producto = Producto::findOrFail($id);

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

    /**
     * Vaciar todo el carrito
     */
    public function vaciar()
    {
        // Esta es una operación del lado del cliente (localStorage)
        return response()->json([
            'success' => true,
            'message' => 'Carrito vaciado correctamente'
        ]);
    }

    /**
     * Obtener contenido del carrito
     */
    public function contenido(Request $request)
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
}

<?php

namespace App\Http\Controllers\Inventario;

use Illuminate\Http\Request;
use App\Models\Inventario\Orden;
use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Producto;
use Illuminate\Support\Facades\DB;
use App\Models\ParametroTema;

class OrdenController extends InventarioController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:VER ORDEN')->only('index');
        $this->middleware('can:CREAR ORDEN')->only(['store', 'storePrestamos']);
        $this->middleware('can:EDITAR ORDEN')->only('update');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ordenes = Orden::with([
            'tipoOrden.parametro',
            'userCreate.persona',
            'userUpdate.persona',
            'detalles.producto'
        ])
        ->latest()
        ->get();
        
        return view('inventario.ordenes.index', compact('ordenes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion_orden' => 'required|string',
            'tipo_orden_id' => 'required|exists:parametros_temas,id',
            'fecha_devolucion' => 'nullable|date|after:today',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.estado_orden_id' => 'required|exists:parametros_temas,id'
        ]);

        try {
            DB::beginTransaction();

            // Crear la orden
            $orden = new Orden([
                'descripcion_orden' => $validated['descripcion_orden'],
                'tipo_orden_id' => $validated['tipo_orden_id'],
                'fecha_devolucion' => $validated['fecha_devolucion'] ?? null
            ]);
            $this->setUserIds($orden);
            $orden->save();

            // Procesar cada producto
            foreach ($validated['productos'] as $productoData) {
                $producto = Producto::findOrFail($productoData['producto_id']);

                // Validar stock disponible
                if (!$producto->tieneStockDisponible($productoData['cantidad'])) {
                    throw new \Exception(
                        "Stock insuficiente para el producto '{$producto->producto}'. " .
                        "Disponible: {$producto->cantidad}, Solicitado: {$productoData['cantidad']}"
                    );
                }

                // Crear detalle de orden
                $detalle = new DetalleOrden([
                    'orden_id' => $orden->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $productoData['cantidad'],
                    'estado_orden_id' => $productoData['estado_orden_id']
                ]);
                $this->setUserIds($detalle);
                $detalle->save();

                // Descontar stock
                $producto->descontarStock($productoData['cantidad']);
            }

            DB::commit();

            return redirect()->route('inventario.ordenes.index')
                ->with('success', 'Orden creada exitosamente. Stock actualizado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al crear la orden: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage (Préstamos y Salidas).
     */
    public function storePrestamos(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'required|string|max:50',
            'rol' => 'required|in:estudiante,instructor,coordinador,administrativo',
            'programa_formacion' => 'required|string|max:255',
            'ficha' => 'required|string|max:50',
            'tipo' => 'required|in:prestamo,salida',
            'fecha_adquirido' => 'required|date',
            'fecha_devolucion' => 'required|date|after:fecha_adquirido',
            'descripcion' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            // Determinar el tipo de orden basado en el valor del formulario
            $tipoOrden = $validated['tipo'] === 'prestamo' ? 'PRESTAMO' : 'SALIDA';

            // Buscar o crear el parámetro de tipo de orden
            $parametroTipoOrden = ParametroTema::where('codigo', $tipoOrden)->first();
            if (!$parametroTipoOrden) {
                throw new \Exception("Tipo de orden '{$tipoOrden}' no encontrado en parámetros.");
            }

            // Crear la orden
            $orden = new Orden([
                'descripcion_orden' => sprintf(
                    "%s - %s (%s) - %s: %s",
                    ucfirst($validated['tipo']),
                    $validated['nombre'],
                    $validated['documento'],
                    $validated['rol'],
                    $validated['programa_formacion']
                ),
                'tipo_orden_id' => $parametroTipoOrden->id,
                'fecha_devolucion' => $validated['fecha_devolucion']
            ]);
            
            $this->setUserIds($orden);
            $orden->save();

            // Crear detalle para el préstamo/salida con los datos del beneficiario
            // Nota: Aquí necesitarías un producto predeterminado o un campo adicional para producto
            // Por ahora creamos un detalle genérico
            $detalle = new DetalleOrden([
                'orden_id' => $orden->id,
                'producto_id' => 1, // Producto por defecto - esto debe ser configurado
                'cantidad' => 1,
                'estado_orden_id' => $parametroTipoOrden->id
            ]);
            
            $this->setUserIds($detalle);
            $detalle->save();

            // Guardar información adicional del beneficiario en la descripción detallada
            $descripcionDetallada = sprintf(
                "BENEFICIARIO:\n" .
                "Nombre: %s\n" .
                "Documento: %s\n" .
                "Rol: %s\n" .
                "Programa: %s\n" .
                "Ficha: %s\n" .
                "Tipo: %s\n" .
                "Fecha Adquisición: %s\n" .
                "Fecha Devolución: %s\n\n" .
                "OBSERVACIONES:\n%s",
                $validated['nombre'],
                $validated['documento'],
                ucfirst($validated['rol']),
                $validated['programa_formacion'],
                $validated['ficha'],
                ucfirst($validated['tipo']),
                $validated['fecha_adquirido'],
                $validated['fecha_devolucion'],
                $validated['descripcion']
            );

            // Actualizar la descripción de la orden con los detalles
            $orden->update([
                'descripcion_orden' => $descripcionDetallada
            ]);

            DB::commit();

            return redirect()->route('inventario.ordenes.index')
                ->with('success', 'Préstamo/Salida creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al crear el préstamo/salida: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $orden = Orden::with(['detalles.producto'])->findOrFail($id);

        // Verificar si la orden ya tiene devoluciones
        $tieneDevoluciones = $orden->detalles()->whereHas('devoluciones')->exists();
        
        if ($tieneDevoluciones) {
            return redirect()->route('inventario.ordenes.index', $orden->id)
                ->with('error', 'No se puede editar una orden que ya tiene devoluciones registradas.');
        }

        $validated = $request->validate([
            'descripcion_orden' => 'required|string',
            'tipo_orden_id' => 'required|exists:parametros_temas,id',
            'fecha_devolucion' => 'nullable|date|after:today',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.estado_orden_id' => 'required|exists:parametros_temas,id'
        ]);

        try {
            DB::beginTransaction();

            // Primero devolver el stock de los productos anteriores
            foreach ($orden->detalles as $detalle) {
                $detalle->producto->devolverStock($detalle->cantidad);
            }

            // Eliminar detalles anteriores
            $orden->detalles()->delete();

            // Actualizar la orden
            $orden->fill([
                'descripcion_orden' => $validated['descripcion_orden'],
                'tipo_orden_id' => $validated['tipo_orden_id'],
                'fecha_devolucion' => $validated['fecha_devolucion'] ?? null
            ]);
            $this->setUserIds($orden, true);
            $orden->save();

            // Procesar nuevos productos
            foreach ($validated['productos'] as $productoData) {
                $producto = Producto::findOrFail($productoData['producto_id']);

                // Validar stock disponible
                if (!$producto->tieneStockDisponible($productoData['cantidad'])) {
                    throw new \Exception(
                        "Stock insuficiente para el producto '{$producto->producto}'. " .
                        "Disponible: {$producto->cantidad}, Solicitado: {$productoData['cantidad']}"
                    );
                }

                // Crear nuevo detalle de orden
                $detalle = new DetalleOrden([
                    'orden_id' => $orden->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $productoData['cantidad'],
                    'estado_orden_id' => $productoData['estado_orden_id']
                ]);
                $this->setUserIds($detalle);
                $detalle->save();

                // Descontar stock
                $producto->descontarStock($productoData['cantidad']);
            }

            DB::commit();

            return redirect()->route('inventario.ordenes.index', $orden->id)
                ->with('success', 'Orden actualizada exitosamente. Stock actualizado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar la orden: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orden = Orden::with(['detalles.producto', 'detalles.devoluciones'])->findOrFail($id);

        // Verificar si la orden ya tiene devoluciones
        $tieneDevoluciones = $orden->detalles()->whereHas('devoluciones')->exists();
        
        if ($tieneDevoluciones) {
            return redirect()->route('inventario.ordenes.index')
                ->with('error', 'No se puede eliminar una orden que ya tiene devoluciones registradas.');
        }

        try {
            DB::beginTransaction();

            // Devolver el stock de todos los productos
            foreach ($orden->detalles as $detalle) {
                $detalle->producto->devolverStock($detalle->cantidad);
            }

            // Eliminar detalles
            $orden->detalles()->delete();

            // Eliminar orden
            $orden->delete();

            DB::commit();

            return redirect()->route('inventario.ordenes.index')
                ->with('success', 'Orden eliminada exitosamente. Stock restaurado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('inventario.ordenes.index')
                ->with('error', 'Error al eliminar la orden: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar vista de préstamos y salidas
     */
    public function prestamosSalidas()
    {
        $ordenes = Orden::with([
            'tipoOrden.parametro',
            'userCreate.persona',
            'userUpdate.persona',
            'detalles.producto',
            'detalles.estadoOrden.parametro'
        ])
        ->latest()
        ->get();
        
        return view('inventario.ordenes.prestamos_salidas', compact('ordenes'));
    }
}

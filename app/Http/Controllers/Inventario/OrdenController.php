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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiposOrdenes = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'TIPOS DE ORDEN'))
            ->where('status', 1)
            ->get();

        $productos = Producto::with(['categoria', 'marca'])
            ->where('cantidad', '>', 0)
            ->orderBy('producto')
            ->get();

        $estadosOrden = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS DE ORDEN'))
            ->where('status', 1)
            ->get();

        return view('inventario.ordenes.create', compact('tiposOrdenes', 'productos', 'estadosOrden'));
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orden = Orden::with([
            'tipoOrden.parametro',
            'detalles.producto.categoria',
            'detalles.producto.marca',
            'detalles.estadoOrden.parametro',
            'detalles.devoluciones',
            'userCreate.persona',
            'userUpdate.persona'
        ])->findOrFail($id);
        
        return view('inventario.ordenes.show', compact('orden'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $orden = Orden::with(['detalles.producto'])->findOrFail($id);
        
        // Verificar si la orden ya tiene devoluciones
        $tieneDevoluciones = $orden->detalles()->whereHas('devoluciones')->exists();
        
        if ($tieneDevoluciones) {
            return redirect()->route('inventario.ordenes.show', $orden->id)
                ->with('error', 'No se puede editar una orden que ya tiene devoluciones registradas.');
        }

        $tiposOrdenes = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'TIPOS DE ORDEN'))
            ->where('status', 1)
            ->get();

        $productos = Producto::with(['categoria', 'marca'])
            ->where('cantidad', '>', 0)
            ->orderBy('producto')
            ->get();

        $estadosOrden = ParametroTema::with(['parametro','tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS DE ORDEN'))
            ->where('status', 1)
            ->get();

        return view('inventario.ordenes.edit', compact('orden', 'tiposOrdenes', 'productos', 'estadosOrden'));
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
            return redirect()->route('inventario.ordenes.show', $orden->id)
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

            return redirect()->route('inventario.ordenes.show', $orden->id)
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
}

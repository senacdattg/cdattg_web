<?php

namespace App\Http\Controllers\Inventario;

use Illuminate\Http\Request;
use App\Models\Inventario\Orden;
use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Producto;
use App\Models\ProgramaFormacion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\ParametroTema;
use App\Notifications\NuevaOrdenNotification;
use App\Notifications\StockBajoNotification;

class OrdenController extends InventarioController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:VER ORDEN')->only(['index', 'show', 'prestamosSalidas', 'storePrestamos']);
        $this->middleware('can:CREAR ORDEN')->only(['store']);
        $this->middleware('can:EDITAR ORDEN')->only('update');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ordenes = Orden::with([
            'tipoOrden.parametro',
            'userCreate',
            'detalles.producto',
            'detalles.estadoOrden.parametro'
        ])
        ->latest()
        ->paginate(15);
        
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
     * Mostrar formulario de solicitud de préstamo/salida
     */
    public function prestamosSalidas()
    {
        $programas = ProgramaFormacion::where('status', true)
            ->orderBy('nombre', 'asc')
            ->get(['id', 'nombre', 'codigo']);
            
        return view('inventario.ordenes.prestamos_salidas', compact('programas'));
    }

    /**
     * Mostrar órdenes pendientes (EN ESPERA)
     */
    public function pendientes()
    {
        $estadoEnEspera = ParametroTema::whereHas('parametro', function($q) {
            $q->where('name', 'EN ESPERA');
        })
        ->whereHas('tema', function($q) {
            $q->where('name', 'ESTADOS DE ORDEN');
        })
        ->first();

        $ordenes = collect();
        
        if ($estadoEnEspera) {
            $ordenes = Orden::with([
                'tipoOrden.parametro',
                'userCreate',
                'detalles.producto',
                'detalles.estadoOrden.parametro'
            ])
            ->whereHas('detalles', function($q) use ($estadoEnEspera) {
                $q->where('estado_orden_id', $estadoEnEspera->id);
            })
            ->latest()
            ->paginate(15);
        }
        
        return view('inventario.ordenes.pendientes', compact('ordenes'));
    }

    /**
     * Mostrar órdenes completadas (APROBADA)
     */
    public function completadas()
    {
        $estadoAprobada = ParametroTema::whereHas('parametro', function($q) {
            $q->where('name', 'APROBADA');
        })
        ->whereHas('tema', function($q) {
            $q->where('name', 'ESTADOS DE ORDEN');
        })
        ->first();

        $ordenes = collect();
        
        if ($estadoAprobada) {
            $ordenes = Orden::with([
                'tipoOrden.parametro',
                'userCreate',
                'detalles.producto',
                'detalles.estadoOrden.parametro'
            ])
            ->whereHas('detalles', function($q) use ($estadoAprobada) {
                $q->where('estado_orden_id', $estadoAprobada->id);
            })
            ->latest()
            ->paginate(15);
        }
        
        return view('inventario.ordenes.completadas', compact('ordenes'));
    }

    /**
     * Mostrar órdenes rechazadas (RECHAZADA)
     */
    public function rechazadas()
    {
        $estadoRechazada = ParametroTema::whereHas('parametro', function($q) {
            $q->where('name', 'RECHAZADA');
        })
        ->whereHas('tema', function($q) {
            $q->where('name', 'ESTADOS DE ORDEN');
        })
        ->first();

        $ordenes = collect();
        
        if ($estadoRechazada) {
            $ordenes = Orden::with([
                'tipoOrden.parametro',
                'userCreate',
                'detalles.producto',
                'detalles.estadoOrden.parametro'
            ])
            ->whereHas('detalles', function($q) use ($estadoRechazada) {
                $q->where('estado_orden_id', $estadoRechazada->id);
            })
            ->latest()
            ->paginate(15);
        }
        
        return view('inventario.ordenes.rechazadas', compact('ordenes'));
    }

    /**
     * Store a newly created resource in storage (Préstamos y Salidas).
     */
    public function storePrestamos(Request $request)
    {
        $validated = $request->validate([
            'rol' => 'required|string|max:100',
            'programa_formacion' => 'required|string|max:255',
            'tipo' => 'required|in:prestamo,salida',
            'fecha_devolucion' => 'required_if:tipo,prestamo|nullable|date|after:today',
            'descripcion' => 'required|string',
            'carrito' => 'required|json' // El carrito viene como JSON desde el frontend
        ]);

        try {
            DB::beginTransaction();

            // Decodificar el carrito
            $carrito = json_decode($validated['carrito'], true);
            
            if (empty($carrito) || !is_array($carrito)) {
                throw new \Exception('El carrito está vacío. Agregue productos antes de crear la solicitud.');
            }

            // Determinar el tipo de orden (PRÉSTAMO o SALIDA)
            $tipoMap = [
                'prestamo' => 'PRÉSTAMO',
                'salida' => 'SALIDA'
            ];
            
            $codigoTipoOrden = $tipoMap[$validated['tipo']] ?? strtoupper($validated['tipo']);

            // Buscar el parámetro de tipo de orden (tema: TIPOS DE ORDEN)
            $parametroTipoOrden = ParametroTema::whereHas('tema', function ($q) {
                    $q->whereRaw('UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(name, "Á", "A"), "É", "E"), "Í", "I"), "Ó", "O"), "Ú", "U")) = ?', ['TIPOS DE ORDEN']);
                })
                ->whereHas('parametro', function ($q) use ($codigoTipoOrden) {
                    // Normalizar sin acentos para la comparación
                    $nombreNormalizado = str_replace(
                        ['Á', 'É', 'Í', 'Ó', 'Ú'],
                        ['A', 'E', 'I', 'O', 'U'],
                        strtoupper($codigoTipoOrden)
                    );
                    $q->whereRaw('UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(name, "Á", "A"), "É", "E"), "Í", "I"), "Ó", "O"), "Ú", "U")) = ?', [$nombreNormalizado]);
                })
                ->first();

            if (!$parametroTipoOrden) {
                throw new \Exception("Tipo de orden '{$codigoTipoOrden}' no encontrado. Verifique los parámetros del sistema.");
            }

            // Buscar parámetro de estado 'EN ESPERA' (tema: ESTADOS DE ORDEN)
            $estadoEnEspera = ParametroTema::whereHas('tema', function ($q) {
                    $q->whereRaw('UPPER(name) = ?', ['ESTADOS DE ORDEN']);
                })
                ->whereHas('parametro', function ($q) {
                    $q->whereRaw('UPPER(name) = ?', ['EN ESPERA']);
                })
                ->first();

            if (!$estadoEnEspera) {
                throw new \Exception("Estado 'EN ESPERA' no encontrado. Verifique los parámetros del sistema.");
            }


            // Obtener datos del usuario
            $usuario = Auth::user();
            $solicitante = $usuario->name ?? 'Usuario';
            $email = $usuario->email ?? '';

            // Crear descripción detallada
            $descripcionDetallada = sprintf(
                "SOLICITUD DE %s\n\n" .
                "SOLICITANTE:\n" .
                "Nombre: %s\n" .
                "Email: %s\n" .
                "Rol: %s\n" .
                "Programa de Formación: %s\n\n" .
                "DETALLES:\n" .
                "Tipo: %s\n" .
                "%s\n" .
                "MOTIVO:\n%s",
                strtoupper($validated['tipo']),
                $solicitante,
                $email,
                $validated['rol'],
                $validated['programa_formacion'],
                ucfirst($validated['tipo']),
                $validated['tipo'] === 'prestamo' && !empty($validated['fecha_devolucion']) 
                    ? "Fecha de Devolución: {$validated['fecha_devolucion']}\n" 
                    : "Sin fecha de devolución\n",
                $validated['descripcion']
            );

            // Crear la orden
            $orden = new Orden([
                'descripcion_orden' => $descripcionDetallada,
                'tipo_orden_id' => $parametroTipoOrden->id,
                'fecha_devolucion' => $validated['tipo'] === 'prestamo' ? $validated['fecha_devolucion'] : null
            ]);
            
            $this->setUserIds($orden);
            $orden->save();

            // Procesar productos del carrito
            foreach ($carrito as $item) {
                $productoId = $item['id'] ?? $item['producto_id'] ?? null;
                $cantidad = (int)($item['quantity'] ?? $item['cantidad'] ?? 1);

                if (!$productoId) {
                    continue;
                }

                $producto = Producto::find($productoId);
                
                if (!$producto) {
                    throw new \Exception("Producto con ID {$productoId} no encontrado.");
                }

                // Validar stock disponible
                if ($producto->cantidad < $cantidad) {
                    throw new \Exception(
                        "Stock insuficiente para '{$producto->producto}'. " .
                        "Disponible: {$producto->cantidad}, Solicitado: {$cantidad}"
                    );
                }

                // Crear detalle en estado EN ESPERA (no descontar stock aún)
                $detalle = new DetalleOrden([
                    'orden_id' => $orden->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'estado_orden_id' => $estadoEnEspera->id
                ]);
                $this->setUserIds($detalle);
                $detalle->save();
            }

            DB::commit();

            // Limpiar el carrito después de crear la orden exitosamente
            session()->forget('carrito_data');
            // Notificar a superadministradores sobre la nueva orden
            $superadmins = User::role('SUPER ADMINISTRADOR')->get();
            if ($superadmins->isNotEmpty()) {
                Notification::send($superadmins, new NuevaOrdenNotification($orden));
            }

            return redirect()->route('inventario.ordenes.index')
                ->with('success', 'Solicitud creada exitosamente. Está pendiente de aprobación por el administrador.')
                ->with('clear_cart', true);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al crear la solicitud: ' . $e->getMessage());
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
     * Display the specified resource.
     */
    public function show(Orden $orden)
    {
        $orden->load([
            'tipoOrden.parametro',
            'userCreate',
            'detalles.producto',
            'detalles.estadoOrden.parametro',
            'detalles.aprobacion.aprobador'
        ]);

        return view('inventario.ordenes.show', compact('orden'));
    }
}
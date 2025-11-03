<?php

namespace App\Http\Controllers\Inventario;

use Illuminate\Http\Request;
use App\Models\Inventario\Aprobacion;
use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Producto;
use App\Models\ParametroTema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OrdenAprobadaNotification;
use App\Notifications\OrdenRechazadaNotification;

class AprobacionController extends InventarioController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:APROBAR ORDEN')->only(['aprobar', 'rechazar', 'pendientes']);
    }

    /**
     * Mostrar órdenes pendientes de aprobación
     */
    public function pendientes()
    {
        // Obtener estado EN ESPERA
        $estadoEnEspera = ParametroTema::whereHas('parametro', function($q) {
            $q->where('name', 'EN ESPERA');
        })
        ->whereHas('tema', function($q) {
            $q->where('name', 'ESTADOS DE ORDEN');
        })
        ->first();

        if (!$estadoEnEspera) {
            return view('inventario.aprobaciones.pendientes', ['detalles' => collect()]);
        }

        // Obtener detalles de orden en estado EN ESPERA
        $detalles = DetalleOrden::with([
            'orden.tipoOrden.parametro',
            'orden.userCreate',
            'producto',
            'estadoOrden.parametro'
        ])
        ->where('estado_orden_id', $estadoEnEspera->id)
        ->whereDoesntHave('aprobacion') // Solo los que no tienen aprobación aún
        ->latest()
        ->get();

        return view('inventario.aprobaciones.pendientes', compact('detalles'));
    }

    /**
     * Aprobar una solicitud
     */
    public function aprobar(Request $request, $detalleOrdenId)
    {
        try {
            DB::beginTransaction();

            $detalleOrden = DetalleOrden::with(['producto', 'orden'])->findOrFail($detalleOrdenId);

            // Verificar que esté en estado EN ESPERA
            $estadoEnEspera = ParametroTema::whereHas('parametro', function($q) {
                $q->where('name', 'EN ESPERA');
            })
            ->whereHas('tema', function($q) {
                $q->where('name', 'ESTADOS DE ORDEN');
            })
            ->first();

            if ($detalleOrden->estado_orden_id != $estadoEnEspera->id) {
                throw new \Exception('Esta solicitud no está pendiente de aprobación.');
            }

            // Verificar que no tenga aprobación previa
            if ($detalleOrden->aprobacion) {
                throw new \Exception('Esta solicitud ya fue procesada anteriormente.');
            }

            // Verificar stock disponible
            $producto = $detalleOrden->producto;
            if ($producto->cantidad < $detalleOrden->cantidad) {
                throw new \Exception(
                    "Stock insuficiente para '{$producto->producto}'. " .
                    "Disponible: {$producto->cantidad}, Solicitado: {$detalleOrden->cantidad}"
                );
            }

            // Obtener estado APROBADA
            $estadoAprobada = ParametroTema::whereHas('parametro', function($q) {
                $q->where('name', 'APROBADA');
            })
            ->whereHas('tema', function($q) {
                $q->where('name', 'ESTADOS DE ORDEN');
            })
            ->first();

            if (!$estadoAprobada) {
                throw new \Exception("Estado 'APROBADA' no encontrado en parámetros.");
            }

            // Actualizar estado del detalle de orden
            $detalleOrden->update([
                'estado_orden_id' => $estadoAprobada->id,
                'user_update_id' => Auth::id()
            ]);

            // Crear registro de aprobación
            $aprobacion = new Aprobacion([
                'detalle_orden_id' => $detalleOrden->id,
                'estado_aprobacion_id' => $estadoAprobada->id,
                'user_create_id' => Auth::id(),
                'user_update_id' => Auth::id()
            ]);
            $aprobacion->save();

            // Descontar el stock del producto
            $producto->cantidad -= $detalleOrden->cantidad;
            $producto->user_update_id = Auth::id();
            $producto->save();

            // Enviar notificación al solicitante de la orden
            $solicitante = $detalleOrden->orden->userCreate;
            if ($solicitante) {
                $solicitante->notify(new OrdenAprobadaNotification($detalleOrden, Auth::user()));
            }

            DB::commit();

            return redirect()->back()
                ->with('success', "Solicitud aprobada exitosamente. Stock actualizado para '{$producto->producto}'.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Error al aprobar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar una solicitud
     */
    public function rechazar(Request $request, $detalleOrdenId)
    {
        $validated = $request->validate([
            'motivo_rechazo' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $detalleOrden = DetalleOrden::with(['producto', 'orden'])->findOrFail($detalleOrdenId);

            // Verificar que esté en estado EN ESPERA
            $estadoEnEspera = ParametroTema::whereHas('parametro', function($q) {
                $q->where('name', 'EN ESPERA');
            })
            ->whereHas('tema', function($q) {
                $q->where('name', 'ESTADOS DE ORDEN');
            })
            ->first();

            if ($detalleOrden->estado_orden_id != $estadoEnEspera->id) {
                throw new \Exception('Esta solicitud no está pendiente de aprobación.');
            }

            // Verificar que no tenga aprobación previa
            if ($detalleOrden->aprobacion) {
                throw new \Exception('Esta solicitud ya fue procesada anteriormente.');
            }

            // Obtener estado RECHAZADA
            $estadoRechazada = ParametroTema::whereHas('parametro', function($q) {
                $q->where('name', 'RECHAZADA');
            })
            ->whereHas('tema', function($q) {
                $q->where('name', 'ESTADOS DE ORDEN');
            })
            ->first();

            if (!$estadoRechazada) {
                throw new \Exception("Estado 'RECHAZADA' no encontrado en parámetros.");
            }

            // Actualizar estado del detalle de orden
            $detalleOrden->update([
                'estado_orden_id' => $estadoRechazada->id,
                'user_update_id' => Auth::id()
            ]);

            // Crear registro en aprobaciones (con estado rechazada)
            $aprobacion = new Aprobacion([
                'detalle_orden_id' => $detalleOrden->id,
                'estado_aprobacion_id' => $estadoRechazada->id,
                'user_create_id' => Auth::id(),
                'user_update_id' => Auth::id()
            ]);
            $aprobacion->save();

            // Agregar motivo de rechazo a la descripción de la orden
            $orden = $detalleOrden->orden;
            $orden->descripcion_orden .= "\n\n--- SOLICITUD RECHAZADA ---\n";
            $orden->descripcion_orden .= "Producto: {$detalleOrden->producto->producto}\n";
            $orden->descripcion_orden .= "Motivo: {$validated['motivo_rechazo']}\n";
            $orden->descripcion_orden .= "Rechazado por: " . Auth::user()->name . "\n";
            $orden->descripcion_orden .= "Fecha: " . now()->format('d/m/Y H:i') . "\n";
            $orden->user_update_id = Auth::id();
            $orden->save();

            // Enviar notificación al solicitante de la orden
            $solicitante = $detalleOrden->orden->userCreate;
            if ($solicitante) {
                $solicitante->notify(new OrdenRechazadaNotification(
                    $detalleOrden, 
                    Auth::user(), 
                    $validated['motivo_rechazo']
                ));
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Solicitud rechazada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Error al rechazar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Ver historial de aprobaciones
     */
    public function historial()
    {
        $aprobaciones = Aprobacion::with([
            'detalleOrden.orden.tipoOrden.parametro',
            'detalleOrden.orden.userCreate',
            'detalleOrden.producto',
            'estado.parametro',
            'aprobador'
        ])
        ->latest()
        ->paginate(20);

        return view('inventario.aprobaciones.historial', compact('aprobaciones'));
    }
}
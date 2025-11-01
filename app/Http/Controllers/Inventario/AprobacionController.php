<?php

namespace App\Http\Controllers\Inventario;

use Illuminate\Http\Request;
use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Aprobacion;
use Illuminate\Support\Facades\DB;
use App\Models\Inventario\Producto;
use App\Models\ParametroTema;

class AprobacionController extends InventarioController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:APROBAR ORDEN');
    }

    /**
     * Aprobar un detalle de orden: crear registro en aprobaciones, descontar stock y eliminar detalle.
     */
    public function aprobar(Request $request, $detalleId)
    {
        $detalle = DetalleOrden::with('producto', 'orden')->findOrFail($detalleId);

        try {
            DB::beginTransaction();

            $producto = $detalle->producto;

            // Validar stock (asegúrate de que el método exista en Producto)
            if (!method_exists($producto, 'tieneStockDisponible') || !$producto->tieneStockDisponible($detalle->cantidad)) {
                throw new \Exception("Stock insuficiente para aprobar el producto '{$producto->producto}'.");
            }

            // Descontar stock (asegúrate de que el método exista en Producto)
            if (!method_exists($producto, 'descontarStock')) {
                throw new \Exception("El método descontarStock no está definido en el modelo Producto.");
            }
            $producto->descontarStock($detalle->cantidad);

            // Obtener el id del parámetro APROBADO
            $paramAprobadoId = \App\Models\ParametroTema::where('codigo', 'APROBADO')->value('id');

            if (!$paramAprobadoId) {
                throw new \Exception("Parámetro 'APROBADO' no encontrado en 'parametros_temas'. Por favor cree el parámetro.");
            }

            // Crear registro de aprobación
            $aprob = Aprobacion::create([
                'detalle_orden_id' => $detalle->id,
                'estado_id' => $paramAprobadoId
            ]);

            // Actualizar estado del detalle a APROBADO
            $detalle->estado_orden_id = $paramAprobadoId;

            // Si usas setUserIds, verifica que exista; si no, asigna user_update_id directamente
            if (method_exists($this, 'setUserIds')) {
                $this->setUserIds($detalle, true);
            } else {
                if (property_exists($detalle, 'user_update_id')) {
                    $detalle->user_update_id = auth()->id;
                }
            }

            $detalle->save();

            DB::commit();

            return redirect()->back()->with('success', 'Detalle aprobado y registrado en aprobaciones.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al aprobar: ' . $e->getMessage());
        }
    }

    public function rechazar(Request $request, $detalleId)
    {
        $detalle = DetalleOrden::findOrFail($detalleId);

        try {
            DB::beginTransaction();

            // Obtener sólo el id del parámetro RECHAZADO
            $paramRechazadoId = \App\Models\ParametroTema::where('codigo', 'RECHAZADO')->value('id');

            if (!$paramRechazadoId) {
                throw new \Exception("Parámetro 'RECHAZADO' no encontrado en 'parametros_temas'. Por favor cree el parámetro antes de rechazar.");
            }

            // Crear registro de rechazo en aprobaciones
            Aprobacion::create([
                'detalle_orden_id' => $detalle->id,
                'estado_id' => $paramRechazadoId
            ]);

            // Actualizar estado del detalle
            $detalle->estado_orden_id = $paramRechazadoId;

            if (method_exists($this, 'setUserIds')) {
                $this->setUserIds($detalle, true);
            } else {
                if (property_exists($detalle, 'user_update_id')) {
                    $detalle->user_update_id = auth()->id;
                }
            }

            $detalle->save();

            DB::commit();
            return redirect()->back()->with('success', 'Detalle marcado como rechazado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al rechazar: ' . $e->getMessage());
        }
    }
} 

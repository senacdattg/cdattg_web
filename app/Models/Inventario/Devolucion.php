<?php

namespace App\Models\Inventario;

use App\Exceptions\DevolucionException;
use App\Traits\Seguimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Devolucion extends Model
{
    use HasFactory, Seguimiento;

    protected $table = 'devoluciones';

    protected $fillable = [
        'detalle_orden_id',
        'cantidad_devuelta',
        'fecha_devolucion',
        'estado_id',
        'observaciones',
        'cierra_sin_stock',
        'user_create_id',
        'user_update_id'
    ];

    protected $casts = [
        'fecha_devolucion' => 'datetime',
        'cierra_sin_stock' => 'boolean',
    ];


    // Relación con el detalle de orden
    public function detalleOrden()
    {
        return $this->belongsTo(DetalleOrden::class, 'detalle_orden_id');
    }

    // Registrar devolución y restaurar stock
    public static function registrarDevolucion(int $detalleOrdenId, int $cantidadDevuelta, ?string $observaciones = null): self
    {
        return DB::transaction(function () use ($detalleOrdenId, $cantidadDevuelta, $observaciones): self {
            $detalleOrden = DetalleOrden::with(['producto.tipoProducto.parametro', 'devoluciones'])
                ->findOrFail($detalleOrdenId);

            if ($detalleOrden->tieneCierreSinStock()) {
                throw new DevolucionException('Este préstamo ya fue cerrado sin devolución de stock.');
            }

            $cantidadPendiente = $detalleOrden->getCantidadPendiente();
            if ($cantidadPendiente <= 0) {
                throw new DevolucionException('No hay cantidades pendientes por devolver.');
            }

            if ($cantidadDevuelta < 0) {
                throw new DevolucionException('La cantidad devuelta no puede ser negativa.');
            }

            if ($cantidadDevuelta > $cantidadPendiente) {
                throw new DevolucionException("No puedes devolver más de lo prestado. Cantidad pendiente: {$cantidadPendiente}");
            }

            $esCierreSinStock = $cantidadDevuelta === 0;
            $observacionesDepuradas = $observaciones !== null ? trim($observaciones) : null;

            if ($esCierreSinStock) {
                if ($observacionesDepuradas === null || $observacionesDepuradas === '') {
                    throw new DevolucionException('Debes registrar el motivo del consumo total para cerrar sin devolución.');
                }

                if (!$detalleOrden->producto->esConsumible()) {
                    throw new DevolucionException('Solo los productos consumibles pueden cerrarse sin devolución de stock.');
                }
            }

            $devolucion = self::create([
                'detalle_orden_id' => $detalleOrdenId,
                'cantidad_devuelta' => $cantidadDevuelta,
                'fecha_devolucion' => now(),
                'estado_id' => 1,
                'observaciones' => $observacionesDepuradas,
                'cierra_sin_stock' => $esCierreSinStock,
                'user_create_id' => Auth::id(),
                'user_update_id' => Auth::id()
            ]);

            if (!$esCierreSinStock && $cantidadDevuelta > 0) {
                $detalleOrden->producto->devolverStock($cantidadDevuelta);
            }

            return $devolucion->fresh([
                'detalleOrden.producto',
                'detalleOrden.orden',
            ]);
        });
    }

    
    // Verificar si la devolución fue a tiempo
    public function fueATiempo()
    {
        $fechaEsperada = $this->detalleOrden->orden->fecha_devolucion;

        if (!$fechaEsperada) {
            return null;
        }

        return $this->fecha_devolucion->lte($fechaEsperada);
    }


    //Obtener días de retraso en la devolución
    public function getDiasRetraso()
    {
        $fechaEsperada = $this->detalleOrden->orden->fecha_devolucion;

        if (!$fechaEsperada || $this->fueATiempo()) {
            return 0;
        }

        return $this->fecha_devolucion->diffInDays($fechaEsperada);
    }

    // Alias para compatibilidad con el controlador
    public function getDiasRetrasoDevolucion()
    {
        return $this->getDiasRetraso();
    }
}

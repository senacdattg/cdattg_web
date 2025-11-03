<?php

namespace App\Models\Inventario;

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
        'user_create_id',
        'user_update_id'
    ];

    protected $casts = [
        'fecha_devolucion' => 'datetime'
    ];


    // Relación con el detalle de orden
    public function detalleOrden()
    {
        return $this->belongsTo(DetalleOrden::class, 'detalle_orden_id');
    }

    // Registrar devolución y restaurar stock
    public static function registrarDevolucion($detalleOrdenId, $cantidadDevuelta, $observaciones = null)
    {
        return DB::transaction(function () use ($detalleOrdenId, $cantidadDevuelta, $observaciones) {
            // Obtener el detalle de la orden
            $detalleOrden = DetalleOrden::with('producto')->findOrFail($detalleOrdenId);

            // Validar que no se devuelva más de lo prestado
            $cantidadPendiente = $detalleOrden->getCantidadPendiente();
            if ($cantidadDevuelta > $cantidadPendiente) {
                throw new \Exception("No puedes devolver más de lo prestado. Cantidad pendiente: {$cantidadPendiente}");
            }

            // Crear el registro de devolución
            $devolucion = self::create([
                'detalle_orden_id' => $detalleOrdenId,
                'cantidad_devuelta' => $cantidadDevuelta,
                'fecha_devolucion' => now(),
                'estado_id' => 1, // Estado por defecto (completado)
                'observaciones' => $observaciones,
                'user_create_id' => Auth::id(),
                'user_update_id' => Auth::id()
            ]);

            // Restaurar el stock del producto
            $producto = $detalleOrden->producto;
            $producto->devolverStock($cantidadDevuelta);

            return $devolucion;
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

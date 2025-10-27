<?php

namespace App\Models\Inventario;

use App\Traits\Seguimiento;
use App\Models\ParametroTema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrden extends Model
{
    use HasFactory, Seguimiento;

    protected $table = 'detalle_ordenes';

    protected $fillable = [
        'orden_id',
        'producto_id',
        'cantidad',
        'estado_orden_id',
        'user_create_id',
        'user_update_id'
    ];

    // Relación con la orden 
    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    
    // Relación con el producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Relación con el estado de la orden
    public function estadoOrden()
    {
        return $this->belongsTo(ParametroTema::class, 'estado_orden_id');
    }

    
    // Relación con devoluciones
    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class, 'detalle_orden_id');
    }

    // Obtener la cantidad total devuelta
    public function getCantidadDevuelta()
    {
        return $this->devoluciones()->sum('cantidad_devuelta');
    }

    // Verificar si está completamente devuelto
    public function Devuelto()
    {
        return $this->getCantidadDevuelta() >= $this->cantidad;
    }

    // Obtener cantidad pendiente de devolución
    public function getCantidadPendiente()
    {
        return $this->cantidad - $this->getCantidadDevuelta();
    }

    // Verificar si el préstamo está vencido
    public function Vencido()
    {
        if (!$this->orden->fecha_devolucion) {
            return false;
        }

        return now()->gt($this->orden->fecha_devolucion) && !$this->Devuelto();
    }

    // Obtener días de retraso
    public function getDiasRetraso()
    {
        if (!$this->Vencido()) {
            return 0;
        }

        return now()->diffInDays($this->orden->fecha_devolucion);
    }

    // Obtener días restantes para devolución
    public function getDiasRestantes()
    {
        if (!$this->orden->fecha_devolucion) {
            return null;
        }

        if ($this->Vencido()) {
            return 0;
        }

        return now()->diffInDays($this->orden->fecha_devolucion, false);
    }
}

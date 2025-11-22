<?php

namespace App\Models\Inventario;

use App\Traits\Seguimiento;
use App\Models\ParametroTema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'user_edit_id'
    ];

    // Relación con la orden 
    public function orden() : BelongsTo
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    
    // Relación con el producto
    public function producto() : BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Relación con el estado de la orden
    public function estadoOrden() : BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'estado_orden_id');
    }

    
    // Relación con devoluciones
    public function devoluciones() : HasMany
    {
        return $this->hasMany(Devolucion::class, 'detalle_orden_id');
    }

    public function aprobacion() : HasOne
    {
        return $this->hasOne(\App\Models\Inventario\Aprobacion::class, 'detalle_orden_id', 'id');
    }

    // Obtener la cantidad total devuelta
    public function getCantidadDevuelta() : int
    {
        return $this->devoluciones()->sum('cantidad_devuelta');
    }

    public function tieneCierreSinStock(): bool
    {
        if ($this->relationLoaded('devoluciones')) {
            return $this->devoluciones->contains(static function (Devolucion $devolucion): bool {
                return $devolucion->cierra_sin_stock === true;
            });
        }

        return $this->devoluciones()
            ->where('cierra_sin_stock', true)
            ->exists();
    }

    // Verificar si está completamente devuelto
    public function estaCompletamenteDevuelto() : bool
    {
        if ($this->tieneCierreSinStock()) {
            return true;
        }

        return $this->getCantidadDevuelta() >= $this->cantidad;
    }

    // Alias para compatibilidad
    public function Devuelto() : bool
    {
        return $this->estaCompletamenteDevuelto();
    }

    // Obtener cantidad pendiente de devolución
    public function getCantidadPendiente() : int
    {
        if ($this->tieneCierreSinStock()) {
            return 0;
        }

        $pendiente = $this->cantidad - $this->getCantidadDevuelta();

        return $pendiente > 0 ? $pendiente : 0;
    }
}

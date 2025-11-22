<?php

namespace App\Models\Inventario;

use App\Models\ParametroTema;
use App\Traits\Seguimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orden extends Model
{
    use HasFactory, Seguimiento;

    protected $table = 'ordenes';

    protected $guarded = [];

    protected $casts = [
        'fecha_devolucion' => 'datetime'
    ];

    public function tipoOrden() : BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'tipo_orden_id');
    }

    // Relación con detalles de la orden
    public function detalles() : HasMany
    {
        return $this->hasMany(DetalleOrden::class, 'orden_id');
    }

    // Verificar si es un préstamo   
    public function Prestamo() : bool
    {
        return $this->tipoOrden && strtoupper($this->tipoOrden->parametro->name ?? '') === 'PRÉSTAMO';
    }

    
    // Verificar si es una salida 
    public function Salida() : bool
    {
        return $this->tipoOrden && strtoupper($this->tipoOrden->parametro->name ?? '') === 'SALIDA';
    }
}

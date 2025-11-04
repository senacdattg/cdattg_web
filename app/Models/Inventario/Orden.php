<?php

namespace App\Models\Inventario;

use App\Models\ParametroTema;
use App\Traits\Seguimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory, Seguimiento;

    protected $table = 'ordenes';

    protected $fillable = [
        'descripcion_orden',
        'tipo_orden_id',
        'fecha_devolucion',
        'user_create_id',
        'user_update_id'
    ];

    protected $casts = [
        'fecha_devolucion' => 'datetime'
    ];

    public function tipoOrden()
    {
        return $this->belongsTo(ParametroTema::class, 'tipo_orden_id');
    }

    // Relación con detalles de la orden
    public function detalles()
    {
        return $this->hasMany(DetalleOrden::class, 'orden_id');
    }

    // Verificar si es un préstamo   
    public function Prestamo()
    {
        return $this->tipoOrden && strtoupper($this->tipoOrden->parametro->name ?? '') === 'PRÉSTAMO';
    }

    
    // Verificar si es una salida 
    public function Salida()
    {
        return $this->tipoOrden && strtoupper($this->tipoOrden->parametro->name ?? '') === 'SALIDA';
    }
}

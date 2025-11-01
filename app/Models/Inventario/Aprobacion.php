<?php

namespace App\Models\Inventario;

use App\Traits\Seguimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aprobacion extends Model
{
    use HasFactory, Seguimiento;

    protected $table = 'aprobaciones';

    protected $fillable = [
        'detalle_orden_id',
        'estado_aprobacion_id',
        'user_create_id',
        'user_update_id'
    ];

    public function detalleOrden()
    {
        return $this->belongsTo(DetalleOrden::class, 'detalle_orden_id');
    }

    public function estado()
    {
        return $this->belongsTo(\App\Models\ParametroTema::class, 'estado_aprobacion_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_update_id');
    }

}

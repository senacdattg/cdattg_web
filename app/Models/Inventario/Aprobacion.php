<?php

namespace App\Models\Inventario;

use App\Traits\Seguimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function detalleOrden() : BelongsTo
    {
        return $this->belongsTo(DetalleOrden::class, 'detalle_orden_id');
    }

    public function estado() : BelongsTo
    {
        return $this->belongsTo(\App\Models\ParametroTema::class, 'estado_aprobacion_id');
    }

    public function aprobador() : BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_update_id');
    }

}

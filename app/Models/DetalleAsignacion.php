<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleAsignacion extends Model
{
    /** @use HasFactory<\Database\Factories\DetalleAsignacionFactory> */
    use HasFactory;
    protected $table = 'detalle_asignacion';

    public function asignacionDeFormacion()
    {
        return $this->belongsTo(AsignacionDeFormacion::class, 'id_asignacion_formacion');
    }
    // Crear relacion con el parametro dias de formacion
}

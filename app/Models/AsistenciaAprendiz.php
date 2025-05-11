<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenciaAprendiz extends Model
{
    use HasFactory;

    protected $table = 'asistencia_aprendices';

    protected $fillable = [
        'id_asignacion_formacion',
        'hora_ingreso',
        'hora_salida',
        'aprendiz',
        'fecha',
    ];

    public function instructor()
    {
        return $this->hasOneThrough(Instructor::class, CaracterizacionPrograma::class, 'id', 'id', 'caracterizacion_id', 'instructor_id');
    }

    public function sede(){
        return $this->hasOneThrough(Sede::class, 'id', 'id', 'caracterizacion_id', 'sede_id');
    }

}

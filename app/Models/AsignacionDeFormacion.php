<?php

namespace App\Models;

use App\Models\Ambiente;
use App\Models\Instructor;
use App\Models\JornadaFormacion;
use App\Models\FichaCaracterizacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AsignacionDeFormacion extends Model
{
    /** @use HasFactory<\Database\Factories\AsignacionDeFormacionFactory> */
    use HasFactory;
    protected $fillable = [
        'id_instructor',
        'id_ficha',
        'id_ambiente',
        'id_jornada',
        'fecha_inicio',
        'fecha_fin'];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'id_instructor');
    }
    public function ficha(){
        return $this->belongsTo(FichaCaracterizacion::class, 'id_ficha');
    }
    public function ambiente(){
        return $this->belongsTo(Ambiente::class, 'id_ambiente');
    }
    public function jornada(){
        return $this->belongsTo(JornadaFormacion::class, 'id_jornada');
    }
    public function detalleAsignacion()
    {
        return $this->hasMany(DetalleAsignacion::class, 'id_asignacion_formacion');
    }
    public function asistenciasAprendices()
{
    return $this->hasMany(AsistenciaAprendiz::class, 'id_asignacion_formacion');
}

}

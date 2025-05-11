<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JornadaFormacion extends Model
{
    use HasFactory;
    protected $fillable = [
        'jornada',
        'hora_inicio',
        'hora_fin'];
    protected $table = 'jornadas_formacion';


    public function caracterizaciones()
    {
        return $this->hasMany(CaracterizacionPrograma::class, 'jornada_id');
    }

    public function caracterizacionProgramas()
    {
        return $this->hasMany(CaracterizacionPrograma::class, 'jornada_id');
    }
    public function asignacionDeFormacion()
    {
        return $this->hasMany(AsignacionDeFormacion::class, 'id_jornada');
    }

}

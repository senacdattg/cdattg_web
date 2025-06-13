<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaracterizacionPrograma extends Model
{
    use HasFactory;

    protected $table = 'caracterizacion_programas';

    public function ficha()
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'persona_id');
    }

    public function programaFormacion()
    {
        return $this->belongsTo(ProgramaFormacion::class, 'programa_formacion_id');
    }

    public function jornada()
    {
        return $this->belongsTo(JornadaFormacion::class, 'jornada_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'instructor_persona_id');
    }

    public function asistencias()
    {
        return $this->hasMany(AsistenciaAprendiz::class, 'caracterizacion_id');
    }
}
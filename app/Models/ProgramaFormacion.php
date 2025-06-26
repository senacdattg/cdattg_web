<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaFormacion extends Model
{
    protected $table = 'programas_formacion';
    protected $fillable = ['codigo', 'nombre', 'red_conocimiento_id', 'nivel_formacion_id'];

    use HasFactory;

    public function caracterizacionPrograma()
    {
        return $this->belongsTo(CaracterizacionPrograma::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function tipoPrograma()
    {
        return $this->belongsTo(TipoPrograma::class);
    }

    public function caracterizacionProgramas()
    {
        return $this->hasMany(CaracterizacionPrograma::class, 'programa_formacion_id');
    }

    public function competenciasProgramas()
    {
        return $this->hasMany(CompetenciaPrograma::class, 'programa_id');
    }

    public function competenciaActual()
    {
        foreach ($this->competenciasProgramas as $competenciaPrograma) {
            if (
                $competenciaPrograma->competencia->fecha_inicio <= now() &&
                $competenciaPrograma->competencia->fecha_fin >= now()
            ) {
                return $competenciaPrograma->competencia;
            }
        }
        return null;
    }
}

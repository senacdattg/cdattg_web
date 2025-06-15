<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaFormacion extends Model
{
    protected $table = 'programas_formacion';
    protected $fillable = ['codigo', 'nombre', 'red_conocimiento_id', 'nivel_formacion_id'];
    
    use HasFactory;

    public function ficha()
    {
        return $this->belongsTo(FichaCaracterizacion::class);
    }

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

}

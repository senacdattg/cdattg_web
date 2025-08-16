<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsistenciaAprendiz extends Model
{
    use HasFactory;

    protected $table = 'asistencia_aprendices';

    protected $fillable = [
        'instructor_ficha_id',
        'aprendiz_ficha_id',
        'evidencia_id',
        'hora_ingreso',
        'hora_salida',
    ];

    /**
     * Get the instructorFichaCaracterizacion that owns the AsistenciaAprendiz.
     */
    public function instructorFichaCaracterizacion(): BelongsTo
    {
        return $this->belongsTo(InstructorFichaCaracterizacion::class, 'instructor_ficha_id');
    }

    /**
     * Get the aprendizFicha that owns the AsistenciaAprendiz.
     */
    public function aprendizFicha(): BelongsTo
    {
        return $this->belongsTo(AprendizFicha::class, 'aprendiz_ficha_id');
    }

    public function caracterizacion()
    {
        return $this->belongsTo(CaracterizacionPrograma::class, 'caracterizacion_id');
    }

    public function ficha()
    {
        return $this->hasOneThrough(FichaCaracterizacion::class, CaracterizacionPrograma::class, 'id', 'id', 'caracterizacion_id', 'ficha_id');
    }

    public function instructor()
    {
        return $this->hasOneThrough(Instructor::class, CaracterizacionPrograma::class, 'id', 'id', 'caracterizacion_id', 'instructor_id');
    }

    public function programa (){
        return $this->hasOneThrough(ProgramaFormacion::class, 'id', 'id', 'caracterizacion_id', 'programa_formacion_id');
    }

    public function jornada()
    {
        return $this->hasOneThrough(JornadaFormacion::class, 'id', 'id', 'caracterizacion_id', 'programa_id');
    }

    public function sede(){
        return $this->hasOneThrough(Sede::class, 'id', 'id', 'caracterizacion_id', 'sede_id');
    }

}

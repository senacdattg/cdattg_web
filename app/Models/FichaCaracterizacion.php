<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaCaracterizacion extends Model
{
    use HasFactory;
    protected $table = 'fichas_caracterizacion';
    protected $fillable = [
        'programa_formacion_id',
        'ficha',
        'instructor_id',
        'fecha_inicio',
        'fecha_fin',
        'ambiente_id',
        'modalidad_formacion_id',
        'sede_id',
        'jornada_id',
        'total_horas',
        'user_create_id',
        'user_edit_id',
        'status'
    ];

    public function instructores()
    {
        return $this->hasMany(Instructor::class, 'instructor_id');
    }

    public function programaFormacion()
    {
        return $this->belongsTo(ProgramaFormacion::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function jornadaFormacion()
    {
        return $this->belongsTo(JornadaFormacion::class, 'jornada_id');
    }

    public function ambiente()
    {
        return $this->belongsTo(Ambiente::class, 'ambiente_id');
    }

    public function modalidadFormacion()
    {
        return $this->belongsTo(ModalidadFormacion::class, 'modalidad_formacion_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }
}

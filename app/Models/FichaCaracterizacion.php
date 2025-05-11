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
    ];

    public function instructores()
    {
        return $this->hasMany(Instructor::class, 'instructor_id');
    }

    public function programaFormacion()
    {
        return $this->belongsTo(ProgramaFormacion::class);
    }

    public function caracterizacionProgramas()
    {
        return $this->hasMany(CaracterizacionPrograma::class, 'ficha_id');
    }
}

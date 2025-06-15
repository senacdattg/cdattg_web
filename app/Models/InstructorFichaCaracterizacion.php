<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorFichaCaracterizacion extends Model
{
    protected $table = "instructor_fichas_caracterizacion";

    protected $fillable = [
        "instructor_id",
        "ficha_id",
        "fecha_inicio",
        "fecha_fin",
        "total_horas_ficha"
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function ficha()
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_id');
    }
}

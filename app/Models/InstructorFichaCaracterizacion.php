<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    /**
     * Get the asistenciaAprendices that owns the InstructorFichaCaracterizacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function asistenciaAprendices(): BelongsTo
    {
        return $this->belongsTo(asistenciaAprendices::class, 'instructor_ficha_id');
    }

    public function ficha(): BelongsTo
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_id');
    }
}

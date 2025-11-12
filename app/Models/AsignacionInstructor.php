<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AsignacionInstructor extends Model
{
    use HasFactory;

    protected $table = 'asignaciones_instructores';

    protected $fillable = [
        'ficha_id',
        'instructor_id',
        'competencia_id',
    ];

    public function ficha(): BelongsTo
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function competencia(): BelongsTo
    {
        return $this->belongsTo(Competencia::class, 'competencia_id');
    }

    public function resultadosAprendizaje(): BelongsToMany
    {
        return $this->belongsToMany(
            ResultadosAprendizaje::class,
            'asignacion_instructor_resultado',
            'asignacion_id',
            'resultado_aprendizaje_id'
        )->withTimestamps();
    }
}


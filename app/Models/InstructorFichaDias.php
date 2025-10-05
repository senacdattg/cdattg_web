<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorFichaDias extends Model
{
    protected $table = 'instructor_ficha_dias';

    protected $fillable = ['instructor_ficha_id', 'dia_id'];

    public function instructorFicha(): BelongsTo
    {
        return $this->belongsTo(InstructorFichaCaracterizacion::class, 'instructor_ficha_id');
    }

    public function dia(): BelongsTo
    {
        return $this->belongsTo(Parametro::class, 'dia_id');
    }
}
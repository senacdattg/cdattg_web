<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorFichaDias extends Model
{
    protected $table = 'instructor_ficha_dias';

    protected $fillable = ['instructor_ficha_id', 'dia_id', 'hora_inicio', 'hora_fin'];

    public function instructorFicha(): BelongsTo
    {
        return $this->belongsTo(InstructorFicha::class);
    }
}
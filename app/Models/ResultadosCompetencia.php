<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultadosCompetencia extends Model
{
    protected $table = 'resultados_aprendizaje_competencia';
    protected $fillable = [
        'rap_id',
        'competencia_id',
        'user_create_id',
        'user_edit_id',
    ];

    public function competencia()
    {
        return $this->belongsTo(Competencia::class);
    }

    public function rap()
    {
        return $this->belongsTo(ResultadosAprendizaje::class);
    }
}

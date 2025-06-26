<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetenciaPrograma extends Model
{
    protected $table = 'competencia_programa';
    protected $fillable = [
        'competencia_id',
        'programa_id',
        'user_create_id',
        'user_edit_id'
    ];

    public function competencia()
    {
        return $this->belongsTo(Competencia::class, 'competencia_id');
    }

    public function programa()
    {
        return $this->belongsTo(ProgramaFormacion::class, 'programa_id');
    }
}

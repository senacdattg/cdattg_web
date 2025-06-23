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
        'user_edit_id',
    ];
}

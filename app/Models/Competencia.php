<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competencia extends Model
{
    /** @use HasFactory<\Database\Factories\CompetenciaFactory> */
    use HasFactory;
    protected $table = 'competencias';
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'duracion',
        'user_create_id',
        'user_edit_id',
    ];
}

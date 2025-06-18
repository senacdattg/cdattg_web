<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultadosAprendizaje extends Model
{
    /** @use HasFactory<\Database\Factories\ResultadosAprendizajeFactory> */
    use HasFactory;
    protected $table = 'resultados_aprendizajes';
    protected $fillable = [
        'codigo',
        'nombre',
        'duracion',
        'fecha_inicio',
        'fecha_fin',
        'user_create_id',
        'user_edit_id',
    ];
}

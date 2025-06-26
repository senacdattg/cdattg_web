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
        'fecha_inicio',
        'fecha_fin',
        'descripcion',
        'duracion',
        'user_create_id',
        'user_edit_id',
    ];

    public function competenciaActual()
    {
        foreach ($this->competencia() as $competencia) {
            if ($competencia->fecha_inicio <= now() && $competencia->fecha_fin >= now()) {
                return $competencia;
            }
        }
        return null;
    }
}

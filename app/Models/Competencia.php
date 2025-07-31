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

    public function resultadosCompetencia()
    {
        return $this->hasMany(ResultadosCompetencia::class);
    }

    public function rapActual()
    {
        foreach ($this->resultadosCompetencia as $rap) {
            if ($rap->rap->fecha_inicio <= now() && $rap->rap->fecha_fin >= now()) {
                return $rap->rap;
            }
        }
        return null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ResultadosAprendizaje;

class GuiasAprendizaje extends Model
{
    protected $table = 'guia_aprendizajes';
    protected $fillable = [
        'id',
        'codigo',
        'nombre',
        'user_create_id',
        'user_edit_id',
    ];

    public function aprendizaje()
    {
        return $this->belongsTo(Aprendizaje::class);
    }

    /**
     * Relación muchos a muchos con ResultadosAprendizaje a través de la tabla intermedia
     */
    public function resultadosAprendizaje()
    {
        return $this->belongsToMany(ResultadosAprendizaje::class, 'guia_aprendizaje_rap', 'guia_aprendizaje_id', 'rap_id')
                    ->withPivot('user_create_id', 'user_edit_id')
                    ->withTimestamps();
    }

    /**
     * Relación con la tabla intermedia
     */
    public function guiaAprendizajeRap()
    {
        return $this->hasMany(GuiaAprendizajeRap::class, 'guia_aprendizaje_id');
    }

    public function actividades()
    {
        return $this->belongsToMany(Evidencias::class, 'evidencia_guia_aprendizaje', 'guia_aprendizaje_id', 'evidencia_id')
                    ->withPivot('user_create_id', 'user_edit_id')
                    ->withTimestamps();
    }
}

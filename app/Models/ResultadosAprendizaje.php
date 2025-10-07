<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GuiasAprendizaje;
use App\Models\User;

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
        'status',
        'user_create_id',
        'user_edit_id',
    ];

    protected $casts = [
        'duracion' => 'integer',
        'status' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación muchos a muchos con GuiasAprendizaje a través de la tabla intermedia
     */
    public function guiasAprendizaje()
    {
        return $this->belongsToMany(GuiasAprendizaje::class, 'guia_aprendizaje_rap', 'rap_id', 'guia_aprendizaje_id')
                    ->withPivot('user_create_id', 'user_edit_id')
                    ->withTimestamps();
    }

    /**
     * Relación con la tabla intermedia
     */
    public function guiaAprendizajeRap()
    {
        return $this->hasMany(GuiaAprendizajeRap::class, 'rap_id');
    }

    /**
     * Relación muchos a muchos con Competencia a través de la tabla intermedia
     */
    public function competencias()
    {
        return $this->belongsToMany(Competencia::class, 'resultados_aprendizaje_competencia', 'rap_id', 'competencia_id')
                    ->withPivot('user_create_id', 'user_edit_id')
                    ->withTimestamps();
    }

    /**
     * Relación con la tabla intermedia resultados_aprendizaje_competencia
     */
    public function resultadosCompetencia()
    {
        return $this->hasMany(ResultadosCompetencia::class, 'rap_id');
    }

    /**
     * Relación con el usuario que creó el resultado
     */
    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Relación con el usuario que editó el resultado
     */
    public function userEdit()
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }
}

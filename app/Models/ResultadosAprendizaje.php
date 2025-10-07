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

    /**
     * SCOPE: Filtrar resultados activos
     */
    public function scopeActivos($query)
    {
        return $query->where('status', 1);
    }

    /**
     * SCOPE: Filtrar resultados inactivos
     */
    public function scopeInactivos($query)
    {
        return $query->where('status', 0);
    }

    /**
     * SCOPE: Filtrar por competencia
     */
    public function scopePorCompetencia($query, $competenciaId)
    {
        return $query->whereHas('competencias', function($q) use ($competenciaId) {
            $q->where('competencias.id', $competenciaId);
        });
    }

    /**
     * SCOPE: Filtrar por código
     */
    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo', 'LIKE', "%{$codigo}%");
    }

    /**
     * SCOPE: Filtrar por fecha de inicio
     */
    public function scopePorFecha($query, $fechaInicio, $fechaFin = null)
    {
        $query->where('fecha_inicio', '>=', $fechaInicio);
        
        if ($fechaFin) {
            $query->where('fecha_fin', '<=', $fechaFin);
        }
        
        return $query;
    }

    /**
     * SCOPE: Ordenar por código ascendente
     */
    public function scopeOrdenadoPorCodigo($query)
    {
        return $query->orderBy('codigo', 'asc');
    }

    /**
     * MÉTODO HELPER: Verificar si el resultado está activo
     */
    public function isActivo(): bool
    {
        return $this->status == 1;
    }

    /**
     * MÉTODO HELPER: Obtener duración en horas
     */
    public function duracionEnHoras(): int
    {
        return $this->duracion ?? 0;
    }

    /**
     * MÉTODO HELPER: Verificar si tiene fechas definidas
     */
    public function tieneFechasDefinidas(): bool
    {
        return !is_null($this->fecha_inicio) && !is_null($this->fecha_fin);
    }

    /**
     * MÉTODO HELPER: Verificar si está vigente
     */
    public function estaVigente(): bool
    {
        if (!$this->tieneFechasDefinidas()) {
            return true;
        }

        $hoy = now();
        return $hoy->greaterThanOrEqualTo($this->fecha_inicio) && 
               $hoy->lessThanOrEqualTo($this->fecha_fin);
    }

    /**
     * MÉTODO HELPER: Contar guías asociadas
     */
    public function contarGuiasAsociadas(): int
    {
        return $this->guiasAprendizaje()->count();
    }

    /**
     * MÉTODO HELPER: Obtener estado formateado
     */
    public function getEstadoFormateadoAttribute(): string
    {
        return $this->status == 1 ? 'ACTIVO' : 'INACTIVO';
    }

    /**
     * MÉTODO HELPER: Obtener nombre completo con código
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->codigo} - {$this->nombre}";
    }
}

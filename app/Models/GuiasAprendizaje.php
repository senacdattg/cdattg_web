<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class GuiasAprendizaje extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'guia_aprendizajes';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'status',
        'user_create_id',
        'user_edit_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Boot method para configurar eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($guia) {
            $guia->status = $guia->status ?? 1;
        });

        static::saving(function ($guia) {
            if ($guia->codigo) {
                $guia->codigo = strtoupper($guia->codigo);
            }
            if ($guia->nombre) {
                $guia->nombre = strtoupper($guia->nombre);
            }
        });
    }

    /**
     * Relación con el usuario que creó la guía
     */
    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Relación con el usuario que editó la guía
     */
    public function userEdit()
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    /**
     * Relación muchos a muchos con ResultadosAprendizaje
     */
    public function resultadosAprendizaje()
    {
        return $this->belongsToMany(ResultadosAprendizaje::class, 'guia_aprendizaje_rap', 'guia_aprendizaje_id', 'rap_id')
                    ->withPivot('user_create_id', 'user_edit_id', 'es_obligatorio')
                    ->withTimestamps();
    }

    /**
     * Relación con la tabla intermedia GuiaAprendizajeRap
     */
    public function guiaAprendizajeRap()
    {
        return $this->hasMany(GuiaAprendizajeRap::class, 'guia_aprendizaje_id');
    }

    /**
     * Relación muchos a muchos con Evidencias (actividades)
     */
    public function actividades()
    {
        return $this->belongsToMany(Evidencias::class, 'evidencia_guia_aprendizaje', 'guia_aprendizaje_id', 'evidencia_id')
                ->withPivot('user_create_id', 'user_edit_id')
                ->withTimestamps()
                ->orderByRaw("FIELD(id_estado, '25', '27')")
                ->orderBy('fecha_evidencia', 'asc');
    }

    /**
     * Relación con Evidencias sin ordenamiento específico
     */
    public function evidencias()
    {
        return $this->belongsToMany(Evidencias::class, 'evidencia_guia_aprendizaje', 'guia_aprendizaje_id', 'evidencia_id')
                ->withPivot('user_create_id', 'user_edit_id')
                ->withTimestamps();
    }

    /**
     * SCOPE: Filtrar guías activas
     */
    public function scopeActivas($query)
    {
        return $query->where('status', 1);
    }

    /**
     * SCOPE: Filtrar guías inactivas
     */
    public function scopeInactivas($query)
    {
        return $query->where('status', 0);
    }

    /**
     * SCOPE: Filtrar por código
     */
    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo', 'LIKE', "%{$codigo}%");
    }

    /**
     * SCOPE: Filtrar por nombre
     */
    public function scopePorNombre($query, $nombre)
    {
        return $query->where('nombre', 'LIKE', "%{$nombre}%");
    }

    /**
     * SCOPE: Filtrar por usuario creador
     */
    public function scopePorUsuarioCreador($query, $userId)
    {
        return $query->where('user_create_id', $userId);
    }

    /**
     * SCOPE: Filtrar por fecha de creación
     */
    public function scopePorFechaCreacion($query, $fechaInicio, $fechaFin = null)
    {
        $query->whereDate('created_at', '>=', $fechaInicio);
        
        if ($fechaFin) {
            $query->whereDate('created_at', '<=', $fechaFin);
        }
        
        return $query;
    }

    /**
     * SCOPE: Ordenar por fecha de creación descendente
     */
    public function scopeMasRecientes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * SCOPE: Ordenar por nombre ascendente
     */
    public function scopePorNombreAsc($query)
    {
        return $query->orderBy('nombre', 'asc');
    }

    /**
     * MÉTODO HELPER: Verificar si la guía está activa
     */
    public function estaActiva(): bool
    {
        return $this->status == 1;
    }

    /**
     * MÉTODO HELPER: Contar resultados de aprendizaje asociados
     */
    public function contarResultadosAprendizaje(): int
    {
        return $this->resultadosAprendizaje()->count();
    }

    /**
     * MÉTODO HELPER: Contar actividades asociadas
     */
    public function contarActividades(): int
    {
        return $this->actividades()->count();
    }

    /**
     * MÉTODO HELPER: Contar evidencias asociadas
     */
    public function contarEvidencias(): int
    {
        return $this->evidencias()->count();
    }

    /**
     * MÉTODO HELPER: Obtener porcentaje de completitud de actividades
     */
    public function porcentajeCompletitud(): float
    {
        $totalActividades = $this->contarActividades();
        
        if ($totalActividades == 0) {
            return 0.0;
        }

        $actividadesCompletadas = $this->actividades()
            ->where('id_estado', '25') // Estado completado
            ->count();

        return round(($actividadesCompletadas / $totalActividades) * 100, 2);
    }

    /**
     * MÉTODO HELPER: Verificar si tiene actividades pendientes
     */
    public function tieneActividadesPendientes(): bool
    {
        return $this->actividades()
            ->where('id_estado', '27') // Estado pendiente
            ->exists();
    }

    /**
     * MÉTODO HELPER: Obtener días transcurridos desde creación
     */
    public function diasDesdeCreacion(): int
    {
        if (!$this->created_at) {
            return 0;
        }
        return $this->created_at->diffInDays(Carbon::now());
    }

    /**
     * MÉTODO HELPER: Obtener días transcurridos desde última actualización
     */
    public function diasDesdeUltimaActualizacion(): int
    {
        if (!$this->updated_at) {
            return 0;
        }
        return $this->updated_at->diffInDays(Carbon::now());
    }

    /**
     * MÉTODO HELPER: Formatear código para mostrar
     */
    public function getCodigoFormateadoAttribute(): string
    {
        return strtoupper($this->codigo);
    }

    /**
     * MÉTODO HELPER: Formatear nombre para mostrar
     */
    public function getNombreFormateadoAttribute(): string
    {
        return strtoupper($this->nombre);
    }

    /**
     * MÉTODO HELPER: Obtener estado formateado
     */
    public function getEstadoFormateadoAttribute(): string
    {
        return $this->status == 1 ? 'ACTIVO' : 'INACTIVO';
    }

    /**
     * MÉTODO HELPER: Verificar si puede ser eliminada
     */
    public function puedeSerEliminada(): bool
    {
        return $this->contarActividades() == 0;
    }

    /**
     * MÉTODO HELPER: Obtener resumen de la guía
     */
    public function obtenerResumen(): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'estado' => $this->getEstadoFormateadoAttribute(),
            'resultados_count' => $this->contarResultadosAprendizaje(),
            'actividades_count' => $this->contarActividades(),
            'evidencias_count' => $this->contarEvidencias(),
            'porcentaje_completitud' => $this->porcentajeCompletitud(),
            'dias_desde_creacion' => $this->diasDesdeCreacion(),
            'puede_eliminarse' => $this->puedeSerEliminada(),
        ];
    }
}

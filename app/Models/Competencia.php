<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competencia extends Model
{
    use HasFactory;
    
    protected $table = 'competencias';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'duracion',
        'fecha_inicio',
        'fecha_fin',
        'status',
        'user_create_id',
        'user_edit_id',
    ];

    protected $casts = [
        'duracion' => 'decimal:2',
        'status' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function resultadosCompetencia()
    {
        return $this->hasMany(ResultadosCompetencia::class);
    }

    public function resultadosAprendizaje()
    {
        return $this->belongsToMany(
            ResultadosAprendizaje::class,
            'resultados_aprendizaje_competencia',
            'competencia_id',
            'rap_id'
        )->withTimestamps()
         ->withPivot('user_create_id', 'user_edit_id');
    }

    public function asignacionesInstructor()
    {
        return $this->hasMany(AsignacionInstructor::class, 'competencia_id');
    }

    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function userEdit()
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    public function programasFormacion()
    {
        return $this->belongsToMany(
            ProgramaFormacion::class,
            'competencia_programa',
            'competencia_id',
            'programa_id'
        )->withTimestamps()
         ->withPivot('user_create_id', 'user_edit_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeActivos($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInactivos($query)
    {
        return $query->where('status', 0);
    }

    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo', $codigo);
    }

    public function scopePorFecha($query, $fechaInicio, $fechaFin)
    {
        return $query->where(function($q) use ($fechaInicio, $fechaFin) {
            $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
              ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin]);
        });
    }

    public function scopeVigentes($query)
    {
        return $query->where('fecha_inicio', '<=', now())
                     ->where('fecha_fin', '>=', now())
                     ->where('status', 1);
    }

    public function scopeOrdenadoPorCodigo($query)
    {
        return $query->orderBy('codigo', 'asc');
    }

    public function scopePorPrograma($query, $programaId)
    {
        return $query->whereHas('programasFormacion', function($q) use ($programaId) {
            $q->where('programas_formacion.id', $programaId);
        });
    }

    // ========================================
    // HELPERS / MÉTODOS AUXILIARES
    // ========================================

    public function isActivo()
    {
        return $this->status === 1 || $this->status === true;
    }

    public function duracionEnHoras()
    {
        return $this->duracion . ' horas';
    }

    public function tieneFechasDefinidas()
    {
        return !is_null($this->fecha_inicio) && !is_null($this->fecha_fin);
    }

    public function estaVigente()
    {
        if (!$this->tieneFechasDefinidas()) {
            return false;
        }
        
        $hoy = now();
        return $this->fecha_inicio <= $hoy && $this->fecha_fin >= $hoy;
    }

    public function contarRAPsAsociados()
    {
        return $this->resultadosAprendizaje()->count();
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

    // ========================================
    // ACCESSORS / ATRIBUTOS
    // ========================================

    public function getEstadoFormateadoAttribute()
    {
        return $this->status ? 'Activa' : 'Inactiva';
    }

    public function getNombreCompletoAttribute()
    {
        return $this->codigo . ' - ' . $this->nombre;
    }

    public function getDuracionFormateadaAttribute()
    {
        return number_format($this->duracion ?? 0, 0) . ' horas';
    }

    public function getFechaInicioFormateadaAttribute()
    {
        return $this->fecha_inicio ? $this->fecha_inicio->format('d/m/Y') : 'N/A';
    }

    public function getFechaFinFormateadaAttribute()
    {
        return $this->fecha_fin ? $this->fecha_fin->format('d/m/Y') : 'N/A';
    }
}

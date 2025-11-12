<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramaFormacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'programas_formacion';
    
    protected $fillable = [
        'codigo', 
        'nombre', 
        'red_conocimiento_id', 
        'nivel_formacion_id',
        'user_create_id',
        'user_edit_id',
        'status',
        'horas_totales',
        'horas_etapa_lectiva',
        'horas_etapa_productiva',
        'tipo_programa_id'
    ];

    protected $casts = [
        'status' => 'boolean',
        'horas_totales' => 'integer',
        'horas_etapa_lectiva' => 'integer',
        'horas_etapa_productiva' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($programa) {
            $programa->status = $programa->status ?? true;
        });

        static::saving(function ($programa) {
            $programa->nombre = strtoupper($programa->nombre);
            $programa->codigo = strtoupper($programa->codigo);
        });
    }

    // Relaciones de auditoría
    public function userCreated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function userEdited(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    // Relaciones principales
    public function redConocimiento(): BelongsTo
    {
        return $this->belongsTo(RedConocimiento::class, 'red_conocimiento_id');
    }

    public function nivelFormacion(): BelongsTo
    {
        return $this->belongsTo(Parametro::class, 'nivel_formacion_id');
    }

    public function tipoPrograma(): BelongsTo
    {
        return $this->belongsTo(TipoPrograma::class, 'tipo_programa_id');
    }

    // Relación indirecta con Regional a través de RedConocimiento
    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class, 'regionals_id')
            ->through('redConocimiento');
    }

    // Relaciones de caracterización y competencias
    public function fichasCaracterizacion(): HasMany
    {
        return $this->hasMany(FichaCaracterizacion::class, 'programa_formacion_id');
    }

    public function competenciasProgramas(): HasMany
    {
        return $this->hasMany(CompetenciaPrograma::class, 'programa_id');
    }

    public function competencias(): BelongsToMany
    {
        return $this->belongsToMany(
            Competencia::class,
            'competencia_programa',
            'programa_id',
            'competencia_id'
        )->withTimestamps()
         ->withPivot('user_create_id', 'user_edit_id');
    }

    public function competenciaActual()
    {
        foreach ($this->competenciasProgramas as $competenciaPrograma) {
            if (
                $competenciaPrograma->competencia->fecha_inicio <= now() &&
                $competenciaPrograma->competencia->fecha_fin >= now()
            ) {
                return $competenciaPrograma->competencia;
            }
        }
        return null;
    }

    // Scopes para filtros comunes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function scopeByRedConocimiento($query, $redConocimientoId)
    {
        return $query->where('red_conocimiento_id', $redConocimientoId);
    }

    public function scopeByNivelFormacion($query, $nivelFormacionId)
    {
        return $query->where('nivel_formacion_id', $nivelFormacionId);
    }

    public function scopeByRegional($query, $regionalId)
    {
        return $query->whereHas('redConocimiento', function ($q) use ($regionalId) {
            $q->where('regionals_id', $regionalId);
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nombre', 'LIKE', "%{$search}%")
              ->orWhere('codigo', 'LIKE', "%{$search}%")
              ->orWhereHas('redConocimiento', function ($subQuery) use ($search) {
                  $subQuery->where('nombre', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('nivelFormacion', function ($subQuery) use ($search) {
                  $subQuery->where('name', 'LIKE', "%{$search}%");
              });
        });
    }
}

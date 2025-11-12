<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Models\AsignacionInstructor;

class Instructor extends Model
{
    use HasFactory;

    protected $table = 'instructors';

    protected $fillable = [
        'persona_id',
        'regional_id',
        'status',
        'user_create_id',
        'user_edit_id',
        'especialidades',
        'competencias',
        'anos_experiencia',
        'experiencia_laboral',
        'numero_documento_cache',
        'nombre_completo_cache'
    ];

    protected $casts = [
        'status' => 'boolean',
        'especialidades' => 'array',
        'competencias' => 'array',
        'anos_experiencia' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($instructor) {
            $instructor->status = $instructor->status ?? true;
            $instructor->actualizarCache();
        });

        static::updating(function ($instructor) {
            $instructor->actualizarCache();
        });
    }

    /**
     * Relación con Persona (belongsTo)
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    /**
     * Relación con Regional (belongsTo)
     */
    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class, 'regional_id');
    }

    /**
     * Relación con FichaCaracterizacion (hasMany)
     */
    public function fichas(): HasMany
    {
        return $this->hasMany(FichaCaracterizacion::class, 'instructor_id');
    }

    /**
     * Relación con InstructorFichaCaracterizacion (hasMany)
     */
    public function instructorFichas(): HasMany
    {
        return $this->hasMany(InstructorFichaCaracterizacion::class, 'instructor_id');
    }

    public function asignacionesInstructor(): HasMany
    {
        return $this->hasMany(AsignacionInstructor::class, 'instructor_id');
    }


    /**
     * Relación con User (hasOne)
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'persona_id', 'persona_id');
    }

    /**
     * Relación con el usuario que creó el instructor
     */
    public function userCreated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Relación con el usuario que editó el instructor
     */
    public function userEdited(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    /**
     * Relación con EntradaSalida (hasMany)
     */
    public function entradaSalidas(): HasMany
    {
        return $this->hasMany(EntradaSalida::class, 'instructor_user_id', 'persona_id');
    }

    // SCOPES

    /**
     * Scope para instructores activos
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope para instructores inactivos
     */
    public function scopeInactivos(Builder $query): Builder
    {
        return $query->where('status', false);
    }

    /**
     * Scope para filtrar por regional
     */
    public function scopePorRegional(Builder $query, int $regionalId): Builder
    {
        return $query->where('regional_id', $regionalId);
    }

    /**
     * Scope para buscar por nombre o documento
     */
    public function scopeBuscar(Builder $query, string $termino): Builder
    {
        return $query->whereHas('persona', function ($q) use ($termino) {
            $q->where('primer_nombre', 'like', "%{$termino}%")
              ->orWhere('segundo_nombre', 'like', "%{$termino}%")
              ->orWhere('primer_apellido', 'like', "%{$termino}%")
              ->orWhere('segundo_apellido', 'like', "%{$termino}%")
              ->orWhere('numero_documento', 'like', "%{$termino}%")
              ->orWhere('email', 'like', "%{$termino}%");
        });
    }

    /**
     * Scope para instructores con fichas asignadas
     */
    public function scopeConFichas(Builder $query): Builder
    {
        return $query->whereHas('fichas');
    }

    /**
     * Scope para instructores sin fichas asignadas
     */
    public function scopeSinFichas(Builder $query): Builder
    {
        return $query->whereDoesntHave('fichas');
    }

    /**
     * Scope para ordenar por nombre completo
     */
    public function scopeOrdenarPorNombre(Builder $query, string $direccion = 'asc'): Builder
    {
        return $query->join('personas', 'instructors.persona_id', '=', 'personas.id')
                     ->orderBy('personas.primer_nombre', $direccion)
                     ->orderBy('personas.primer_apellido', $direccion)
                     ->select('instructors.*');
    }

    // MÉTODOS HELPER

    /**
     * Obtener el nombre completo del instructor
     */
    public function getNombreCompletoAttribute(): string
    {
        $persona = $this->persona;
        if (!$persona) {
            return 'Sin nombre';
        }

        $nombre = $persona->primer_nombre;
        if ($persona->segundo_nombre) {
            $nombre .= ' ' . $persona->segundo_nombre;
        }
        $nombre .= ' ' . $persona->primer_apellido;
        if ($persona->segundo_apellido) {
            $nombre .= ' ' . $persona->segundo_apellido;
        }

        return $nombre;
    }

    /**
     * Obtener el número de documento del instructor
     */
    public function getNumeroDocumentoAttribute(): string
    {
        return $this->persona ? $this->persona->numero_documento : 'Sin documento';
    }

    /**
     * Obtener el email del instructor
     */
    public function getEmailAttribute(): string
    {
        return $this->persona ? $this->persona->email : 'Sin email';
    }

    /**
     * Calcular el total de horas asignadas al instructor
     */
    public function getTotalHorasAsignadasAttribute(): int
    {
        return $this->instructorFichas()->sum('total_horas_instructor') ?? 0;
    }

    /**
     * Obtener el número de fichas asignadas
     */
    public function getNumeroFichasAsignadasAttribute(): int
    {
        return $this->fichas()->count();
    }

    /**
     * Verificar si el instructor tiene fichas activas
     */
    public function tieneFichasActivas(): bool
    {
        return $this->fichas()->where('status', true)->exists();
    }

    /**
     * Obtener las fichas activas del instructor
     */
    public function fichasActivas()
    {
        return $this->fichas()->where('status', true);
    }

    /**
     * Calcular la edad del instructor
     */
    public function getEdadAttribute(): int
    {
        if (!$this->persona || !$this->persona->fecha_de_nacimiento) {
            return 0;
        }

        return Carbon::parse($this->persona->fecha_de_nacimiento)->age;
    }

    /**
     * Verificar si el instructor está disponible para nuevas asignaciones
     */
    public function estaDisponible(): bool
    {
        return $this->status && !$this->tieneFichasActivas();
    }

    /**
     * Obtener el estado formateado
     */
    public function getEstadoFormateadoAttribute(): string
    {
        return $this->status ? 'Activo' : 'Inactivo';
    }

    /**
     * Obtener la fecha de creación formateada
     */
    public function getFechaCreacionFormateadaAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : 'Sin fecha';
    }

    /**
     * Obtener la fecha de actualización formateada
     */
    public function getFechaActualizacionFormateadaAttribute(): string
    {
        return $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : 'Sin fecha';
    }

    /**
     * Actualizar campos de caché para optimizar búsquedas
     */
    public function actualizarCache(): void
    {
        if ($this->persona) {
            $this->numero_documento_cache = $this->persona->numero_documento;
            $this->nombre_completo_cache = $this->getNombreCompletoAttribute();
        }
    }

    /**
     * Agregar especialidad al instructor
     */
    public function agregarEspecialidad(string $especialidad): void
    {
        $especialidades = $this->especialidades ?? [];
        if (!in_array($especialidad, $especialidades)) {
            $especialidades[] = $especialidad;
            $this->especialidades = $especialidades;
            $this->save();
        }
    }

    /**
     * Remover especialidad del instructor
     */
    public function removerEspecialidad(string $especialidad): void
    {
        $especialidades = $this->especialidades ?? [];
        $especialidades = array_filter($especialidades, function($esp) use ($especialidad) {
            return $esp !== $especialidad;
        });
        $this->especialidades = array_values($especialidades);
        $this->save();
    }

    /**
     * Agregar competencia al instructor
     */
    public function agregarCompetencia(string $competencia): void
    {
        $competencias = $this->competencias ?? [];
        if (!in_array($competencia, $competencias)) {
            $competencias[] = $competencia;
            $this->competencias = $competencias;
            $this->save();
        }
    }

    /**
     * Remover competencia del instructor
     */
    public function removerCompetencia(string $competencia): void
    {
        $competencias = $this->competencias ?? [];
        $competencias = array_filter($competencias, function($comp) use ($competencia) {
            return $comp !== $competencia;
        });
        $this->competencias = array_values($competencias);
        $this->save();
    }

    /**
     * Verificar si el instructor tiene una especialidad específica
     */
    public function tieneEspecialidad(string $especialidad): bool
    {
        return in_array($especialidad, $this->especialidades ?? []);
    }

    /**
     * Verificar si el instructor tiene una competencia específica
     */
    public function tieneCompetencia(string $competencia): bool
    {
        return in_array($competencia, $this->competencias ?? []);
    }

    /**
     * Obtener años de experiencia formateados
     */
    public function getAnosExperienciaFormateadosAttribute(): string
    {
        if (!$this->anos_experiencia) {
            return 'Sin especificar';
        }
        
        return $this->anos_experiencia . ' año' . ($this->anos_experiencia > 1 ? 's' : '');
    }

    /**
     * Scope para buscar por especialidad
     */
    public function scopePorEspecialidad(Builder $query, string $especialidad): Builder
    {
        return $query->whereJsonContains('especialidades', $especialidad);
    }

    /**
     * Scope para buscar por competencia
     */
    public function scopePorCompetencia(Builder $query, string $competencia): Builder
    {
        return $query->whereJsonContains('competencias', $competencia);
    }

    /**
     * Scope para filtrar por años de experiencia mínimos
     */
    public function scopeConExperienciaMinima(Builder $query, int $anosMinimos): Builder
    {
        return $query->where('anos_experiencia', '>=', $anosMinimos);
    }
}

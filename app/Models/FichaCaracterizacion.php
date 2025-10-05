<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class FichaCaracterizacion extends Model
{
    use HasFactory;
    
    protected $table = 'fichas_caracterizacion';
    
    protected $fillable = [
        'programa_formacion_id',
        'ficha',
        'instructor_id',
        'fecha_inicio',
        'fecha_fin',
        'ambiente_id',
        'modalidad_formacion_id',
        'sede_id',
        'jornada_id',
        'total_horas',
        'user_create_id',
        'user_edit_id',
        'status'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'status' => 'boolean',
        'total_horas' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'fecha_inicio',
        'fecha_fin',
        'created_at',
        'updated_at',
    ];

    /**
     * Relación con el instructor principal de la ficha (Many-to-One).
     * Esto se basa en la columna `instructor_id` en `fichas_caracterizacion`.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    /**
     * Get the instructorFicha that owns the FichaCaracterizacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function instructorFicha(): hasMany
    {
        return $this->hasMany(InstructorFichaCaracterizacion::class, 'ficha_id');
    }

    /**
     * Relación con los programas de formación.
     */
    public function programaFormacion(): BelongsTo
    {
        return $this->belongsTo(ProgramaFormacion::class);
    }

    /**
     * Relación con la jornada de formación.
     */
    public function jornadaFormacion(): BelongsTo
    {
        return $this->belongsTo(JornadaFormacion::class, 'jornada_id');
    }

    /**
     * Relación con el ambiente.
     */
    public function ambiente(): BelongsTo
    {
        return $this->belongsTo(Ambiente::class, 'ambiente_id');
    }

    /**
     * Relación con la modalidad de formación.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function modalidadFormacion(): BelongsTo
    {
        return $this->belongsTo(Parametro::class, 'modalidad_formacion_id');
    }

    /**
     * Relación con la sede.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    /**
     * Relación con el usuario que creó la ficha.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuarioCreacion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Relación con el usuario que editó la ficha.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuarioEdicion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    /**
     * Relación con los días de formación (tabla intermedia `ficha_dias_formacion`).
     */
    public function diasFormacion(): HasMany
    {
        return $this->hasMany(FichaDiasFormacion::class, 'ficha_id', 'id');
    }

    /**
     * Relación Muchos a Muchos con instructores a través de la tabla intermedia.
     * Esta es la relación que describe la tabla 'instructor_fichas_caracterizacion'.
     */
    public function instructorAsignado(): belongsTo
    {
        return $this->belongsTo(InstructorFichaCaracterizacion::class, 'ficha_id');
    }

    /**
     * Relación con la tabla pivot AprendizFicha.
     * Obtiene todos los registros de la tabla intermedia.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aprendicesFicha(): HasMany
    {
        return $this->hasMany(AprendizFicha::class, 'ficha_id', 'id');
    }

    /**
     * Relación Many-to-Many con Aprendiz.
     * Obtiene directamente los aprendices asignados a esta ficha.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function aprendices(): BelongsToMany
    {
        return $this->belongsToMany(
            Aprendiz::class,
            'aprendiz_fichas_caracterizacion',
            'ficha_id',
            'aprendiz_id'
        )->withTimestamps();
    }

    /**
     * Relación Many-to-Many con Persona.
     * Obtiene directamente las personas asignadas a esta ficha como aprendices.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function personas(): BelongsToMany
    {
        return $this->belongsToMany(
            Persona::class,
            'aprendiz_fichas_caracterizacion',
            'ficha_id',
            'persona_id'
        )->withTimestamps();
    }

    /**
     * Obtiene solo los aprendices activos de esta ficha.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function aprendicesActivos(): BelongsToMany
    {
        return $this->aprendices()->where('aprendices.estado', 1);
    }

    /**
     * Obtiene el conteo total de aprendices en esta ficha.
     *
     * @return int
     */
    public function contarAprendices(): int
    {
        return $this->aprendices()->count();
    }

    /**
     * Obtiene el conteo de aprendices activos en esta ficha.
     *
     * @return int
     */
    public function contarAprendicesActivos(): int
    {
        return $this->aprendicesActivos()->count();
    }

    /**
     * Verifica si la ficha tiene aprendices asignados.
     *
     * @return bool
     */
    public function tieneAprendices(): bool
    {
        return $this->aprendices()->exists();
    }

    /**
     * Verifica si un aprendiz específico pertenece a esta ficha.
     *
     * @param int $aprendizId
     * @return bool
     */
    public function tieneAprendiz(int $aprendizId): bool
    {
        return $this->aprendices()->where('aprendices.id', $aprendizId)->exists();
    }

    // ==================== SCOPES ====================

    /**
     * Scope para filtrar fichas activas.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope para filtrar fichas inactivas.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactivas(Builder $query): Builder
    {
        return $query->where('status', false);
    }

    /**
     * Scope para filtrar fichas por programa de formación.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $programaId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorPrograma(Builder $query, int $programaId): Builder
    {
        return $query->where('programa_formacion_id', $programaId);
    }

    /**
     * Scope para filtrar fichas por sede.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $sedeId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorSede(Builder $query, int $sedeId): Builder
    {
        return $query->where('sede_id', $sedeId);
    }

    /**
     * Scope para filtrar fichas por instructor.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $instructorId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorInstructor(Builder $query, int $instructorId): Builder
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Scope para filtrar fichas por modalidad de formación.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $modalidadId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorModalidad(Builder $query, int $modalidadId): Builder
    {
        return $query->where('modalidad_formacion_id', $modalidadId);
    }

    /**
     * Scope para filtrar fichas por jornada.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $jornadaId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorJornada(Builder $query, int $jornadaId): Builder
    {
        return $query->where('jornada_id', $jornadaId);
    }

    /**
     * Scope para filtrar fichas que tienen aprendices.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConAprendices(Builder $query): Builder
    {
        return $query->has('aprendices');
    }

    /**
     * Scope para filtrar fichas sin aprendices.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSinAprendices(Builder $query): Builder
    {
        return $query->doesntHave('aprendices');
    }

    /**
     * Scope para filtrar fichas por rango de fechas.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorRangoFechas(Builder $query, string $fechaInicio, string $fechaFin): Builder
    {
        return $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                    ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                    ->orWhere(function ($q) use ($fechaInicio, $fechaFin) {
                        $q->where('fecha_inicio', '<=', $fechaInicio)
                          ->where('fecha_fin', '>=', $fechaFin);
                    });
    }

    /**
     * Scope para filtrar fichas que están en curso actualmente.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnCurso(Builder $query): Builder
    {
        $hoy = Carbon::today();
        return $query->where('fecha_inicio', '<=', $hoy)
                    ->where('fecha_fin', '>=', $hoy);
    }

    /**
     * Scope para filtrar fichas que han terminado.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTerminadas(Builder $query): Builder
    {
        return $query->where('fecha_fin', '<', Carbon::today());
    }

    /**
     * Scope para filtrar fichas que están por iniciar.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorIniciar(Builder $query): Builder
    {
        return $query->where('fecha_inicio', '>', Carbon::today());
    }

    // ==================== MÉTODOS HELPER ====================

    /**
     * Calcula la duración de la ficha en días.
     *
     * @return int
     */
    public function duracionEnDias(): int
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return 0;
        }

        return $this->fecha_inicio->diffInDays($this->fecha_fin) + 1;
    }

    /**
     * Calcula la duración de la ficha en meses.
     *
     * @return int
     */
    public function duracionEnMeses(): int
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return 0;
        }

        return $this->fecha_inicio->diffInMonths($this->fecha_fin);
    }

    /**
     * Calcula las horas promedio por día.
     *
     * @return float
     */
    public function horasPromedioPorDia(): float
    {
        $duracionDias = $this->duracionEnDias();
        
        if ($duracionDias === 0 || !$this->total_horas) {
            return 0;
        }

        return round($this->total_horas / $duracionDias, 2);
    }

    /**
     * Verifica si la ficha está en curso actualmente.
     *
     * @return bool
     */
    public function estaEnCurso(): bool
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return false;
        }

        $hoy = Carbon::today();
        return $this->fecha_inicio <= $hoy && $this->fecha_fin >= $hoy;
    }

    /**
     * Verifica si la ficha ya terminó.
     *
     * @return bool
     */
    public function yaTermino(): bool
    {
        if (!$this->fecha_fin) {
            return false;
        }

        return $this->fecha_fin < Carbon::today();
    }

    /**
     * Verifica si la ficha está por iniciar.
     *
     * @return bool
     */
    public function estaPorIniciar(): bool
    {
        if (!$this->fecha_inicio) {
            return false;
        }

        return $this->fecha_inicio > Carbon::today();
    }

    /**
     * Calcula el porcentaje de avance de la ficha.
     *
     * @return float
     */
    public function porcentajeAvance(): float
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return 0;
        }

        $hoy = Carbon::today();
        $duracionTotal = $this->fecha_inicio->diffInDays($this->fecha_fin);
        
        if ($duracionTotal === 0) {
            return 100;
        }

        $diasTranscurridos = $this->fecha_inicio->diffInDays($hoy);
        $porcentaje = ($diasTranscurridos / $duracionTotal) * 100;

        return min(100, max(0, round($porcentaje, 2)));
    }

    /**
     * Obtiene el estado textual de la ficha.
     *
     * @return string
     */
    public function obtenerEstadoTexto(): string
    {
        if (!$this->status) {
            return 'Inactiva';
        }

        if ($this->estaPorIniciar()) {
            return 'Por iniciar';
        }

        if ($this->estaEnCurso()) {
            return 'En curso';
        }

        if ($this->yaTermino()) {
            return 'Terminada';
        }

        return 'Desconocido';
    }

    /**
     * Obtiene información resumida de la ficha.
     *
     * @return array
     */
    public function obtenerResumen(): array
    {
        return [
            'id' => $this->id,
            'numero_ficha' => $this->ficha,
            'programa' => $this->programaFormacion->nombre ?? 'N/A',
            'instructor' => $this->instructor ? 
                $this->instructor->persona->primer_nombre . ' ' . $this->instructor->persona->primer_apellido : 'N/A',
            'sede' => $this->sede->nombre ?? 'N/A',
            'modalidad' => $this->modalidadFormacion->name ?? 'N/A',
            'fecha_inicio' => $this->fecha_inicio?->format('d/m/Y'),
            'fecha_fin' => $this->fecha_fin?->format('d/m/Y'),
            'duracion_dias' => $this->duracionEnDias(),
            'total_horas' => $this->total_horas ?? 0,
            'aprendices_count' => $this->contarAprendices(),
            'estado' => $this->obtenerEstadoTexto(),
            'porcentaje_avance' => $this->porcentajeAvance(),
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Aprendiz extends Model
{
    use HasFactory;

    protected $table = 'aprendices';

    /**
     * Los atributos asignables.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'persona_id',
        'ficha_caracterizacion_id',
        'estado',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Relación con Persona (Many-to-One).
     * Un aprendiz pertenece a una persona.
     *
     * @return BelongsTo
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    /**
     * Relación directa con FichaCaracterizacion (Many-to-One).
     * Un aprendiz puede tener una ficha principal asignada.
     *
     * @return BelongsTo
     */
    public function fichaCaracterizacion(): BelongsTo
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_caracterizacion_id');
    }

    /**
     * Relación con AprendizFicha (One-to-Many).
     * Un aprendiz puede estar asignado a múltiples fichas.
     *
     * @return HasMany
     */
    public function aprendizFichas(): HasMany
    {
        return $this->hasMany(AprendizFicha::class, 'aprendiz_id');
    }

    /**
     * Relación Many-to-Many con FichaCaracterizacion a través de tabla pivot.
     * Un aprendiz puede pertenecer a múltiples fichas de caracterización.
     *
     * @return BelongsToMany
     */
    public function fichasCaracterizacion(): BelongsToMany
    {
        return $this->belongsToMany(
            FichaCaracterizacion::class,
            'aprendiz_fichas_caracterizacion',
            'aprendiz_id',
            'ficha_id'
        )->withTimestamps();
    }

    /**
     * Relación HasManyThrough con AsistenciaAprendiz.
     * Un aprendiz tiene múltiples asistencias a través de AprendizFicha.
     *
     * @return HasManyThrough
     */
    public function asistencias(): HasManyThrough
    {
        return $this->hasManyThrough(
            AsistenciaAprendiz::class,
            AprendizFicha::class,
            'aprendiz_id',        // FK en aprendiz_fichas_caracterizacion
            'aprendiz_ficha_id',  // FK en asistencia_aprendices
            'id',                 // PK en aprendices
            'id'                  // PK en aprendiz_fichas_caracterizacion
        );
    }
}

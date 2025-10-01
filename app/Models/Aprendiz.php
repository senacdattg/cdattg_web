<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aprendiz extends Model
{
    use HasFactory, SoftDeletes;

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
        'user_create_id',
        'user_edit_id',
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
     * Relación con FichaCaracterizacion (Many-to-One).
     * Un aprendiz pertenece a una única ficha de caracterización.
     *
     * @return BelongsTo
     */
    public function fichaCaracterizacion(): BelongsTo
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_caracterizacion_id');
    }

    /**
     * Relación con AprendizFicha (One-to-Many).
     * Un aprendiz tiene un registro en la tabla intermedia para asistencias.
     *
     * @return HasMany
     */
    public function aprendizFichas(): HasMany
    {
        return $this->hasMany(AprendizFicha::class, 'aprendiz_id');
    }

    /**
     * Relación con AsistenciaAprendiz a través de AprendizFicha.
     * Un aprendiz tiene múltiples asistencias.
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

    /**
     * Relación con el usuario que creó el registro.
     *
     * @return BelongsTo
     */
    public function userCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Relación con el usuario que editó el registro por última vez.
     *
     * @return BelongsTo
     */
    public function userUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }
}

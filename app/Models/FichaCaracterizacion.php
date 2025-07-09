<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        return $this->hasMany(InstructorFichaCaracterizaion::class, 'ficha_id');
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
     * The modalidadFormacion that belong to the FichaCaracterizacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function modalidadFormacion()
    {
        return $this->belongsTo(Parametro::class, 'modalidad_formacion_id');
    }

    /**
     * Relación con la sede.
     */
    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
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
}

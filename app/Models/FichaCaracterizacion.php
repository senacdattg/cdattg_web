<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function instructorFicha(): BelongsTo
    {
        return $this->belongsTo(InstructorFichaCaracterizaion::class, 'ficha_id');
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
     */
    public function modalidadFormacion(): BelongsTo
    {
        return $this->belongsTo(ModalidadFormacion::class, 'modalidad_formacion_id');
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
     * Obtiene el horario del día actual
     */
    public function getHorarioHoy()
    {
        $diaHoy = now()->dayOfWeek; // 0 = Domingo, 1 = Lunes, etc.
        $diaId = $diaHoy == 0 ? 18 : $diaHoy + 11; // Domingo = 18, Lunes = 12, etc.

        return $this->diasFormacion()
            ->where('dia_id', $diaId)
            ->first();
    }

    /**
     * Obtiene el horario de un día específico
     */
    public function getHorarioDia($diaId)
    {
        return $this->diasFormacion()
            ->where('dia_id', $diaId)
            ->first();
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

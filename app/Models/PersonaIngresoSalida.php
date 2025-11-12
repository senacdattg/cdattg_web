<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PersonaIngresoSalida extends Model
{
    use HasFactory;

    protected $table = 'persona_ingreso_salida';

    protected $fillable = [
        'persona_id',
        'sede_id',
        'tipo_persona',
        'fecha_entrada',
        'hora_entrada',
        'timestamp_entrada',
        'fecha_salida',
        'hora_salida',
        'timestamp_salida',
        'ambiente_id',
        'ficha_caracterizacion_id',
        'observaciones',
        'user_create_id',
        'user_edit_id',
    ];

    protected $casts = [
        'fecha_entrada' => 'date',
        'fecha_salida' => 'date',
        'timestamp_entrada' => 'datetime',
        'timestamp_salida' => 'datetime',
    ];

    /**
     * Relación con Persona
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    /**
     * Relación con Sede
     */
    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    /**
     * Relación con Ambiente
     */
    public function ambiente(): BelongsTo
    {
        return $this->belongsTo(Ambiente::class, 'ambiente_id');
    }

    /**
     * Relación con FichaCaracterizacion
     */
    public function fichaCaracterizacion(): BelongsTo
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_caracterizacion_id');
    }

    /**
     * Relación con User que creó el registro
     */
    public function userCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Relación con User que editó el registro
     */
    public function userUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    /**
     * Verifica si la persona está dentro (tiene entrada sin salida)
     */
    public function estaDentro(): bool
    {
        return is_null($this->timestamp_salida);
    }

    /**
     * Calcula el tiempo que la persona ha estado dentro
     * Retorna null si aún no ha salido
     */
    public function tiempoDentro(): ?int
    {
        if ($this->estaDentro()) {
            return Carbon::now()->diffInMinutes($this->timestamp_entrada);
        }

        if ($this->timestamp_salida && $this->timestamp_entrada) {
            return $this->timestamp_salida->diffInMinutes($this->timestamp_entrada);
        }

        return null;
    }

    /**
     * Scope para personas que están dentro actualmente
     */
    public function scopeDentro($query)
    {
        return $query->whereNull('timestamp_salida');
    }

    /**
     * Scope para filtrar por tipo de persona
     */
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo_persona', $tipo);
    }

    /**
     * Scope para filtrar por sede
     */
    public function scopePorSede($query, int $sedeId)
    {
        return $query->where('sede_id', $sedeId);
    }

    /**
     * Scope para filtrar por fecha
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha_entrada', $fecha);
    }

    /**
     * Scope para personas que entraron hoy
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_entrada', Carbon::today());
    }
}


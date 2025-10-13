<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SofiaValidationProgress extends Model
{
    protected $table = 'sofia_validation_progress';

    protected $fillable = [
        'complementario_id',
        'user_id',
        'status',
        'total_aspirantes',
        'processed_aspirantes',
        'successful_validations',
        'failed_validations',
        'errors',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'errors' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relación con el programa complementario
     */
    public function complementario()
    {
        return $this->belongsTo(ComplementarioOfertado::class, 'complementario_id');
    }

    /**
     * Relación con el usuario que inició la validación
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calcular el porcentaje de progreso
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->total_aspirantes === 0) {
            return 0;
        }

        return round(($this->processed_aspirantes / $this->total_aspirantes) * 100, 2);
    }

    /**
     * Obtener el estado legible
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pendiente',
            'processing' => 'Procesando',
            'completed' => 'Completado',
            'failed' => 'Fallido',
            default => 'Desconocido'
        };
    }

    /**
     * Marcar como iniciado
     */
    public function markAsStarted()
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    /**
     * Marcar como completado
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Marcar como fallido
     */
    public function markAsFailed($errors = [])
    {
        $this->update([
            'status' => 'failed',
            'errors' => $errors,
            'completed_at' => now(),
        ]);
    }

    /**
     * Incrementar contador de procesados
     */
    public function incrementProcessed($successful = true)
    {
        $this->increment('processed_aspirantes');

        if ($successful) {
            $this->increment('successful_validations');
        } else {
            $this->increment('failed_validations');
        }
    }
}

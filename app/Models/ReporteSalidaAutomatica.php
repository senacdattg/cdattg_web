<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporteSalidaAutomatica extends Model
{
    use HasFactory;

    protected $table = 'reporte_salida_automatica';

    protected $fillable = [
        'fecha_procesamiento',
        'hora_procesamiento',
        'total_salidas_procesadas',
        'detalle',
        'user_id',
    ];

    protected $casts = [
        'fecha_procesamiento' => 'date',
        'detalle' => 'array',
        'total_salidas_procesadas' => 'integer',
    ];

    /**
     * Relación con el usuario que procesó el reporte
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obtiene el detalle formateado
     */
    public function getDetalleFormateadoAttribute(): array
    {
        return $this->detalle ?? [];
    }
}

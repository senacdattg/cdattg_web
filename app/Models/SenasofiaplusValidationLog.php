<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SenasofiaplusValidationLog extends Model
{
    use HasFactory;

    protected $table = 'senasofiaplus_validation_logs';

    protected $fillable = [
        'aspirante_id',
        'accion',
        'detalles',
        'resultado',
        'mensaje',
        'user_id',
        'fecha_accion',
        'datos_anteriores',
        'datos_nuevos',
    ];

    protected $casts = [
        'detalles' => 'array',
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'fecha_accion' => 'datetime',
    ];

    /**
     * Relación con aspirante complementario
     */
    public function aspirante(): BelongsTo
    {
        return $this->belongsTo(AspiranteComplementario::class, 'aspirante_id');
    }

    /**
     * Relación con usuario que realizó la acción
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Crear log de validación
     */
    public static function crearLog(array $datos): self
    {
        return self::create([
            'aspirante_id' => $datos['aspirante_id'],
            'accion' => $datos['accion'] ?? 'validar',
            'detalles' => $datos['detalles'] ?? null,
            'resultado' => $datos['resultado'],
            'mensaje' => $datos['mensaje'],
            'user_id' => $datos['user_id'] ?? 1, // Bot user por defecto
            'fecha_accion' => $datos['fecha_accion'] ?? now(),
            'datos_anteriores' => $datos['datos_anteriores'] ?? null,
            'datos_nuevos' => $datos['datos_nuevos'] ?? null,
        ]);
    }

    /**
     * Obtener logs por aspirante
     */
    public static function getLogsPorAspirante(int $aspiranteId, int $limit = 50)
    {
        return self::where('aspirante_id', $aspiranteId)
            ->with(['user'])
            ->orderBy('fecha_accion', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener estadísticas de validaciones
     */
    public static function getEstadisticasValidaciones(string $fechaInicio, string $fechaFin): array
    {
        $logs = self::whereBetween('fecha_accion', [$fechaInicio, $fechaFin])->get();

        return [
            'total_validaciones' => $logs->count(),
            'exitosas' => $logs->where('resultado', 'exitoso')->count(),
            'errores' => $logs->where('resultado', 'error')->count(),
            'advertencias' => $logs->where('resultado', 'advertencia')->count(),
            'tasa_exito' => $logs->count() > 0 ? round(($logs->where('resultado', 'exitoso')->count() / $logs->count()) * 100, 2) : 0,
        ];
    }
}
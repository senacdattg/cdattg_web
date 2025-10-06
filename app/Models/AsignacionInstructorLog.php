<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AsignacionInstructorLog extends Model
{
    protected $table = 'asignacion_instructor_logs';

    protected $fillable = [
        'instructor_id',
        'ficha_id',
        'accion',
        'detalles',
        'resultado',
        'mensaje',
        'user_id',
        'fecha_accion',
        'datos_anteriores',
        'datos_nuevos'
    ];

    protected $casts = [
        'detalles' => 'array',
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'fecha_accion' => 'datetime'
    ];

    /**
     * Relación con Instructor
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * Relación con FichaCaracterizacion
     */
    public function ficha(): BelongsTo
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_id');
    }

    /**
     * Relación con User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Crear log de asignación
     */
    public static function crearLog(
        int $instructorId,
        int $fichaId,
        string $accion,
        string $resultado,
        string $mensaje,
        int $userId,
        array $detalles = [],
        array $datosAnteriores = null,
        array $datosNuevos = null
    ): self {
        return self::create([
            'instructor_id' => $instructorId,
            'ficha_id' => $fichaId,
            'accion' => $accion,
            'detalles' => $detalles,
            'resultado' => $resultado,
            'mensaje' => $mensaje,
            'user_id' => $userId,
            'fecha_accion' => now(),
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos
        ]);
    }

    /**
     * Scope para filtrar por resultado
     */
    public function scopeExitoso($query)
    {
        return $query->where('resultado', 'exitoso');
    }

    /**
     * Scope para filtrar por error
     */
    public function scopeConError($query)
    {
        return $query->where('resultado', 'error');
    }

    /**
     * Scope para filtrar por acción
     */
    public function scopeAccion($query, string $accion)
    {
        return $query->where('accion', $accion);
    }

    /**
     * Scope para filtrar por instructor
     */
    public function scopeInstructor($query, int $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Scope para filtrar por ficha
     */
    public function scopeFicha($query, int $fichaId)
    {
        return $query->where('ficha_id', $fichaId);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, Carbon $fechaInicio, Carbon $fechaFin)
    {
        return $query->whereBetween('fecha_accion', [$fechaInicio, $fechaFin]);
    }

    /**
     * Obtener logs recientes
     */
    public static function obtenerLogsRecientes(int $limite = 50): \Illuminate\Database\Eloquent\Collection
    {
        return self::with(['instructor.persona', 'ficha', 'user'])
            ->orderBy('fecha_accion', 'desc')
            ->limit($limite)
            ->get();
    }

    /**
     * Obtener estadísticas de asignaciones
     */
    public static function obtenerEstadisticas(Carbon $fechaInicio = null, Carbon $fechaFin = null): array
    {
        $query = self::query();
        
        if ($fechaInicio && $fechaFin) {
            $query->entreFechas($fechaInicio, $fechaFin);
        }

        $total = $query->count();
        $exitosos = $query->clone()->exitoso()->count();
        $conError = $query->clone()->conError()->count();

        return [
            'total' => $total,
            'exitosos' => $exitosos,
            'con_error' => $conError,
            'porcentaje_exito' => $total > 0 ? round(($exitosos / $total) * 100, 2) : 0,
            'porcentaje_error' => $total > 0 ? round(($conError / $total) * 100, 2) : 0
        ];
    }

    /**
     * Accessor para el nombre del instructor
     */
    public function getNombreInstructorAttribute(): string
    {
        return $this->instructor ? ($this->instructor->nombre_completo ?? 'Sin nombre') : 'Instructor eliminado';
    }

    /**
     * Accessor para el número de ficha
     */
    public function getNumeroFichaAttribute(): string
    {
        return $this->ficha ? ($this->ficha->ficha ?? 'Sin número') : 'Ficha eliminada';
    }

    /**
     * Accessor para el nombre del usuario
     */
    public function getNombreUsuarioAttribute(): string
    {
        return $this->user ? ($this->user->name ?? 'Sin nombre') : 'Usuario eliminado';
    }

    /**
     * Accessor para la fecha formateada
     */
    public function getFechaAccionFormateadaAttribute(): string
    {
        return $this->fecha_accion ? $this->fecha_accion->format('d/m/Y H:i:s') : 'Sin fecha';
    }
}

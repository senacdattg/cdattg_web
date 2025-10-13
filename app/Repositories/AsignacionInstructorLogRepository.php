<?php

namespace App\Repositories;

use App\Models\AsignacionInstructorLog;
use Illuminate\Database\Eloquent\Collection;

class AsignacionInstructorLogRepository
{
    /**
     * Registra un log de asignación
     *
     * @param array $datos
     * @return AsignacionInstructorLog
     */
    public function registrar(array $datos): AsignacionInstructorLog
    {
        return AsignacionInstructorLog::create($datos);
    }

    /**
     * Obtiene logs por instructor
     *
     * @param int $instructorId
     * @return Collection
     */
    public function obtenerPorInstructor(int $instructorId): Collection
    {
        return AsignacionInstructorLog::where('instructor_id', $instructorId)
            ->with(['ficha', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtiene logs por ficha
     *
     * @param int $fichaId
     * @return Collection
     */
    public function obtenerPorFicha(int $fichaId): Collection
    {
        return AsignacionInstructorLog::where('ficha_caracterizacion_id', $fichaId)
            ->with(['instructor.persona', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtiene auditoría de cambios
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return Collection
     */
    public function obtenerAuditoria(string $fechaInicio, string $fechaFin): Collection
    {
        return AsignacionInstructorLog::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->with(['instructor.persona', 'ficha', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}


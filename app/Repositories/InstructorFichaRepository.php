<?php

namespace App\Repositories;

use App\Models\InstructorFichaCaracterizacion;
use Illuminate\Database\Eloquent\Collection;

class InstructorFichaRepository
{
    /**
     * Obtiene fichas por instructor
     *
     * @param int $instructorId
     * @param bool $soloActivas
     * @return Collection
     */
    public function obtenerPorInstructor(int $instructorId, bool $soloActivas = false): Collection
    {
        $query = InstructorFichaCaracterizacion::where('instructor_id', $instructorId)
            ->with(['ficha.programaFormacion', 'ficha.jornadaFormacion']);

        if ($soloActivas) {
            $query->whereHas('ficha', function ($q) {
                $q->where('status', true)
                  ->where('fecha_fin', '>=', now());
            });
        }

        return $query->orderBy('fecha_inicio', 'desc')->get();
    }

    /**
     * Obtiene instructores por ficha
     *
     * @param int $fichaId
     * @return Collection
     */
    public function obtenerPorFicha(int $fichaId): Collection
    {
        return InstructorFichaCaracterizacion::where('ficha_caracterizacion_id', $fichaId)
            ->with(['instructor.persona', 'instructor.regional'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Crea asignación de instructor a ficha
     *
     * @param array $datos
     * @return InstructorFichaCaracterizacion
     */
    public function crear(array $datos): InstructorFichaCaracterizacion
    {
        return InstructorFichaCaracterizacion::create($datos);
    }

    /**
     * Actualiza asignación
     *
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar(int $id, array $datos): bool
    {
        return InstructorFichaCaracterizacion::where('id', $id)->update($datos);
    }

    /**
     * Verifica si instructor está asignado a ficha
     *
     * @param int $instructorId
     * @param int $fichaId
     * @return bool
     */
    public function estaAsignado(int $instructorId, int $fichaId): bool
    {
        return InstructorFichaCaracterizacion::where('instructor_id', $instructorId)
            ->where('ficha_caracterizacion_id', $fichaId)
            ->where('status', true)
            ->exists();
    }

    /**
     * Obtiene carga horaria semanal del instructor
     *
     * @param int $instructorId
     * @param string $fecha
     * @return int
     */
    public function obtenerCargaSemanal(int $instructorId, string $fecha): int
    {
        return InstructorFichaCaracterizacion::where('instructor_id', $instructorId)
            ->where('status', true)
            ->whereHas('ficha', function ($q) use ($fecha) {
                $q->where('fecha_inicio', '<=', $fecha)
                  ->where('fecha_fin', '>=', $fecha);
            })
            ->sum('horas_semanales') ?? 0;
    }
}


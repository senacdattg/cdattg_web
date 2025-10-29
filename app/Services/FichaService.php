<?php

namespace App\Services;

use App\Repositories\FichaRepository;
use App\Repositories\InstructorFichaRepository;
use App\Repositories\AprendizFichaRepository;
use App\Models\FichaCaracterizacion;
use App\Events\FichaAsignadaAInstructor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FichaService
{
    protected FichaRepository $fichaRepo;
    protected InstructorFichaRepository $instructorFichaRepo;
    protected AprendizFichaRepository $aprendizFichaRepo;

    public function __construct(
        FichaRepository $fichaRepo,
        InstructorFichaRepository $instructorFichaRepo,
        AprendizFichaRepository $aprendizFichaRepo
    ) {
        $this->fichaRepo = $fichaRepo;
        $this->instructorFichaRepo = $instructorFichaRepo;
        $this->aprendizFichaRepo = $aprendizFichaRepo;
    }

    /**
     * Lista fichas con filtros
     *
     * @param array $filtros
     * @return LengthAwarePaginator
     */
    public function listarConFiltros(array $filtros = []): LengthAwarePaginator
    {
        return $this->fichaRepo->obtenerConFiltros($filtros);
    }

    /**
     * Obtiene ficha con relaciones
     *
     * @param int $id
     * @return FichaCaracterizacion|null
     */
    public function obtener(int $id): ?FichaCaracterizacion
    {
        return $this->fichaRepo->encontrarConRelaciones($id);
    }

    /**
     * Crea una nueva ficha
     *
     * @param array $datos
     * @return FichaCaracterizacion
     */
    public function crear(array $datos): FichaCaracterizacion
    {
        return DB::transaction(function () use ($datos) {
            $ficha = $this->fichaRepo->crear($datos);

            Log::info('Ficha creada exitosamente', [
                'ficha_id' => $ficha->id,
                'numero' => $ficha->ficha,
            ]);

            return $ficha;
        });
    }

    /**
     * Actualiza una ficha
     *
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $actualizado = $this->fichaRepo->actualizar($id, $datos);

            Log::info('Ficha actualizada', [
                'ficha_id' => $id,
            ]);

            return $actualizado;
        });
    }

    /**
     * Asigna instructor a ficha
     *
     * @param int $fichaId
     * @param int $instructorId
     * @param array $datosAsignacion
     * @return bool
     */
    public function asignarInstructor(int $fichaId, int $instructorId, array $datosAsignacion): bool
    {
        return DB::transaction(function () use ($fichaId, $instructorId, $datosAsignacion) {
            // Verificar si ya está asignado
            if ($this->instructorFichaRepo->estaAsignado($instructorId, $fichaId)) {
                throw new \Exception('El instructor ya está asignado a esta ficha.');
            }

            // Crear asignación
            $asignacion = $this->instructorFichaRepo->crear([
                'instructor_id' => $instructorId,
                'ficha_caracterizacion_id' => $fichaId,
                ...$datosAsignacion
            ]);

            $instructor = \App\Models\Instructor::find($instructorId);
            $ficha = $this->fichaRepo->encontrarConRelaciones($fichaId);

            // Disparar evento
            event(new FichaAsignadaAInstructor($instructor, $ficha, $datosAsignacion));

            Log::info('Instructor asignado a ficha', [
                'ficha_id' => $fichaId,
                'instructor_id' => $instructorId,
            ]);

            return true;
        });
    }

    /**
     * Obtiene estadísticas de fichas
     *
     * @return array
     */
    public function obtenerEstadisticas(): array
    {
        return $this->fichaRepo->obtenerEstadisticas();
    }

    /**
     * Verifica disponibilidad de ficha
     *
     * @param int $fichaId
     * @return array
     */
    public function verificarDisponibilidad(int $fichaId): array
    {
        $ficha = $this->fichaRepo->encontrarConRelaciones($fichaId);

        if (!$ficha) {
            return [
                'disponible' => false,
                'razon' => 'Ficha no encontrada',
            ];
        }

        $instructores = $this->instructorFichaRepo->obtenerPorFicha($fichaId);
        $aprendices = $this->aprendizFichaRepo->obtenerPorFicha($fichaId);

        return [
            'disponible' => $ficha->status,
            'tiene_instructor' => $instructores->isNotEmpty(),
            'total_instructores' => $instructores->count(),
            'total_aprendices' => $aprendices->count(),
            'cupos_disponibles' => max(0, ($ficha->cupos_maximos ?? 40) - $aprendices->count()),
        ];
    }
}


<?php

namespace App\Services;

use App\Repositories\AprendizRepository;
use App\Models\Aprendiz;
use App\Events\AprendizAsignadoAFicha;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AprendizService
{
    protected AprendizRepository $repository;

    public function __construct(AprendizRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Obtiene lista paginada de aprendices con filtros
     *
     * @param array $filtros
     * @return LengthAwarePaginator
     */
    public function listarConFiltros(array $filtros = []): LengthAwarePaginator
    {
        return $this->repository->obtenerAprendicesConFiltros($filtros);
    }

    /**
     * Obtiene un aprendiz por ID con todas sus relaciones
     *
     * @param int $id
     * @return Aprendiz|null
     */
    public function obtener(int $id): ?Aprendiz
    {
        return $this->repository->encontrarConRelaciones($id);
    }

    /**
     * Crea un nuevo aprendiz con validaciones de negocio
     *
     * @param array $datos
     * @return Aprendiz
     * @throws \Exception
     */
    public function crear(array $datos): Aprendiz
    {
        return DB::transaction(function () use ($datos) {
            // Validar que la persona no sea ya aprendiz
            if ($this->repository->esAprendiz($datos['persona_id'])) {
                throw new \Exception('Esta persona ya está registrada como aprendiz.');
            }

            // Crear el aprendiz
            $aprendiz = $this->repository->crear($datos);

            // Invalidar caché
            $this->repository->invalidarCache();

            // Disparar evento si tiene ficha asignada
            if (!empty($datos['ficha_caracterizacion_id'])) {
                event(new AprendizAsignadoAFicha($aprendiz, $datos['ficha_caracterizacion_id']));
            }

            Log::info('Aprendiz creado exitosamente', [
                'aprendiz_id' => $aprendiz->id,
                'persona_id' => $datos['persona_id'],
                'ficha_id' => $datos['ficha_caracterizacion_id'] ?? null,
            ]);

            return $aprendiz;
        });
    }

    /**
     * Actualiza un aprendiz existente
     *
     * @param int $id
     * @param array $datos
     * @return bool
     * @throws \Exception
     */
    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $aprendiz = $this->repository->encontrarConRelaciones($id);

            if (!$aprendiz) {
                throw new \Exception('Aprendiz no encontrado.');
            }

            $fichaAnterior = $aprendiz->ficha_caracterizacion_id;
            
            // Actualizar el aprendiz
            $actualizado = $this->repository->actualizar($id, $datos);

            // Invalidar caché
            $this->repository->invalidarCache();

            // Disparar evento si cambió la ficha
            if (!empty($datos['ficha_caracterizacion_id']) && $datos['ficha_caracterizacion_id'] != $fichaAnterior) {
                event(new AprendizAsignadoAFicha($aprendiz->fresh(), $datos['ficha_caracterizacion_id']));
            }

            Log::info('Aprendiz actualizado exitosamente', [
                'aprendiz_id' => $id,
                'ficha_anterior' => $fichaAnterior,
                'ficha_nueva' => $datos['ficha_caracterizacion_id'] ?? null,
            ]);

            return $actualizado;
        });
    }

    /**
     * Elimina un aprendiz (soft delete)
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $eliminado = $this->repository->eliminar($id);

            if ($eliminado) {
                // Invalidar caché
                $this->repository->invalidarCache();
                
                Log::info('Aprendiz eliminado exitosamente', [
                    'aprendiz_id' => $id,
                ]);
            }

            return $eliminado;
        });
    }

    /**
     * Cambia el estado de un aprendiz
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function cambiarEstado(int $id): bool
    {
        $aprendiz = $this->repository->encontrarConRelaciones($id);

        if (!$aprendiz) {
            throw new \Exception('Aprendiz no encontrado.');
        }

        $nuevoEstado = !$aprendiz->estado;
        
        $actualizado = $this->repository->actualizar($id, ['estado' => $nuevoEstado]);

        Log::info('Estado de aprendiz cambiado', [
            'aprendiz_id' => $id,
            'nuevo_estado' => $nuevoEstado,
        ]);

        return $actualizado;
    }

    /**
     * Busca aprendices por término
     *
     * @param string $termino
     * @param int $limite
     * @return Collection
     */
    public function buscar(string $termino, int $limite = 10): Collection
    {
        return $this->repository->buscar($termino, $limite);
    }

    /**
     * Obtiene aprendices por ficha
     *
     * @param int $fichaId
     * @return Collection
     */
    public function obtenerPorFicha(int $fichaId): Collection
    {
        return $this->repository->obtenerPorFicha($fichaId);
    }

    /**
     * Obtiene estadísticas de aprendices
     *
     * @return array
     */
    public function obtenerEstadisticas(): array
    {
        return $this->repository->obtenerEstadisticas();
    }

    /**
     * Verifica si una persona ya es aprendiz
     *
     * @param int $personaId
     * @return bool
     */
    public function esAprendiz(int $personaId): bool
    {
        return $this->repository->esAprendiz($personaId);
    }

    /**
     * Cuenta aprendices por ficha
     *
     * @param int $fichaId
     * @return int
     */
    public function contarPorFicha(int $fichaId): int
    {
        return $this->repository->contarPorFicha($fichaId);
    }

    /**
     * Prepara datos para API/JSON
     *
     * @param Aprendiz $aprendiz
     * @return array
     */
    public function formatearParaApi(Aprendiz $aprendiz): array
    {
        return [
            'id' => $aprendiz->id,
            'persona_id' => $aprendiz->persona_id,
            'nombre_completo' => $aprendiz->persona->nombre_completo ?? 'N/A',
            'numero_documento' => $aprendiz->persona->numero_documento ?? 'N/A',
            'email' => $aprendiz->persona->email ?? 'N/A',
            'ficha' => $aprendiz->fichaCaracterizacion->ficha ?? 'N/A',
            'programa' => $aprendiz->fichaCaracterizacion->programaFormacion->nombre ?? 'N/A',
            'estado' => $aprendiz->estado,
        ];
    }
}


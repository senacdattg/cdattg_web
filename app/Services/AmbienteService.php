<?php

namespace App\Services;

use App\Repositories\AmbienteRepository;
use App\Repositories\RegionalRepository;
use App\Models\Ambiente;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AmbienteService
{
    protected AmbienteRepository $repository;
    protected RegionalRepository $regionalRepo;

    public function __construct(
        AmbienteRepository $repository,
        RegionalRepository $regionalRepo
    ) {
        $this->repository = $repository;
        $this->regionalRepo = $regionalRepo;
    }

    /**
     * Lista ambientes paginados
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listar(int $perPage = 10): LengthAwarePaginator
    {
        return Ambiente::with(['piso.bloque', 'sede'])->paginate($perPage);
    }

    /**
     * Obtiene ambiente por ID
     *
     * @param int $id
     * @return Ambiente|null
     */
    public function obtener(int $id): ?Ambiente
    {
        return Ambiente::with(['piso.bloque', 'sede'])->find($id);
    }

    /**
     * Obtiene ambientes por piso
     *
     * @param int $pisoId
     * @return array
     */
    public function obtenerPorPiso(int $pisoId): array
    {
        $ambientes = Ambiente::where('piso_id', $pisoId)->get();

        return [
            'success' => true,
            'ambientes' => $ambientes,
        ];
    }

    /**
     * Obtiene ambientes por regional (estructura completa)
     *
     * @param int $regionalId
     * @return array
     */
    public function obtenerPorRegional(int $regionalId): array
    {
        $regional = \App\Models\Regional::find($regionalId);

        if (!$regional) {
            return [
                'success' => false,
                'message' => 'Regional no encontrada',
            ];
        }

        $ambientes = [];

        foreach ($regional->sedes as $sede) {
            foreach ($sede->bloques as $bloque) {
                foreach ($bloque->piso as $piso) {
                    $ambientes = array_merge($ambientes, $piso->ambientes->toArray());
                }
            }
        }

        return [
            'success' => true,
            'ambientes' => $ambientes,
        ];
    }

    /**
     * Crea un nuevo ambiente
     *
     * @param array $datos
     * @return Ambiente
     */
    public function crear(array $datos): Ambiente
    {
        return DB::transaction(function () use ($datos) {
            $ambiente = Ambiente::create($datos);

            $this->repository->invalidarCache();

            Log::info('Ambiente creado', [
                'ambiente_id' => $ambiente->id,
                'nombre' => $ambiente->title,
            ]);

            return $ambiente;
        });
    }

    /**
     * Actualiza un ambiente
     *
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $actualizado = Ambiente::where('id', $id)->update($datos);

            if ($actualizado) {
                $this->repository->invalidarCache();

                Log::info('Ambiente actualizado', [
                    'ambiente_id' => $id,
                ]);
            }

            return $actualizado;
        });
    }

    /**
     * Elimina un ambiente
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $ambiente = Ambiente::find($id);

            if (!$ambiente) {
                throw new \Exception('Ambiente no encontrado');
            }

            $eliminado = $ambiente->delete();

            if ($eliminado) {
                $this->repository->invalidarCache();

                Log::info('Ambiente eliminado', [
                    'ambiente_id' => $id,
                ]);
            }

            return $eliminado;
        });
    }

    /**
     * Cambia el estado de un ambiente
     *
     * @param int $id
     * @return bool
     */
    public function cambiarEstado(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $ambiente = Ambiente::find($id);

            if (!$ambiente) {
                throw new \Exception('Ambiente no encontrado');
            }

            $nuevoEstado = !$ambiente->status;
            $ambiente->update(['status' => $nuevoEstado]);

            $this->repository->invalidarCache();

            Log::info('Estado de ambiente cambiado', [
                'ambiente_id' => $id,
                'nuevo_estado' => $nuevoEstado,
            ]);

            return true;
        });
    }
}


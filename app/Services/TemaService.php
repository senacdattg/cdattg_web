<?php

namespace App\Services;

use App\Repositories\TemaRepository;
use App\Models\Tema;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TemaService
{
    protected TemaRepository $repository;

    public function __construct(TemaRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Lista temas paginados
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listar(int $perPage = 10): LengthAwarePaginator
    {
        return Tema::with('parametros')->paginate($perPage);
    }

    /**
     * Obtiene tema con parámetros
     *
     * @param int $id
     * @return Tema|null
     */
    public function obtenerConParametros(int $id): ?Tema
    {
        return $this->repository->encontrarConParametros($id);
    }

    /**
     * Crea un tema
     *
     * @param array $datos
     * @return Tema
     */
    public function crear(array $datos): Tema
    {
        return DB::transaction(function () use ($datos) {
            $tema = Tema::create($datos);
            
            $this->repository->invalidarCache();

            Log::info('Tema creado', ['tema_id' => $tema->id]);

            return $tema;
        });
    }

    /**
     * Actualiza un tema
     *
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $actualizado = Tema::where('id', $id)->update($datos);
            
            if ($actualizado) {
                $this->repository->invalidarCache();
            }

            return $actualizado;
        });
    }

    /**
     * Elimina un tema
     *
     * @param int $id
     * @return bool
     */
    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $eliminado = Tema::where('id', $id)->delete();
            
            if ($eliminado) {
                $this->repository->invalidarCache();
            }

            return $eliminado;
        });
    }

    /**
     * Actualiza parámetros de un tema
     *
     * @param int $temaId
     * @param array $parametrosIds
     * @param array $estados
     * @return bool
     */
    public function actualizarParametros(int $temaId, array $parametrosIds, array $estados): bool
    {
        return DB::transaction(function () use ($temaId, $parametrosIds, $estados) {
            $tema = Tema::findOrFail($temaId);

            $syncData = [];
            foreach ($parametrosIds as $index => $parametroId) {
                $syncData[$parametroId] = [
                    'status' => $estados[$index] ?? 1,
                ];
            }

            $tema->parametros()->sync($syncData);

            $this->repository->invalidarCache();

            Log::info('Parámetros de tema actualizados', [
                'tema_id' => $temaId,
                'parametros_actualizados' => count($parametrosIds),
            ]);

            return true;
        });
    }

    /**
     * Cambia el estado de un tema
     *
     * @param int $id
     * @return bool
     */
    public function cambiarEstado(int $id): bool
    {
        $tema = Tema::find($id);
        $nuevoEstado = !$tema->status;
        
        return $this->actualizar($id, ['status' => $nuevoEstado]);
    }
}


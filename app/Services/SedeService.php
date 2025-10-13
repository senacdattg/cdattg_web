<?php

namespace App\Services;

use App\Repositories\SedeRepository;
use App\Models\Sede;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SedeService
{
    protected SedeRepository $repository;

    public function __construct(SedeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listar(int $perPage = 10): LengthAwarePaginator
    {
        return Sede::with(['municipio', 'regional'])->paginate($perPage);
    }

    public function obtenerPorMunicipio(int $municipioId): Collection
    {
        return Sede::where('municipio_id', $municipioId)
            ->where('status', 1)
            ->get();
    }

    public function obtenerPorRegional(int $regionalId): Collection
    {
        return Sede::where('regional_id', $regionalId)
            ->where('status', 1)
            ->get();
    }

    public function crear(array $datos): Sede
    {
        return DB::transaction(function () use ($datos) {
            $sede = Sede::create($datos);
            $this->repository->invalidarCache();

            Log::info('Sede creada', ['sede_id' => $sede->id]);

            return $sede;
        });
    }

    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $actualizado = Sede::where('id', $id)->update($datos);
            
            if ($actualizado) {
                $this->repository->invalidarCache();
            }

            return $actualizado;
        });
    }

    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $eliminado = Sede::where('id', $id)->delete();
            
            if ($eliminado) {
                $this->repository->invalidarCache();
            }

            return $eliminado;
        });
    }

    public function cambiarEstado(int $id): bool
    {
        $sede = Sede::find($id);
        $nuevoEstado = !$sede->status;
        
        return $this->actualizar($id, ['status' => $nuevoEstado]);
    }
}


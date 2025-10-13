<?php

namespace App\Services;

use App\Repositories\PisoRepository;
use App\Models\Piso;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PisoService
{
    protected PisoRepository $repository;

    public function __construct(PisoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listar(int $perPage = 10)
    {
        return Piso::with(['bloque.sede'])->paginate($perPage);
    }

    public function obtenerPorBloque(int $bloqueId): Collection
    {
        return Piso::where('bloque_id', $bloqueId)->get();
    }

    public function crear(array $datos): Piso
    {
        return DB::transaction(function () use ($datos) {
            $piso = Piso::create($datos);
            $this->repository->invalidarCache();

            Log::info('Piso creado', ['piso_id' => $piso->id]);

            return $piso;
        });
    }

    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $actualizado = Piso::where('id', $id)->update($datos);
            
            if ($actualizado) {
                $this->repository->invalidarCache();
            }

            return $actualizado;
        });
    }

    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $eliminado = Piso::where('id', $id)->delete();
            
            if ($eliminado) {
                $this->repository->invalidarCache();
            }

            return $eliminado;
        });
    }

    public function cambiarEstado(int $id): bool
    {
        $piso = Piso::find($id);
        $nuevoEstado = !$piso->status;
        
        return $this->actualizar($id, ['status' => $nuevoEstado]);
    }
}


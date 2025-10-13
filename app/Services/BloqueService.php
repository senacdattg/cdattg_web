<?php

namespace App\Services;

use App\Repositories\BloqueRepository;
use App\Models\Bloque;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BloqueService
{
    protected BloqueRepository $repository;

    public function __construct(BloqueRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listar(int $perPage = 10)
    {
        return Bloque::with(['sede'])->paginate($perPage);
    }

    public function obtenerPorSede(int $sedeId): Collection
    {
        return Bloque::where('sede_id', $sedeId)->get();
    }

    public function crear(array $datos): Bloque
    {
        return DB::transaction(function () use ($datos) {
            $bloque = Bloque::create($datos);
            $this->repository->invalidarCache();

            Log::info('Bloque creado', ['bloque_id' => $bloque->id]);

            return $bloque;
        });
    }

    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $actualizado = Bloque::where('id', $id)->update($datos);
            
            if ($actualizado) {
                $this->repository->invalidarCache();
            }

            return $actualizado;
        });
    }

    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $eliminado = Bloque::where('id', $id)->delete();
            
            if ($eliminado) {
                $this->repository->invalidarCache();
            }

            return $eliminado;
        });
    }

    public function cambiarEstado(int $id): bool
    {
        $bloque = Bloque::find($id);
        $nuevoEstado = !$bloque->status;
        
        return $this->actualizar($id, ['status' => $nuevoEstado]);
    }
}


<?php

namespace App\Services;

use App\Repositories\ParametroRepository;
use App\Models\Parametro;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParametroService
{
    protected ParametroRepository $repository;

    public function __construct(ParametroRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listar(int $perPage = 10): LengthAwarePaginator
    {
        return Parametro::paginate($perPage);
    }

    public function obtenerTodos(): Collection
    {
        return Parametro::all();
    }

    public function obtenerActivos(): Collection
    {
        return Parametro::where('status', 1)->get();
    }

    public function crear(array $datos): Parametro
    {
        return DB::transaction(function () use ($datos) {
            $parametro = Parametro::create($datos);
            
            $this->repository->invalidarCache();

            Log::info('Parámetro creado', ['parametro_id' => $parametro->id]);

            return $parametro;
        });
    }

    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $actualizado = Parametro::where('id', $id)->update($datos);
            
            if ($actualizado) {
                $this->repository->invalidarCache();
            }

            return $actualizado;
        });
    }

    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $eliminado = Parametro::where('id', $id)->delete();
            
            if ($eliminado) {
                $this->repository->invalidarCache();
            }

            return $eliminado;
        });
    }

    public function cambiarEstado(int $id): bool
    {
        $parametro = Parametro::find($id);
        $nuevoEstado = !$parametro->status;
        
        return $this->actualizar($id, ['status' => $nuevoEstado]);
    }
}


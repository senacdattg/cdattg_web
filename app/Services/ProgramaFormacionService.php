<?php

namespace App\Services;

use App\Repositories\ProgramaFormacionRepository;
use App\Models\ProgramaFormacion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgramaFormacionService
{
    protected ProgramaFormacionRepository $repository;

    public function __construct(ProgramaFormacionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listar(int $perPage = 15): LengthAwarePaginator
    {
        return ProgramaFormacion::with(['redConocimiento', 'nivelFormacion', 'tipoPrograma'])
            ->orderBy('nombre')
            ->paginate($perPage);
    }

    public function obtenerActivos(): Collection
    {
        return $this->repository->obtenerActivos();
    }

    public function obtenerPorRed(int $redId): Collection
    {
        return $this->repository->obtenerPorRed($redId);
    }

    public function crear(array $datos): ProgramaFormacion
    {
        return DB::transaction(function () use ($datos) {
            $programa = ProgramaFormacion::create($datos);
            $this->repository->invalidarCache();

            Log::info('Programa creado', ['programa_id' => $programa->id]);

            return $programa;
        });
    }

    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $actualizado = ProgramaFormacion::where('id', $id)->update($datos);
            
            if ($actualizado) {
                $this->repository->invalidarCache();
            }

            return $actualizado;
        });
    }

    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $eliminado = ProgramaFormacion::where('id', $id)->delete();
            
            if ($eliminado) {
                $this->repository->invalidarCache();
            }

            return $eliminado;
        });
    }
}


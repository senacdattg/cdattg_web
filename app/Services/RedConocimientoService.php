<?php

namespace App\Services;

use App\Repositories\RedConocimientoRepository;
use App\Models\RedConocimiento;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RedConocimientoService
{
    protected RedConocimientoRepository $repository;

    public function __construct(RedConocimientoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listar(int $perPage = 10): LengthAwarePaginator
    {
        return RedConocimiento::with('regional')->paginate($perPage);
    }

    public function crear(array $datos): RedConocimiento
    {
        return DB::transaction(function () use ($datos) {
            $red = RedConocimiento::create($datos);

            $this->repository->invalidarCache();

            Log::info('Red de conocimiento creada', ['red_id' => $red->id]);

            return $red;
        });
    }

    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $actualizado = RedConocimiento::where('id', $id)->update($datos);

            if ($actualizado) {
                $this->repository->invalidarCache();
            }

            return $actualizado;
        });
    }

    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $eliminado = RedConocimiento::where('id', $id)->delete();

            if ($eliminado) {
                $this->repository->invalidarCache();
            }

            return $eliminado;
        });
    }

    public function cambiarEstado(int $id): bool
    {
        $red = RedConocimiento::find($id);
        $nuevoEstado = !$red->status;

        return $this->actualizar($id, ['status' => $nuevoEstado]);
    }
}


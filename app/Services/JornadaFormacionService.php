<?php

namespace App\Services;

use App\Repositories\JornadaFormacionRepository;
use App\Models\JornadaFormacion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JornadaFormacionService
{
    protected JornadaFormacionRepository $repository;

    public function __construct(JornadaFormacionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listarTodas(): Collection
    {
        return JornadaFormacion::all();
    }

    public function obtenerActivas(): Collection
    {
        return JornadaFormacion::where('status', 1)->get();
    }

    public function crear(array $datos): JornadaFormacion
    {
        return DB::transaction(function () use ($datos) {
            $jornada = JornadaFormacion::create($datos);
            
            $this->repository->invalidarCache();

            Log::info('Jornada creada', ['jornada_id' => $jornada->id]);

            return $jornada;
        });
    }

    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $actualizado = JornadaFormacion::where('id', $id)->update($datos);
            
            if ($actualizado) {
                $this->repository->invalidarCache();
            }

            return $actualizado;
        });
    }

    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $eliminado = JornadaFormacion::where('id', $id)->delete();
            
            if ($eliminado) {
                $this->repository->invalidarCache();
            }

            return $eliminado;
        });
    }

    public function cambiarEstado(int $id): bool
    {
        $jornada = JornadaFormacion::find($id);
        $nuevoEstado = !$jornada->status;
        
        return $this->actualizar($id, ['status' => $nuevoEstado]);
    }

    public function validarHorarios(string $horaInicio, string $horaFin): bool
    {
        return strtotime($horaFin) > strtotime($horaInicio);
    }
}


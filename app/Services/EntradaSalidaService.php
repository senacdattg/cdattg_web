<?php

namespace App\Services;

use App\Repositories\EntradaSalidaRepository;
use App\Models\EntradaSalida;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntradaSalidaService
{
    protected EntradaSalidaRepository $repository;

    public function __construct(EntradaSalidaRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Registra entrada de una persona
     *
     * @param int $personaId
     * @return EntradaSalida
     * @throws \Exception
     */
    public function registrarEntrada(int $personaId): EntradaSalida
    {
        return DB::transaction(function () use ($personaId) {
            $fecha = Carbon::now()->format('Y-m-d');

            // Verificar si ya tiene un registro abierto
            $registroAbierto = $this->repository->obtenerRegistroAbierto($personaId, $fecha);

            if ($registroAbierto) {
                throw new \Exception('Ya existe un registro de entrada sin salida para hoy.');
            }

            $entrada = $this->repository->registrarEntrada($personaId);

            Log::info('Entrada registrada', [
                'entrada_salida_id' => $entrada->id,
                'persona_id' => $personaId,
                'hora' => $entrada->hora_entrada,
            ]);

            return $entrada;
        });
    }

    /**
     * Registra salida de una persona
     *
     * @param int $personaId
     * @return bool
     * @throws \Exception
     */
    public function registrarSalida(int $personaId): bool
    {
        return DB::transaction(function () use ($personaId) {
            $fecha = Carbon::now()->format('Y-m-d');

            $registroAbierto = $this->repository->obtenerRegistroAbierto($personaId, $fecha);

            if (!$registroAbierto) {
                throw new \Exception('No hay registro de entrada para registrar salida.');
            }

            $actualizado = $this->repository->registrarSalida($registroAbierto->id);

            if ($actualizado) {
                Log::info('Salida registrada', [
                    'entrada_salida_id' => $registroAbierto->id,
                    'persona_id' => $personaId,
                ]);
            }

            return $actualizado;
        });
    }

    /**
     * Obtiene registros de entrada/salida por fecha
     *
     * @param string $fecha
     * @return Collection
     */
    public function obtenerPorFecha(string $fecha): Collection
    {
        return $this->repository->obtenerPorFecha($fecha);
    }

    /**
     * Obtiene historial de una persona
     *
     * @param int $personaId
     * @param string|null $fechaInicio
     * @param string|null $fechaFin
     * @return Collection
     */
    public function obtenerHistorialPersona(int $personaId, ?string $fechaInicio = null, ?string $fechaFin = null): Collection
    {
        return $this->repository->obtenerPorPersona($personaId, $fechaInicio, $fechaFin);
    }

    /**
     * Obtiene reporte de asistencia diaria
     *
     * @param string $fecha
     * @return array
     */
    public function obtenerReporteDiario(string $fecha): array
    {
        $registros = $this->repository->obtenerPorFecha($fecha);

        $total = $registros->count();
        $conSalida = $registros->whereNotNull('hora_salida')->count();
        $sinSalida = $total - $conSalida;

        return [
            'fecha' => $fecha,
            'total_registros' => $total,
            'con_salida' => $conSalida,
            'sin_salida' => $sinSalida,
            'porcentaje_completos' => $total > 0 ? round(($conSalida / $total) * 100, 2) : 0,
            'registros' => $registros,
        ];
    }
}


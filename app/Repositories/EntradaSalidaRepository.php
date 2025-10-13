<?php

namespace App\Repositories;

use App\Models\EntradaSalida;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class EntradaSalidaRepository
{
    /**
     * Obtiene registros de entrada/salida por fecha
     *
     * @param string $fecha
     * @return Collection
     */
    public function obtenerPorFecha(string $fecha): Collection
    {
        return EntradaSalida::whereDate('fecha', $fecha)
            ->with(['persona'])
            ->orderBy('hora_entrada', 'desc')
            ->get();
    }

    /**
     * Obtiene registros por persona
     *
     * @param int $personaId
     * @param string|null $fechaInicio
     * @param string|null $fechaFin
     * @return Collection
     */
    public function obtenerPorPersona(int $personaId, ?string $fechaInicio = null, ?string $fechaFin = null): Collection
    {
        $query = EntradaSalida::where('persona_id', $personaId);

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        }

        return $query->orderBy('fecha', 'desc')->get();
    }

    /**
     * Registra entrada
     *
     * @param int $personaId
     * @return EntradaSalida
     */
    public function registrarEntrada(int $personaId): EntradaSalida
    {
        return EntradaSalida::create([
            'persona_id' => $personaId,
            'fecha' => Carbon::now()->format('Y-m-d'),
            'hora_entrada' => Carbon::now()->format('H:i:s'),
        ]);
    }

    /**
     * Registra salida
     *
     * @param int $entradaSalidaId
     * @return bool
     */
    public function registrarSalida(int $entradaSalidaId): bool
    {
        return EntradaSalida::where('id', $entradaSalidaId)->update([
            'hora_salida' => Carbon::now()->format('H:i:s'),
        ]);
    }

    /**
     * Verifica si hay registro abierto (sin salida)
     *
     * @param int $personaId
     * @param string $fecha
     * @return EntradaSalida|null
     */
    public function obtenerRegistroAbierto(int $personaId, string $fecha): ?EntradaSalida
    {
        return EntradaSalida::where('persona_id', $personaId)
            ->whereDate('fecha', $fecha)
            ->whereNull('hora_salida')
            ->first();
    }
}


<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\Ambiente;
use Illuminate\Database\Eloquent\Collection;

class AmbienteRepository
{
    use HasCache;

    protected $cacheType = 'programas';
    protected $cacheTags = ['ambientes', 'infraestructura'];

    /**
     * Obtiene todos los ambientes activos
     *
     * @return Collection
     */
    public function obtenerActivos(): Collection
    {
        return $this->cache('activos', function () {
            return Ambiente::where('status', true)
                ->with(['sede', 'piso'])
                ->orderBy('nombre')
                ->get();
        }, 360); // 6 horas
    }

    /**
     * Obtiene ambientes por sede
     *
     * @param int $sedeId
     * @return Collection
     */
    public function obtenerPorSede(int $sedeId): Collection
    {
        return $this->cache("sede.{$sedeId}.ambientes", function () use ($sedeId) {
            return Ambiente::where('sede_id', $sedeId)
                ->where('status', true)
                ->orderBy('nombre')
                ->get();
        }, 360);
    }

    /**
     * Verifica disponibilidad de ambiente
     *
     * @param int $ambienteId
     * @param string $fecha
     * @param string $horaInicio
     * @param string $horaFin
     * @return bool
     */
    public function estaDisponible(int $ambienteId, string $fecha, string $horaInicio, string $horaFin): bool
    {
        // Verificar si hay fichas asignadas al ambiente en ese horario
        $ocupado = \App\Models\FichaCaracterizacion::where('ambiente_id', $ambienteId)
            ->where('status', 1)
            ->where('fecha_inicio', '<=', $fecha)
            ->where('fecha_fin', '>=', $fecha)
            ->exists();

        return !$ocupado;
    }

    /**
     * Invalida caché
     *
     * @return void
     */
    public function invalidarCache(): void
    {
        $this->flushCache();
    }
}


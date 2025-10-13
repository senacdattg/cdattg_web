<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\Bloque;
use Illuminate\Database\Eloquent\Collection;

class BloqueRepository
{
    use HasCache;

    protected $cacheType = 'parametros';
    protected $cacheTags = ['bloques', 'infraestructura'];

    /**
     * Obtiene bloques por sede
     *
     * @param int $sedeId
     * @return Collection
     */
    public function obtenerPorSede(int $sedeId): Collection
    {
        return $this->cache("sede.{$sedeId}.bloques", function () use ($sedeId) {
            return Bloque::where('sede_id', $sedeId)
                ->orderBy('nombre')
                ->get();
        }, 720); // 12 horas
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


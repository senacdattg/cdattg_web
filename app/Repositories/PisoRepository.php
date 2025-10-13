<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\Piso;
use Illuminate\Database\Eloquent\Collection;

class PisoRepository
{
    use HasCache;

    protected $cacheType = 'parametros';
    protected $cacheTags = ['pisos', 'infraestructura'];

    /**
     * Obtiene pisos por sede
     *
     * @param int $sedeId
     * @return Collection
     */
    public function obtenerPorSede(int $sedeId): Collection
    {
        return $this->cache("sede.{$sedeId}.pisos", function () use ($sedeId) {
            return Piso::where('sede_id', $sedeId)
                ->orderBy('numero')
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


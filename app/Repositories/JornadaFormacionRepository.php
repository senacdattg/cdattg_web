<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\JornadaFormacion;
use Illuminate\Database\Eloquent\Collection;

class JornadaFormacionRepository
{
    use HasCache;


    public function __construct()
    {
        $this->cacheType = 'parametros';
        $this->cacheTags = ['jornadas', 'configuracion'];
    }    /**
     * Obtiene todas las jornadas activas
     *
     * @return Collection
     */
    public function obtenerActivas(): Collection
    {
        return $this->cache('activas', function () {
            return JornadaFormacion::where('status', true)
                ->orderBy('jornada')
                ->get();
        }, 1440); // 24 horas
    }

    /**
     * Encuentra jornada por nombre
     *
     * @param string $nombre
     * @return JornadaFormacion|null
     */
    public function encontrarPorNombre(string $nombre): ?JornadaFormacion
    {
        return $this->cache("jornada.{$nombre}", function () use ($nombre) {
            return JornadaFormacion::where('jornada', $nombre)->first();
        }, 1440);
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


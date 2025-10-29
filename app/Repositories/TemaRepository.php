<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\Tema;
use Illuminate\Database\Eloquent\Collection;

class TemaRepository
{
    use HasCache;


    public function __construct()
    {
        $this->cacheType = 'temas';
        $this->cacheTags = ['temas', 'configuracion'];
    }    /**
     * Obtiene todos los temas con parámetros activos
     *
     * @return Collection
     */
    public function obtenerConParametros(): Collection
    {
        return $this->cache('con_parametros', function () {
            return Tema::with(['parametros' => function ($query) {
                $query->wherePivot('status', 1);
            }])->get();
        }, 1440); // 24 horas
    }

    /**
     * Obtiene un tema específico con parámetros
     *
     * @param int $id
     * @return Tema|null
     */
    public function encontrarConParametros(int $id): ?Tema
    {
        return $this->cache("tema.{$id}.parametros", function () use ($id) {
            return Tema::with(['parametros' => function ($query) {
                $query->wherePivot('status', 1);
            }])->find($id);
        }, 1440);
    }

    /**
     * Obtiene tipos de documento
     *
     * @return Tema|null
     */
    public function obtenerTiposDocumento(): ?Tema
    {
        return $this->encontrarConParametros(2);
    }

    /**
     * Obtiene géneros
     *
     * @return Tema|null
     */
    public function obtenerGeneros(): ?Tema
    {
        return $this->encontrarConParametros(3);
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


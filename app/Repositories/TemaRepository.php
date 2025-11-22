<?php

namespace App\Repositories;

use App\Models\Tema;
use Illuminate\Database\Eloquent\Collection;

class TemaRepository
{

    /**
     * Obtiene todos los temas con parámetros activos
     *
     * @return Collection
     */
    public function obtenerConParametros(): Collection
    {
        return Tema::with(['parametros' => function ($query) {
            $query->wherePivot('status', 1);
        }])->get();
    }

    /**
     * Obtiene un tema específico con parámetros
     *
     * @param int $id
     * @return Tema|null
     */
    public function encontrarConParametros(int $id): ?Tema
    {
        return Tema::with(['parametros' => function ($query) {
            $query->wherePivot('status', 1);
        }])->find($id);
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
     * Obtiene caracterizaciones complementarias
     *
     * @return Tema|null
     */
    public function obtenerCaracterizacionesComplementarias(): ?Tema
    {
        return $this->encontrarConParametros(16);
    }

    /**
     * Obtiene vias
     *
     * @return Tema|null
     */
    public function obtenerVias(): ?Tema
    {
        return $this->encontrarConParametros(17);
    }

    /**
     * Obtiene letras
     *
     * @return Tema|null
     */
    public function obtenerLetras(): ?Tema
    {
        return $this->encontrarConParametros(18);
    }

    /**
     * Obtiene cardinales
     *
     * @return Tema|null
     */
    public function obtenerCardinales(): ?Tema
    {
        $tema = $this->encontrarConParametros(18);

        if (!$tema) {
            return null;
        }

        $parametros = $tema->parametros()
            ->wherePivotIn('parametro_id', [250, 259, 260, 264])
            ->orderBy('name')
            ->get();

        $tema->setRelation('parametros', $parametros);

        return $tema;
    }
}

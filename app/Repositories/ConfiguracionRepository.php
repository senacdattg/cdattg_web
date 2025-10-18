<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\FichaCaracterizacion;
use App\Models\Regional;
use App\Models\Tema;
use App\Models\Parametro;
use App\Models\RedConocimiento;
use App\Models\ProgramaFormacion;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Database\Eloquent\Collection;

class ConfiguracionRepository
{
    use HasCache;


    public function __construct()
    {
        $this->cacheType = 'parametros';
        $this->cacheTags = ['configuracion', 'sistema'];
    }    /**
     * Obtiene todas las fichas activas (con caché)
     *
     * @return Collection
     */
    public function obtenerFichasActivas(): Collection
    {
        return $this->cache('fichas.activas', function () {
            return FichaCaracterizacion::where('status', 1)
                ->with(['programaFormacion', 'jornadaFormacion'])
                ->orderBy('ficha')
                ->get();
        }, 60); // 1 hora
    }

    /**
     * Obtiene todas las regionales activas (con caché)
     *
     * @return Collection
     */
    public function obtenerRegionalesActivas(): Collection
    {
        return $this->cache('regionales.activas', function () {
            return Regional::where('status', true)
                ->orderBy('nombre')
                ->get();
        }, 720); // 12 horas
    }

    /**
     * Obtiene todos los temas con parámetros (con caché)
     *
     * @return Collection
     */
    public function obtenerTemasConParametros(): Collection
    {
        return $this->cache('temas.parametros', function () {
            return Tema::with(['parametros' => function ($query) {
                $query->wherePivot('status', 1);
            }])->get();
        }, 1440); // 24 horas
    }

    /**
     * Obtiene un tema específico con sus parámetros (con caché)
     *
     * @param int $temaId
     * @return Tema|null
     */
    public function obtenerTemaConParametros(int $temaId): ?Tema
    {
        return $this->cache("tema.{$temaId}.parametros", function () use ($temaId) {
            return Tema::with(['parametros' => function ($query) {
                $query->wherePivot('status', 1);
            }])->find($temaId);
        }, 1440); // 24 horas
    }

    /**
     * Obtiene todas las redes de conocimiento activas (con caché)
     *
     * @return Collection
     */
    public function obtenerRedesConocimiento(): Collection
    {
        return $this->cache('redes.conocimiento', function () {
            return RedConocimiento::where('status', true)
                ->orderBy('nombre')
                ->get();
        }, 720); // 12 horas
    }

    /**
     * Obtiene programas de formación activos (con caché)
     *
     * @return Collection
     */
    public function obtenerProgramasActivos(): Collection
    {
        return $this->cache('programas.activos', function () {
            return ProgramaFormacion::where('status', true)
                ->with(['redConocimiento', 'nivelFormacion'])
                ->orderBy('nombre')
                ->get();
        }, 360); // 6 horas
    }

    /**
     * Obtiene todos los departamentos (con caché)
     *
     * @return Collection
     */
    public function obtenerDepartamentos(): Collection
    {
        return $this->cache('departamentos.todos', function () {
            return Departamento::orderBy('nombre')->get();
        }, 1440); // 24 horas
    }

    /**
     * Obtiene municipios por departamento (con caché)
     *
     * @param int $departamentoId
     * @return Collection
     */
    public function obtenerMunicipiosPorDepartamento(int $departamentoId): Collection
    {
        return $this->cache("municipios.departamento.{$departamentoId}", function () use ($departamentoId) {
            return Municipio::where('departamento_id', $departamentoId)
                ->orderBy('nombre')
                ->get();
        }, 1440); // 24 horas
    }

    /**
     * Obtiene parámetros del sistema activos (con caché)
     *
     * @return Collection
     */
    public function obtenerParametrosSistema(): Collection
    {
        return $this->cache('parametros.sistema', function () {
            return Parametro::where('status', true)->get();
        }, 1440); // 24 horas
    }

    /**
     * Invalida toda la caché de configuración
     *
     * @return void
     */
    public function invalidarCache(): void
    {
        $this->flushCache();
    }

    /**
     * Invalida solo caché de fichas
     *
     * @return void
     */
    public function invalidarCacheFichas(): void
    {
        $this->forgetCache('fichas.activas');
    }

    /**
     * Invalida solo caché de regionales
     *
     * @return void
     */
    public function invalidarCacheRegionales(): void
    {
        $this->forgetCache('regionales.activas');
    }
}


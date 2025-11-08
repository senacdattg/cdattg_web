<?php

namespace App\Services;

use App\Repositories\PaisRepository;
use App\Repositories\DepartamentoRepository;
use App\Repositories\MunicipioRepository;
use Illuminate\Support\Collection;

class UbicacionService
{
    protected PaisRepository $paisRepo;
    protected DepartamentoRepository $departamentoRepo;
    protected MunicipioRepository $municipioRepo;

    public function __construct(
        PaisRepository $paisRepo,
        DepartamentoRepository $departamentoRepo,
        MunicipioRepository $municipioRepo
    ) {
        $this->paisRepo = $paisRepo;
        $this->departamentoRepo = $departamentoRepo;
        $this->municipioRepo = $municipioRepo;
    }

    /**
     * Obtiene estructura completa de ubicación (País > Departamento > Municipio)
     *
     * @return array
     */
    public function obtenerEstructuraCompleta(): array
    {
        $paises = $this->paisRepo->obtenerTodos();

        return $paises->map(function ($pais) {
            return [
                'id' => $pais->id,
                'nombre' => $pais->nombre,
                'departamentos' => $this->departamentoRepo->obtenerPorPais($pais->id)->map(function ($depto) {
                    return [
                        'id' => $depto->id,
                        'nombre' => $depto->nombre,
                        'municipios_count' => $this->municipioRepo->obtenerPorDepartamento($depto->id)->count(),
                    ];
                }),
            ];
        })->toArray();
    }

    /**
     * Obtiene departamentos por país (con caché)
     *
     * @param int $paisId
     * @return Collection
     */
    public function obtenerDepartamentosPorPais(int $paisId): Collection
    {
        return $this->departamentoRepo->obtenerPorPais($paisId)
            ->map(function ($departamento) {
                $nombre = data_get($departamento, 'nombre', data_get($departamento, 'departamento', ''));
                return [
                    'id' => data_get($departamento, 'id'),
                    'nombre' => $nombre,
                    'name' => $nombre,
                    'pais_id' => data_get($departamento, 'pais_id'),
                ];
            });
    }

    /**
     * Obtiene municipios por departamento (con caché)
     *
     * @param int $departamentoId
     * @return Collection
     */
    public function obtenerMunicipiosPorDepartamento(int $departamentoId): Collection
    {
        return $this->municipioRepo->obtenerPorDepartamento($departamentoId)
            ->map(function ($municipio) {
                $nombre = $municipio->municipio ?? $municipio->nombre ?? '';
                return [
                    'id' => data_get($municipio, 'id'),
                    'nombre' => $nombre,
                    'name' => $nombre,
                    'departamento_id' => data_get($municipio, 'departamento_id'),
                ];
            });
    }

    /**
     * Busca ubicación completa
     *
     * @param string $termino
     * @return array
     */
    public function buscarUbicacion(string $termino): array
    {
        $departamentos = $this->departamentoRepo->buscar($termino);
        $municipios = $this->municipioRepo->buscar($termino);

        return [
            'departamentos' => $departamentos,
            'municipios' => $municipios,
        ];
    }
}


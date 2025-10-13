<?php

namespace App\Services;

use App\Repositories\CompetenciaRepository;
use App\Repositories\ResultadosAprendizajeRepository;
use App\Repositories\ResultadosCompetenciaRepository;
use Illuminate\Database\Eloquent\Collection;

class CompetenciaService
{
    protected CompetenciaRepository $competenciaRepo;
    protected ResultadosAprendizajeRepository $resultadosRepo;
    protected ResultadosCompetenciaRepository $resultadosCompetenciaRepo;

    public function __construct(
        CompetenciaRepository $competenciaRepo,
        ResultadosAprendizajeRepository $resultadosRepo,
        ResultadosCompetenciaRepository $resultadosCompetenciaRepo
    ) {
        $this->competenciaRepo = $competenciaRepo;
        $this->resultadosRepo = $resultadosRepo;
        $this->resultadosCompetenciaRepo = $resultadosCompetenciaRepo;
    }

    /**
     * Obtiene competencias por programa
     *
     * @param int $programaId
     * @return Collection
     */
    public function obtenerPorPrograma(int $programaId): Collection
    {
        return $this->competenciaRepo->obtenerPorPrograma($programaId);
    }

    /**
     * Obtiene competencia con sus resultados de aprendizaje
     *
     * @param int $competenciaId
     * @return array
     */
    public function obtenerConResultados(int $competenciaId): array
    {
        $competencia = $this->competenciaRepo->obtenerConRelaciones($competenciaId);
        
        if (!$competencia) {
            throw new \Exception('Competencia no encontrada.');
        }

        $resultados = $this->resultadosCompetenciaRepo->obtenerPorCompetencia($competenciaId);

        return [
            'competencia' => $competencia,
            'resultados' => $resultados,
            'total_resultados' => $resultados->count(),
        ];
    }

    /**
     * Obtiene árbol de competencias (programa > competencias > RAPs)
     *
     * @param int $programaId
     * @return array
     */
    public function obtenerArbolCompetencias(int $programaId): array
    {
        $competencias = $this->competenciaRepo->obtenerPorPrograma($programaId);

        $arbol = [];

        foreach ($competencias as $competencia) {
            $resultados = $this->resultadosCompetenciaRepo->obtenerPorCompetencia($competencia->id);

            $arbol[] = [
                'competencia' => [
                    'id' => $competencia->id,
                    'nombre' => $competencia->nombre,
                    'codigo' => $competencia->codigo,
                ],
                'resultados_aprendizaje' => $resultados->map(function ($resultado) {
                    return [
                        'id' => $resultado->resultadoAprendizaje->id ?? null,
                        'nombre' => $resultado->resultadoAprendizaje->nombre ?? 'N/A',
                        'codigo' => $resultado->resultadoAprendizaje->codigo ?? 'N/A',
                    ];
                }),
            ];
        }

        return $arbol;
    }
}


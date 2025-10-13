<?php

namespace App\Services;

use App\Repositories\CompetenciaRepository;
use App\Repositories\ResultadosAprendizajeRepository;
use App\Repositories\ResultadosCompetenciaRepository;
use App\Models\Competencia;
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
        return Competencia::whereHas('programaFormacion', function($query) use ($programaId) {
            $query->where('id', $programaId);
        })->get();
    }

    /**
     * Obtiene competencia con sus resultados de aprendizaje
     *
     * @param int $competenciaId
     * @return array
     */
    public function obtenerConResultados(int $competenciaId): array
    {
        $competencia = Competencia::with(['resultadosAprendizaje', 'resultadosCompetencia'])->find($competenciaId);
        
        if (!$competencia) {
            throw new \Exception('Competencia no encontrada.');
        }

        $resultados = $competencia->resultadosAprendizaje;

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
        $competencias = $this->obtenerPorPrograma($programaId);

        $arbol = [];

        foreach ($competencias as $competencia) {
            $resultados = $competencia->resultadosAprendizaje;

            $arbol[] = [
                'competencia' => [
                    'id' => $competencia->id,
                    'nombre' => $competencia->nombre,
                    'codigo' => $competencia->codigo,
                ],
                'resultados_aprendizaje' => $resultados->map(function ($resultado) {
                    return [
                        'id' => $resultado->id ?? null,
                        'nombre' => $resultado->nombre ?? 'N/A',
                        'codigo' => $resultado->codigo ?? 'N/A',
                    ];
                }),
            ];
        }

        return $arbol;
    }
}


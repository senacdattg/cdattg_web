<?php

namespace App\Services;

use App\Repositories\AprendizRepository;
use App\Repositories\InstructorRepository;
use App\Repositories\FichaRepository;
use App\Repositories\ProgramaFormacionRepository;
use Illuminate\Support\Collection;

class BusquedaService
{
    protected AprendizRepository $aprendizRepo;
    protected InstructorRepository $instructorRepo;
    protected FichaRepository $fichaRepo;
    protected ProgramaFormacionRepository $programaRepo;

    public function __construct(
        AprendizRepository $aprendizRepo,
        InstructorRepository $instructorRepo,
        FichaRepository $fichaRepo,
        ProgramaFormacionRepository $programaRepo
    ) {
        $this->aprendizRepo = $aprendizRepo;
        $this->instructorRepo = $instructorRepo;
        $this->fichaRepo = $fichaRepo;
        $this->programaRepo = $programaRepo;
    }

    /**
     * Búsqueda global en todo el sistema
     *
     * @param string $termino
     * @param array $tipos
     * @return array
     */
    public function busquedaGlobal(string $termino, array $tipos = []): array
    {
        $tipos = empty($tipos) ? ['aprendices', 'instructores', 'fichas', 'programas'] : $tipos;
        $resultados = [];

        if (in_array('aprendices', $tipos)) {
            $resultados['aprendices'] = $this->aprendizRepo->buscar($termino, 5);
        }

        if (in_array('instructores', $tipos)) {
            $resultados['instructores'] = $this->instructorRepo->buscarInstructores([
                'search' => $termino,
                'per_page' => 5,
            ]);
        }

        if (in_array('programas', $tipos)) {
            $resultados['programas'] = $this->programaRepo->buscar($termino);
        }

        return [
            'termino' => $termino,
            'resultados' => $resultados,
            'total' => array_sum(array_map(function ($resultado) {
                return is_countable($resultado) ? count($resultado) : 0;
            }, $resultados)),
        ];
    }

    /**
     * Búsqueda avanzada de aprendices
     *
     * @param array $criterios
     * @return Collection
     */
    public function busquedaAvanzadaAprendices(array $criterios): Collection
    {
        return $this->aprendizRepo->obtenerAprendicesConFiltros($criterios)->getCollection();
    }

    /**
     * Sugerencias de búsqueda
     *
     * @param string $termino
     * @return array
     */
    public function obtenerSugerencias(string $termino): array
    {
        if (strlen($termino) < 3) {
            return [];
        }

        $aprendices = $this->aprendizRepo->buscar($termino, 3);
        $programas = $this->programaRepo->buscar($termino);

        $sugerencias = [];

        foreach ($aprendices as $aprendiz) {
            $sugerencias[] = [
                'tipo' => 'aprendiz',
                'id' => $aprendiz->id,
                'texto' => $aprendiz->persona->nombre_completo ?? 'N/A',
                'subtexto' => $aprendiz->persona->numero_documento ?? '',
            ];
        }

        foreach ($programas as $programa) {
            $sugerencias[] = [
                'tipo' => 'programa',
                'id' => $programa->id,
                'texto' => $programa->nombre,
                'subtexto' => $programa->codigo ?? '',
            ];
        }

        return $sugerencias;
    }
}


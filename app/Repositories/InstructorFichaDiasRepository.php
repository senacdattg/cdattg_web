<?php

namespace App\Repositories;

use App\Models\InstructorFichaDias;
use Illuminate\Database\Eloquent\Collection;

class InstructorFichaDiasRepository
{
    /**
     * Obtiene días asignados por instructor-ficha
     *
     * @param int $instructorFichaId
     * @return Collection
     */
    public function obtenerPorInstructorFicha(int $instructorFichaId): Collection
    {
        return InstructorFichaDias::where('instructor_ficha_caracterizacion_id', $instructorFichaId)
            ->with(['diaFormacion'])
            ->orderBy('dia_formacion_id')
            ->get();
    }

    /**
     * Crea relación instructor-ficha-día
     *
     * @param array $datos
     * @return InstructorFichaDias
     */
    public function crear(array $datos): InstructorFichaDias
    {
        return InstructorFichaDias::create($datos);
    }

    /**
     * Asigna días a instructor-ficha
     *
     * @param int $instructorFichaId
     * @param array $diasIds
     * @return int
     */
    public function asignarDias(int $instructorFichaId, array $diasIds): int
    {
        $registros = array_map(function ($diaId) use ($instructorFichaId) {
            return [
                'instructor_ficha_caracterizacion_id' => $instructorFichaId,
                'dia_formacion_id' => $diaId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $diasIds);

        InstructorFichaDias::insert($registros);

        return count($registros);
    }
}


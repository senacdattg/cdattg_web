<?php

namespace App\Services;

use App\Repositories\SedeRepository;
use App\Repositories\AmbienteRepository;
use App\Repositories\CentroFormacionRepository;
use Illuminate\Database\Eloquent\Collection;

class InfraestructuraService
{
    protected SedeRepository $sedeRepo;
    protected AmbienteRepository $ambienteRepo;
    protected CentroFormacionRepository $centroRepo;

    public function __construct(
        SedeRepository $sedeRepo,
        AmbienteRepository $ambienteRepo,
        CentroFormacionRepository $centroRepo
    ) {
        $this->sedeRepo = $sedeRepo;
        $this->ambienteRepo = $ambienteRepo;
        $this->centroRepo = $centroRepo;
    }

    /**
     * Obtiene estructura completa de infraestructura
     *
     * @param int|null $regionalId
     * @return array
     */
    public function obtenerEstructuraCompleta(?int $regionalId = null): array
    {
        $centros = $regionalId 
            ? $this->centroRepo->obtenerPorRegional($regionalId)
            : $this->centroRepo->obtenerActivos();

        $estructura = [];

        foreach ($centros as $centro) {
            $sedes = $this->sedeRepo->obtenerPorCentro($centro->id);

            $estructura[] = [
                'centro' => [
                    'id' => $centro->id,
                    'nombre' => $centro->nombre,
                ],
                'sedes' => $sedes->map(function ($sede) {
                    $ambientes = $this->ambienteRepo->obtenerPorSede($sede->id);

                    return [
                        'id' => $sede->id,
                        'nombre' => $sede->nombre,
                        'total_ambientes' => $ambientes->count(),
                        'ambientes_activos' => $ambientes->where('status', true)->count(),
                    ];
                }),
            ];
        }

        return $estructura;
    }

    /**
     * Verifica disponibilidad de ambiente
     *
     * @param int $ambienteId
     * @param string $fecha
     * @param string $horaInicio
     * @param string $horaFin
     * @return array
     */
    public function verificarDisponibilidadAmbiente(int $ambienteId, string $fecha, string $horaInicio, string $horaFin): array
    {
        $disponible = $this->ambienteRepo->estaDisponible($ambienteId, $fecha, $horaInicio, $horaFin);

        return [
            'disponible' => $disponible,
            'ambiente_id' => $ambienteId,
            'fecha' => $fecha,
            'horario' => "{$horaInicio} - {$horaFin}",
        ];
    }

    /**
     * Obtiene ambientes disponibles por sede y horario
     *
     * @param int $sedeId
     * @param string $fecha
     * @param string $horaInicio
     * @param string $horaFin
     * @return Collection
     */
    public function obtenerAmbientesDisponibles(int $sedeId, string $fecha, string $horaInicio, string $horaFin): Collection
    {
        $ambientes = $this->ambienteRepo->obtenerPorSede($sedeId);

        return $ambientes->filter(function ($ambiente) use ($fecha, $horaInicio, $horaFin) {
            return $this->ambienteRepo->estaDisponible($ambiente->id, $fecha, $horaInicio, $horaFin);
        });
    }
}


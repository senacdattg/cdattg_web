<?php

namespace App\Services;

use App\Models\InstructorFichaCaracterizacion;
use App\Repositories\FichaCaracterizacionRepository;

class RegistroActividadesServices
{
    protected $fichaCaracterizacionRepository;

    public function __construct(FichaCaracterizacionRepository $fichaCaracterizacionRepository)
    {
        $this->fichaCaracterizacionRepository = $fichaCaracterizacionRepository;
    }

    public function getActividades(InstructorFichaCaracterizacion $instructorFichaCaracterizacion)
    {
        $fichaCaracterizacion = $this->fichaCaracterizacionRepository->getFichaCaracterizacion($instructorFichaCaracterizacion->ficha_id);
        $programaFormacion = $fichaCaracterizacion->programaFormacion;
        $competenciaActual = $programaFormacion->competenciaActual();
        $rapActual = $competenciaActual->rapActual();
        $guiaAprendizaje = $rapActual->guiasAprendizaje->first();
        $actividades = $guiaAprendizaje->actividades;
        return $actividades;
    }
}

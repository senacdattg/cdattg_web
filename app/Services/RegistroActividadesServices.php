<?php

namespace App\Services;

use App\Models\InstructorFichaCaracterizacion;
use App\Repositories\FichaCaracterizacionRepository;
use App\Repositories\ResultadosAprendizajeRepository;
use App\Repositories\CompetenciaRepository;
use App\Repositories\EvidenciasRepository;
use App\Models\Evidencias;
use App\Models\EvidenciaGuiaAprendizaje;

class RegistroActividadesServices
{
    protected $fichaCaracterizacionRepository;
    protected $resultadosAprendizajeRepository;
    protected $competenciaRepository;
    protected $evidenciaRepository;

    public function __construct(FichaCaracterizacionRepository $fichaCaracterizacionRepository,
                                ResultadosAprendizajeRepository $resultadosAprendizajeRepository,
                                CompetenciaRepository $competenciaRepository,
                                EvidenciasRepository $evidenciaRepository)
    {
        $this->fichaCaracterizacionRepository = $fichaCaracterizacionRepository;
        $this->resultadosAprendizajeRepository = $resultadosAprendizajeRepository;
        $this->competenciaRepository = $competenciaRepository;
        $this->evidenciaRepository = $evidenciaRepository;
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

    public function getRaps(InstructorFichaCaracterizacion $instructorFichaCaracterizacion)
    {
        // Obtener la ficha de caracterización asociada
        $fichaCaracterizacion = $this->fichaCaracterizacionRepository->getFichaCaracterizacion($instructorFichaCaracterizacion->ficha_id);

        // Obtener el programa de formación relacionado
        $programaFormacion = $fichaCaracterizacion->programaFormacion;

        // Obtener la competencia actual
        $competenciaActual = $programaFormacion->competenciaActual();
    }

    public function generateCodigo(InstructorFichaCaracterizacion $instructorFichaCaracterizacion)
    {

        $codigo = '';

        $caracterizacion = $this->fichaCaracterizacionRepository->getFichaCaracterizacion($instructorFichaCaracterizacion->ficha_id);
        $programaFormacion = $caracterizacion->programaFormacion;
        $competenciaActual = $programaFormacion->competenciaActual();
        $rapActual = $competenciaActual->rapActual();
        $guiaAprendizaje = $rapActual->guiasAprendizaje->first();
        $ultimaEvidenciaGuia = $this->evidenciaRepository->getUltimaEvidencia($guiaAprendizaje->id);
        if ($ultimaEvidenciaGuia){
            $numeroCodigoGuiaAprendizaje = obtener_numero_evidencia($ultimaEvidenciaGuia->codigo) + 1;
            $codigo = 'EV-'.$numeroCodigoGuiaAprendizaje;
        } else {
            $codigo = 'EV-0';
        }

        return $codigo;
    }

    public function crearEvidencia($data, InstructorFichaCaracterizacion $caracterizacion)
    {
        $caracterizacion = $this->fichaCaracterizacionRepository->getFichaCaracterizacion($caracterizacion->ficha_id);
        $programaFormacion = $caracterizacion->programaFormacion;
        $competenciaActual = $programaFormacion->competenciaActual();
        $rapActual = $competenciaActual->rapActual();
        $guiaAprendizaje = $rapActual->guiasAprendizaje->first();

        $evidenciaId = Evidencias::create($data);

        $dataEvidenciaGuia = [
            'evidencia_id' => $evidenciaId->id,
            'guia_aprendizaje_id' => $guiaAprendizaje->id,
            'user_create_id' => $data['user_create_id'],
            'user_edit_id' => $data['user_edit_id']
        ];

        EvidenciaGuiaAprendizaje::create($dataEvidenciaGuia);
    }
}

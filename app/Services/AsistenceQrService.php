<?php

namespace App\Services;

use App\Repositories\InstructorFichaCaracterizacionRepository;
use App\Repositories\InstructorRepository;
use App\Repositories\PersonaRepository;
use App\Repositories\ParametroRepository;

class AsistenceQrService
{

    protected $instructorFichaCaracterizacionRepository;
    protected $instructorRepository;
    protected $personaRepository;
    protected $parametroRepository;

    public function __construct(InstructorFichaCaracterizacionRepository $instructorFichaCaracterizacionRepository,
                                InstructorRepository $instructorRepository, PersonaRepository $personaRepository,
                                ParametroRepository $parametroRepository
    )
    {
        $this->instructorFichaCaracterizacionRepository = $instructorFichaCaracterizacionRepository;
        $this->instructorRepository = $instructorRepository;
        $this->personaRepository = $personaRepository;
        $this->parametroRepository = $parametroRepository;
    }

    public function getInstructorFichaIndex($user)
    {
        $instructor = $this->instructorRepository->getInstructor($user->persona_id);

        return $this->instructorFichaCaracterizacionRepository->getInstructorFichaCaracterizacion($instructor->id);
    }

    public function getDiasFormacion()
    {
        return $this->parametroRepository->getDiasFormacion();
    }
}
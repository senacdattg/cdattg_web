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

    public function __construct(
        InstructorFichaCaracterizacionRepository $instructorFichaCaracterizacionRepository,
        InstructorRepository $instructorRepository,
        PersonaRepository $personaRepository,
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

    /**
     * Obtiene datos de caracterización con aprendices y horarios
     *
     * @param int $caracterizacionId
     * @param mixed $user
     * @return array
     */
    public function obtenerDatosCaracterizacion(int $caracterizacionId, $user): array
    {
        $fichaCaracterizacion = \App\Models\FichaCaracterizacion::with([
            'diasFormacion.dia',
            'programaFormacion',
            'instructor.persona',
            'jornadaFormacion'
        ])->find($caracterizacionId);

        if (!$fichaCaracterizacion) {
            return [
                'fichaCaracterizacion' => null,
                'aprendices' => collect(),
                'horarioHoy' => null,
            ];
        }

        // Obtener horario de hoy
        $diaHoy = now()->dayOfWeek;
        $diaId = ($diaHoy == 0) ? 18 : $diaHoy + 11;
        
        $horarioHoy = null;
        if ($fichaCaracterizacion->diasFormacion) {
            $horarioHoy = $fichaCaracterizacion->diasFormacion
                ->where('dia_id', $diaId)
                ->first();

            if ($horarioHoy) {
                $horarioHoy->hora_inicio = \Carbon\Carbon::parse($horarioHoy->hora_inicio)->format('h:i A');
                $horarioHoy->hora_fin = \Carbon\Carbon::parse($horarioHoy->hora_fin)->format('h:i A');
            }
        }

        // Obtener instructor ficha ID
        $instructorFichaId = null;
        if ($user && $user->persona && $user->persona->instructor) {
            $instructor = $user->persona->instructor;
            $instructorFicha = \App\Models\InstructorFichaCaracterizacion::where('instructor_id', $instructor->id)
                ->where('ficha_id', $fichaCaracterizacion->id)
                ->first();
            if ($instructorFicha) {
                $instructorFichaId = $instructorFicha->id;
            }
        }

        // Obtener aprendices con asistencias
        $aprendicesFicha = \App\Models\AprendizFicha::where('ficha_id', $fichaCaracterizacion->id)->get();
        $aprendizPersonaConAsistencia = collect();
        $fechaActual = \Carbon\Carbon::now()->format('Y-m-d');

        foreach ($aprendicesFicha as $af) {
            $aprendiz = \App\Models\Aprendiz::find($af->aprendiz_id);
            if ($aprendiz && $aprendiz->persona) {
                $persona = $aprendiz->persona;

                $asistenciaHoy = null;
                if ($instructorFichaId) {
                    $asistenciaHoy = \App\Models\AsistenciaAprendiz::where('aprendiz_ficha_id', $af->id)
                        ->where('instructor_ficha_id', $instructorFichaId)
                        ->whereDate('created_at', $fechaActual)
                        ->first();
                }

                $persona->asistenciaHoy = $asistenciaHoy;

                if ($persona->asistenciaHoy) {
                    $persona->asistenciaHoy->formatted_hora_ingreso = \Carbon\Carbon::parse($persona->asistenciaHoy->hora_ingreso)->format('h:i A');
                    $persona->asistenciaHoy->formatted_hora_salida = $persona->asistenciaHoy->hora_salida 
                        ? \Carbon\Carbon::parse($persona->asistenciaHoy->hora_salida)->format('h:i A') 
                        : null;
                }

                $aprendizPersonaConAsistencia->push($persona);
            }
        }

        return [
            'fichaCaracterizacion' => $fichaCaracterizacion,
            'aprendices' => $aprendizPersonaConAsistencia,
            'horarioHoy' => $horarioHoy,
        ];
    }
}

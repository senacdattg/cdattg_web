<?php

namespace App\Services;

use App\Models\Instructor;
use App\Models\FichaCaracterizacion;
use App\Models\InstructorFichaCaracterizacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InstructorBusinessRulesService
{
    /**
     * Límite máximo de fichas activas por instructor según reglas SENA
     */
    const MAX_FICHAS_ACTIVAS = 5;

    /**
     * Experiencia mínima requerida para instructores
     */
    const EXPERIENCIA_MINIMA = 1;

    /**
     * Horas máximas por semana para un instructor
     */
    const MAX_HORAS_SEMANA = 48;

    private const TEXTO_SIN_PROGRAMA = 'Sin programa';

    /**
     * Verificar disponibilidad del instructor para una nueva ficha
     */
    public function verificarDisponibilidad(
        Instructor $instructor,
        array $datosFicha,
        ?int $fichaIdActual = null
    ): array {
        $resultado = $this->inicializarResultadoDisponibilidad();

        try {
            if (!$this->verificarEstadoActivo($instructor, $resultado)) {
                return $resultado;
            }

            $fechaInicio = Carbon::parse($datosFicha['fecha_inicio']);
            $fechaFin = Carbon::parse($datosFicha['fecha_fin']);

            $this->evaluarLimiteFichasActivas($instructor, $resultado);
            $this->evaluarConflictosAsignacion(
                $instructor,
                $fechaInicio,
                $fechaFin,
                $datosFicha,
                $fichaIdActual,
                $resultado
            );

            // VALIDACIÓN DESHABILITADA: Verificar carga horaria semanal
            // Esta validación estaba comparando incorrectamente horas totales vs límite semanal
            // $cargaHoraria = $this->calcularCargaHorariaSemanal(
            //     $instructor,
            //     $fechaInicio,
            //     $fechaFin,
            //     $datosFicha['horas_semanales'] ?? 0,
            //     $jornadaId
            // );
            // if ($cargaHoraria > self::MAX_HORAS_SEMANA) {
            //     $resultado['disponible'] = false;
            //     $resultado['razones'][] =
            //         "El instructor excedería la carga horaria máxima semanal en esta jornada "
            //         . "({$cargaHoraria}h > " . self::MAX_HORAS_SEMANA . "h). Ejemplo: Actualmente tiene "
            //         . ($cargaHoraria - ($datosFicha['horas_semanales'] ?? 0))
            //         . "h semanales asignadas en la misma jornada";
            // }

            // NOTA: Validación de especialidades deshabilitada por solicitud del usuario
            // No se valida la especialidad del instructor para la ficha
        } catch (\Exception $e) {
            Log::error('Error verificando disponibilidad del instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'datos_ficha' => $datosFicha
            ]);

            $resultado['disponible'] = false;
            $resultado['razones'][] = 'Error interno al verificar disponibilidad';
        }

        return $resultado;
    }

    /**
     * Verificar si el instructor excede el límite de fichas activas
     */
    public function excedeLimiteFichasActivas(Instructor $instructor): bool
    {
        return $this->contarFichasActivas($instructor) >= self::MAX_FICHAS_ACTIVAS;
    }

    /**
     * Verificar superposición de fechas con fichas existentes considerando jornada y días de la semana
     * @param Instructor $instructor
     * @param Carbon $fechaInicio
     * @param Carbon $fechaFin
     * @param int|null $jornadaId Jornada de la ficha a asignar
     * @param array $diasFormacion Días de formación de la nueva asignación (opcional)
     * @param int|null $fichaIdActual ID de la ficha actual (para excluir si es instructor principal)
     * @return array
     */
    public function verificarSuperposicionFechas(
        Instructor $instructor,
        Carbon $fechaInicio,
        Carbon $fechaFin,
        ?int $jornadaId = null,
        array $diasFormacion = [],
        ?int $fichaIdActual = null
    ): array {
        $conflictos = [];

        $fichasExistentes = $this->obtenerFichasActivasConDetalles($instructor);

        $diasNuevos = $this->extraerDiasNuevos($diasFormacion);

        foreach ($fichasExistentes as $instructorFicha) {
            if ($this->esMismaFichaPrincipal($fichaIdActual, $instructorFicha, $instructor)) {
                continue;
            }

            $ficha = $instructorFicha->ficha;

            if (!$this->hayConflictoFechas($fechaInicio, $fechaFin, $ficha)) {
                continue;
            }

            if ($this->esConflictoJornada($jornadaId, $ficha)) {
                continue;
            }

            $conflictosFicha = $this->resolverConflictoPorDias(
                $diasNuevos,
                $instructorFicha,
                $ficha
            );

            if (empty($diasNuevos) && empty($conflictosFicha)) {
                $conflictosFicha[] = $this->crearConflictoSinDias($ficha);
            }

            $conflictos = array_merge($conflictos, $conflictosFicha);
        }

        return $conflictos;
    }

    /**
     * Verificar si dos rangos de fechas se superponen
     */
    protected function haySuperposicion(Carbon $inicio1, Carbon $fin1, Carbon $inicio2, Carbon $fin2): bool
    {
        return $inicio1->lte($fin2) && $fin1->gte($inicio2);
    }

    private function obtenerFichasActivasConDetalles(Instructor $instructor)
    {
        return $instructor->instructorFichas()
            ->with(['ficha.jornadaFormacion', 'instructorFichaDias.dia'])
            ->whereHas('ficha', function ($q) {
                $q->where('status', true);
            })
            ->get();
    }

    private function extraerDiasNuevos(array $diasFormacion): array
    {
        return collect($diasFormacion)->pluck('dia_id')->filter()->toArray();
    }

    private function esMismaFichaPrincipal(
        ?int $fichaIdActual,
        InstructorFichaCaracterizacion $instructorFicha,
        Instructor $instructor
    ): bool {
        if (!$fichaIdActual) {
            return false;
        }

        $ficha = $instructorFicha->ficha;

        return $ficha->id === $fichaIdActual && $ficha->instructor_id === $instructor->id;
    }

    private function hayConflictoFechas(Carbon $fechaInicio, Carbon $fechaFin, FichaCaracterizacion $ficha): bool
    {
        $fechaInicioExistente = Carbon::parse($ficha->fecha_inicio);
        $fechaFinExistente = Carbon::parse($ficha->fecha_fin);

        return $this->haySuperposicion($fechaInicio, $fechaFin, $fechaInicioExistente, $fechaFinExistente);
    }

    private function esConflictoJornada(?int $jornadaNuevaId, FichaCaracterizacion $ficha): bool
    {
        return $jornadaNuevaId && $ficha->jornada_id && $jornadaNuevaId !== $ficha->jornada_id;
    }

    private function resolverConflictoPorDias(
        array $diasNuevos,
        InstructorFichaCaracterizacion $instructorFicha,
        FichaCaracterizacion $ficha
    ): array {
        if (empty($diasNuevos)) {
            return [];
        }

        $diasExistentes = $instructorFicha->instructorFichaDias->pluck('dia_id')->toArray();
        $diasEnComun = array_intersect($diasNuevos, $diasExistentes);

        if (empty($diasEnComun)) {
            return [];
        }

        $diasNombres = $this->obtenerNombresDias($instructorFicha, $diasEnComun);

        return [
            $this->crearConflicto(
                $ficha,
                [
                    'dias_conflicto' => $diasNombres
                ]
            )
        ];
    }

    private function obtenerNombresDias(InstructorFichaCaracterizacion $instructorFicha, array $diasEnComun): string
    {
        return $instructorFicha->instructorFichaDias
            ->whereIn('dia_id', $diasEnComun)
            ->pluck('dia.name')
            ->filter()
            ->implode(', ');
    }

    private function crearConflictoSinDias(FichaCaracterizacion $ficha): array
    {
        return $this->crearConflicto($ficha);
    }

    private function crearConflicto(FichaCaracterizacion $ficha, array $extra = []): array
    {
        $conflicto = [
            'ficha_id' => $ficha->id,
            'ficha_numero' => $ficha->ficha,
            'fecha_inicio' => $ficha->fecha_inicio,
            'fecha_fin' => $ficha->fecha_fin,
            'programa' => $ficha->programaFormacion->nombre ?? self::TEXTO_SIN_PROGRAMA,
            'jornada' => $ficha->jornadaFormacion->jornada ?? 'Sin jornada'
        ];

        return array_merge($conflicto, $extra);
    }

    /**
     * Calcular carga horaria semanal del instructor
     * @param Instructor $instructor
     * @param Carbon $fechaInicio
     * @param Carbon $fechaFin
     * @param int $horasNuevaFicha Horas de la nueva ficha
     * @param int|null $jornadaId Jornada de la nueva ficha (para filtrar solo misma jornada)
     * @return int Total de horas semanales
     */
    public function calcularCargaHorariaSemanal(
        Instructor $instructor,
        Carbon $fechaInicio,
        Carbon $fechaFin,
        int $horasNuevaFicha = 0,
        ?int $jornadaId = null
    ): int {
        // Obtener fichas activas en el período
        $fichasActivas = $instructor->instructorFichas()
            ->whereHas('ficha', function ($q) use ($fechaInicio, $fechaFin, $jornadaId) {
                $q->where('status', true)
                    ->where(function ($query) use ($fechaInicio, $fechaFin) {
                        $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                            ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                            ->orWhere(function ($subQuery) use ($fechaInicio, $fechaFin) {
                                $subQuery->where('fecha_inicio', '<=', $fechaInicio)
                                    ->where('fecha_fin', '>=', $fechaFin);
                            });
                    });

                // Filtrar solo por la misma jornada si se proporciona
                if ($jornadaId) {
                    $q->where('jornada_id', $jornadaId);
                }
            })
            ->get();

        $totalHoras = 0;

        // Sumar horas de fichas existentes en la misma jornada
        foreach ($fichasActivas as $instructorFicha) {
            $totalHoras += $this->calcularHorasSemanalesFicha($instructorFicha->ficha);
        }

        // Agregar horas de la nueva ficha
        $totalHoras += $horasNuevaFicha;

        return $totalHoras;
    }

    /**
     * Calcular horas semanales de una ficha específica
     */
    protected function calcularHorasSemanalesFicha(FichaCaracterizacion $ficha): int
    {
        // Calcular días de formación por semana
        $diasFormacion = $ficha->diasFormacion->count();

        // Calcular horas por día (asumiendo 8 horas por día como estándar SENA)
        $horasPorDia = 8;

        return $diasFormacion * $horasPorDia;
    }

    /**
     * Verificar si el instructor tiene las especialidades requeridas
     */
    public function tieneEspecialidadesRequeridas(Instructor $instructor, ?string $especialidadRequerida): bool
    {
        if (!$especialidadRequerida) {
            return true; // Si no hay especialidad requerida, cualquier instructor puede tomar la ficha
        }

        if (is_array($instructor->especialidades)) {
            $especialidades = $instructor->especialidades;
        } elseif (is_string($instructor->especialidades)) {
            $especialidades = json_decode($instructor->especialidades, true);
        } else {
            $especialidades = [];
        }

        $especialidades = $especialidades ?? [];
        $especialidadPrincipal = $especialidades['principal'] ?? null;
        $especialidadesSecundarias = $especialidades['secundarias'] ?? [];

        // Verificar si la especialidad requerida coincide con la principal o alguna secundaria
        if ($especialidadPrincipal === $especialidadRequerida) {
            return true;
        }

        return in_array($especialidadRequerida, $especialidadesSecundarias);
    }

    /**
     * Validar que el instructor tenga experiencia mínima
     */
    public function validarExperienciaMinima(Instructor $instructor): bool
    {
        $anosExperiencia = $instructor->anos_experiencia ?? 0;
        return $anosExperiencia >= self::EXPERIENCIA_MINIMA;
    }

    /**
     * Obtener instructores disponibles para una ficha específica
     */
    public function obtenerInstructoresDisponibles(array $criterios): array
    {
        $especialidadRequerida = $criterios['especialidad_requerida'] ?? null;
        $regionalId = $criterios['regional_id'] ?? null;

        $query = Instructor::with(['persona', 'regional'])
            ->where('status', true);

        // Filtrar por regional
        if ($regionalId) {
            $query->where('regional_id', $regionalId);
        }

        // Filtrar por especialidad si se especifica
        if ($especialidadRequerida) {
            $query->where(function ($q) use ($especialidadRequerida) {
                $q->whereJsonContains('especialidades->principal', $especialidadRequerida)
                    ->orWhereJsonContains('especialidades->secundarias', $especialidadRequerida);
            });
        }

        $instructores = $query->get();
        $disponibles = [];

        foreach ($instructores as $instructor) {
            $disponibilidad = $this->verificarDisponibilidad($instructor, $criterios);

            if ($disponibilidad['disponible']) {
                $disponibles[] = [
                    'instructor' => $instructor,
                    'disponibilidad' => $disponibilidad
                ];
            }
        }

        return $disponibles;
    }

    /**
     * Validar reglas específicas del SENA para asignación de fichas
     */
    public function validarReglasSENA(Instructor $instructor, array $datosFicha): array
    {
        $resultado = $this->inicializarResultadoReglasSena();

        try {
            $this->evaluarExperienciaMinima($instructor, $resultado);
            $this->evaluarRegionalAsignacion($instructor, $datosFicha, $resultado);
            $this->evaluarDisponibilidadGeneral($instructor, $datosFicha, $resultado);
            $this->agregarAdvertenciasGenerales($instructor, $resultado);
        } catch (\Exception $e) {
            Log::error('Error validando reglas SENA', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'datos_ficha' => $datosFicha
            ]);

            $resultado['valido'] = false;
            $resultado['errores'][] = 'Error interno al validar reglas de negocio';
        }

        return $resultado;
    }

    /**
     * Obtener estadísticas de carga de trabajo por instructor
     */
    public function obtenerEstadisticasCargaTrabajo(): array
    {
        $instructores = Instructor::with(['instructorFichas.ficha'])
            ->where('status', true)
            ->get();

        return $instructores->map(function (Instructor $instructor) {
            $fichasActivas = $this->contarFichasActivas($instructor);
            $totalHoras = $this->sumarTotalHorasInstructor($instructor, true);

            return [
                'instructor_id' => $instructor->id,
                'nombre' => $instructor->nombre_completo,
                'fichas_activas' => $fichasActivas,
                'total_horas' => $totalHoras,
                'carga_alta' => $fichasActivas >= 4 || $totalHoras >= 200,
                'disponible_para_mas' => $fichasActivas < self::MAX_FICHAS_ACTIVAS
            ];
        })->toArray();
    }

    /**
     * Obtener fichas activas del instructor
     */
    private function obtenerFichasActivas(Instructor $instructor)
    {
        return $instructor->instructorFichas()
            ->with(['ficha.programaFormacion'])
            ->whereHas('ficha', function ($q) {
                $q->where('status', true)
                    ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->get()
            ->pluck('ficha');
    }

    public function contarFichasActivas(Instructor $instructor): int
    {
        return $instructor->instructorFichas()
            ->whereHas('ficha', function ($q) {
                $q->where('status', true)
                    ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->count();
    }

    public function contarFichasFinalizadas(Instructor $instructor): int
    {
        return $instructor->instructorFichas()
            ->whereHas('ficha', function ($q) {
                $q->where('fecha_fin', '<', now()->toDateString());
            })
            ->count();
    }

    public function contarTotalFichasAsignadas(Instructor $instructor): int
    {
        return $instructor->instructorFichas()->count();
    }

    public function contarFichasProximas(Instructor $instructor, int $dias = 30): int
    {
        return $instructor->instructorFichas()
            ->whereHas('ficha', function ($q) use ($dias) {
                $q->where('status', true)
                    ->where('fecha_inicio', '>=', now()->toDateString())
                    ->where('fecha_inicio', '<=', now()->addDays($dias)->toDateString());
            })
            ->count();
    }

    public function sumarTotalHorasInstructor(Instructor $instructor, bool $soloActivas = false): int
    {
        $query = $instructor->instructorFichas();

        if ($soloActivas) {
            $query->whereHas('ficha', function ($q) {
                $q->where('status', true)
                    ->where('fecha_fin', '>=', now()->toDateString());
            });
        }

        return (int) $query->sum('total_horas_instructor');
    }

    public function sumarHorasDelMes(Instructor $instructor, Carbon $fechaReferencia): int
    {
        return (int) $instructor->instructorFichas()
            ->whereHas('ficha', function ($q) use ($fechaReferencia) {
                $q->whereMonth('fecha_inicio', $fechaReferencia->month)
                    ->whereYear('fecha_inicio', $fechaReferencia->year);
            })
            ->sum('total_horas_instructor');
    }

    public function promedioHorasUltimosMeses(Instructor $instructor, int $cantidadMeses = 6): float
    {
        if ($cantidadMeses <= 0) {
            return 0.0;
        }

        $totalHoras = $instructor->instructorFichas()
            ->whereHas('ficha', function ($q) use ($cantidadMeses) {
                $q->where('fecha_inicio', '>=', now()->subMonths($cantidadMeses)->toDateString());
            })
            ->sum('total_horas_instructor');

        return $totalHoras / $cantidadMeses;
    }

    public function obtenerResumenFichas(Instructor $instructor): array
    {
        return [
            'total' => $instructor->instructorFichas()->count(),
            'activas' => $this->contarFichasActivas($instructor),
            'finalizadas' => $this->contarFichasFinalizadas($instructor),
            'proximas' => $this->contarFichasProximas($instructor),
            'total_horas' => $this->sumarTotalHorasInstructor($instructor),
        ];
    }

    private function inicializarResultadoDisponibilidad(): array
    {
        return [
            'disponible' => true,
            'razones' => [],
            'conflictos' => []
        ];
    }

    private function verificarEstadoActivo(Instructor $instructor, array &$resultado): bool
    {
        if ($instructor->status) {
            return true;
        }

        $resultado['disponible'] = false;
        $resultado['razones'][] = 'El instructor está inactivo';

        return false;
    }

    private function evaluarLimiteFichasActivas(Instructor $instructor, array &$resultado): void
    {
        if (!$this->excedeLimiteFichasActivas($instructor)) {
            return;
        }

        $resultado['disponible'] = false;

        $fichasActivas = $this->obtenerFichasActivas($instructor);
        $ejemploFichas = $fichasActivas->take(2)->map(function ($ficha) {
            return "Ficha {$ficha->ficha} ({$ficha->programaFormacion->nombre})";
        })->implode(', ');

        $resultado['razones'][] =
            "El instructor excede el límite máximo de fichas activas ("
            . count($fichasActivas)
            . "/"
            . self::MAX_FICHAS_ACTIVAS
            . "). Ejemplo: {$ejemploFichas}";
    }

    private function evaluarConflictosAsignacion(
        Instructor $instructor,
        Carbon $fechaInicio,
        Carbon $fechaFin,
        array $datosFicha,
        ?int $fichaIdActual,
        array &$resultado
    ): void {
        $jornadaId = $datosFicha['jornada_id'] ?? null;
        $diasFormacion = $datosFicha['dias_formacion'] ?? [];
        $conflictos = $this->verificarSuperposicionFechas(
            $instructor,
            $fechaInicio,
            $fechaFin,
            $jornadaId,
            $diasFormacion,
            $fichaIdActual
        );

        if (empty($conflictos)) {
            return;
        }

        $resultado['disponible'] = false;
        $resultado['conflictos'] = $conflictos;
        $resultado['razones'][] = $this->construirMensajeConflicto($conflictos[0]);
    }

    private function construirMensajeConflicto(array $conflicto): string
    {
        $programaNombre = $conflicto['programa'] ?? self::TEXTO_SIN_PROGRAMA;
        $jornadaInfo = isset($conflicto['jornada']) ? " (Jornada: {$conflicto['jornada']})" : '';
        $diasTexto = $conflicto['dias_conflicto'] ?? '';
        $diasInfo = $diasTexto !== '' ? " en los días: {$diasTexto}" : '';
        $fechaInicio = Carbon::parse($conflicto['fecha_inicio'])->format('d/m/Y');
        $fechaFin = Carbon::parse($conflicto['fecha_fin'])->format('d/m/Y');

        return "El instructor tiene fichas con fechas superpuestas en la misma jornada{$diasInfo}. "
            . "Ejemplo: Ficha {$conflicto['ficha_numero']} ({$programaNombre}){$jornadaInfo} del "
            . "{$fechaInicio} al {$fechaFin}";
    }

    private function inicializarResultadoReglasSena(): array
    {
        return [
            'valido' => true,
            'errores' => [],
            'advertencias' => []
        ];
    }

    private function evaluarExperienciaMinima(Instructor $instructor, array &$resultado): void
    {
        if ($this->validarExperienciaMinima($instructor)) {
            return;
        }

        $resultado['valido'] = false;
        $experienciaActual = $instructor->anos_experiencia ?? 0;
        $resultado['errores'][] =
            "El instructor no cumple con la experiencia mínima requerida ({$experienciaActual}/"
            . self::EXPERIENCIA_MINIMA
            . " años). Ejemplo: Necesita al menos "
            . self::EXPERIENCIA_MINIMA
            . " año de experiencia";
    }

    private function evaluarRegionalAsignacion(Instructor $instructor, array $datosFicha, array &$resultado): void
    {
        if (!isset($datosFicha['regional_id']) || $instructor->regional_id == $datosFicha['regional_id']) {
            return;
        }

        $resultado['valido'] = false;
        $regionalInstructor = $instructor->regional->nombre ?? 'Sin regional';
        $regionalFicha = $datosFicha['regional_nombre'] ?? 'Sin especificar';
        $resultado['errores'][] =
            "El instructor debe pertenecer a la misma regional que la ficha. Instructor: "
            . "{$regionalInstructor}, Ficha: {$regionalFicha}";
    }

    private function evaluarDisponibilidadGeneral(Instructor $instructor, array $datosFicha, array &$resultado): void
    {
        if (!$resultado['valido']) {
            return;
        }

        $disponibilidad = $this->verificarDisponibilidad($instructor, $datosFicha);
        if ($disponibilidad['disponible']) {
            return;
        }

        $resultado['valido'] = false;
        $resultado['errores'] = array_merge($resultado['errores'], $disponibilidad['razones']);
    }

    private function agregarAdvertenciasGenerales(Instructor $instructor, array &$resultado): void
    {
        if ($this->contarTotalFichasAsignadas($instructor) >= 4) {
            $resultado['advertencias'][] = 'El instructor ya tiene 4 fichas asignadas, considere la carga de trabajo';
        }

        if (($instructor->anos_experiencia ?? 0) < 3) {
            $resultado['advertencias'][] = 'Instructor con poca experiencia, considere asignar fichas básicas';
        }
    }
}

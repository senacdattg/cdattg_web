<?php

namespace App\Services;

use App\Repositories\AsistenciaAprendizRepository;
use App\Services\JornadaValidationService;
use App\Models\AsistenciaAprendiz;
use App\Events\NuevaAsistenciaRegistrada;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AsistenciaService
{
    protected AsistenciaAprendizRepository $repository;
    protected JornadaValidationService $jornadaValidation;

    public function __construct(
        AsistenciaAprendizRepository $repository,
        JornadaValidationService $jornadaValidation
    ) {
        $this->repository = $repository;
        $this->jornadaValidation = $jornadaValidation;
    }

    /**
     * Obtiene asistencias por ficha
     *
     * @param int $fichaId
     * @return Collection
     */
    public function obtenerPorFicha(int $fichaId): Collection
    {
        return $this->repository->obtenerPorFicha($fichaId);
    }

    /**
     * Obtiene asistencias por ficha y rango de fechas
     *
     * @param int $fichaId
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return Collection
     */
    public function obtenerPorFichaYFechas(int $fichaId, string $fechaInicio, string $fechaFin): Collection
    {
        return $this->repository->obtenerPorFichaYFechas($fichaId, $fechaInicio, $fechaFin);
    }

    /**
     * Obtiene asistencias por número de documento
     *
     * @param string $numeroDocumento
     * @return Collection
     */
    public function obtenerPorDocumento(string $numeroDocumento): Collection
    {
        return $this->repository->obtenerPorDocumento($numeroDocumento);
    }

    /**
     * Obtiene documentos únicos por ficha
     *
     * @param int $fichaId
     * @return Collection
     */
    public function obtenerDocumentosPorFicha(int $fichaId): Collection
    {
        return $this->repository->obtenerDocumentosPorFicha($fichaId);
    }

    /**
     * Obtiene lista de asistencias del día por ficha y jornada
     *
     * @param string $ficha
     * @param string $jornada
     * @return Collection|null
     */
    public function obtenerListaDelDia(string $ficha, string $jornada): ?Collection
    {
        $fechaActual = Carbon::now()->format('Y-m-d');
        $horaActual = Carbon::now()->format('H:i:s');

        // Validar que la hora actual esté dentro de la jornada
        if (!$this->jornadaValidation->validarHorarioJornada($horaActual, $jornada)) {
            return null;
        }

        return $this->repository->obtenerPorFichaJornadaYFecha($ficha, $jornada, $fechaActual);
    }

    /**
     * Registra asistencia individual
     *
     * @param array $datos
     * @return AsistenciaAprendiz
     */
    public function registrarAsistencia(array $datos): AsistenciaAprendiz
    {
        return DB::transaction(function () use ($datos) {
            $asistencia = $this->repository->crear($datos);

            // Disparar evento de nueva asistencia
            event(new NuevaAsistenciaRegistrada([
                'id' => $asistencia->id,
                'aprendiz' => $asistencia->nombres . ' ' . $asistencia->apellidos,
                'estado' => 'entrada',
                'timestamp' => now()->toISOString(),
            ]));

            Log::info('Asistencia registrada', [
                'asistencia_id' => $asistencia->id,
                'numero_identificacion' => $asistencia->numero_identificacion,
                'hora_ingreso' => $asistencia->hora_ingreso,
            ]);

            return $asistencia;
        });
    }

    /**
     * Registra múltiples asistencias en lote
     *
     * @param array $asistencias
     * @param int $caracterizacionId
     * @return int
     */
    public function registrarAsistenciaLote(array $asistencias, int $caracterizacionId): int
    {
        return DB::transaction(function () use ($asistencias, $caracterizacionId) {
            $cantidad = $this->repository->crearLote($asistencias, $caracterizacionId);

            Log::info('Asistencias registradas en lote', [
                'cantidad' => $cantidad,
                'caracterizacion_id' => $caracterizacionId,
            ]);

            return $cantidad;
        });
    }

    /**
     * Actualiza hora de salida para todas las asistencias del día
     *
     * @param int $caracterizacionId
     * @param string $fecha
     * @param string $horaSalida
     * @return int
     */
    public function actualizarHoraSalida(int $caracterizacionId, string $fecha, string $horaSalida): int
    {
        return DB::transaction(function () use ($caracterizacionId, $fecha, $horaSalida) {
            $cantidad = $this->repository->actualizarHoraSalida($caracterizacionId, $fecha, $horaSalida);

            Log::info('Horas de salida actualizadas', [
                'cantidad' => $cantidad,
                'caracterizacion_id' => $caracterizacionId,
                'fecha' => $fecha,
            ]);

            return $cantidad;
        });
    }

    /**
     * Actualiza novedad de entrada con validaciones
     *
     * @param int $caracterizacionId
     * @param string $numeroIdentificacion
     * @param string $horaIngreso
     * @param string $novedadEntrada
     * @param string $jornada
     * @return bool
     * @throws \Exception
     */
    public function actualizarNovedadEntrada(
        int $caracterizacionId,
        string $numeroIdentificacion,
        string $horaIngreso,
        string $novedadEntrada,
        string $jornada
    ): bool {
        $fechaActual = Carbon::now()->format('Y-m-d');
        $horaActual = Carbon::now()->format('H:i:s');

        // Validar que estamos en la jornada correcta
        if (!$this->jornadaValidation->validarAsistenciaEnJornada($horaIngreso, $horaActual, $jornada)) {
            throw new \Exception('No se puede actualizar la novedad fuera de la jornada correspondiente.');
        }

        $asistencia = $this->repository->buscarAsistencia($caracterizacionId, $numeroIdentificacion, $horaIngreso);

        if (!$asistencia) {
            throw new \Exception('Asistencia no encontrada.');
        }

        // Validar que la asistencia sea del día actual
        if ($asistencia->created_at->format('Y-m-d') !== $fechaActual) {
            throw new \Exception('Solo se pueden actualizar novedades de asistencias del día actual.');
        }

        // Solo actualizar hora_ingreso para jornada Mañana
        $nuevaHoraIngreso = ($jornada === 'Mañana') ? Carbon::now()->format('H:i:s') : null;

        $actualizado = $this->repository->actualizarNovedadEntrada($asistencia->id, $novedadEntrada, $nuevaHoraIngreso);

        Log::info('Novedad de entrada actualizada', [
            'asistencia_id' => $asistencia->id,
            'novedad' => $novedadEntrada,
            'nueva_hora_ingreso' => $nuevaHoraIngreso,
        ]);

        return $actualizado;
    }

    /**
     * Actualiza novedad de salida con validaciones
     *
     * @param int $caracterizacionId
     * @param string $numeroIdentificacion
     * @param string $horaIngreso
     * @param string $novedadSalida
     * @param string $jornada
     * @return bool
     * @throws \Exception
     */
    public function actualizarNovedadSalida(
        int $caracterizacionId,
        string $numeroIdentificacion,
        string $horaIngreso,
        string $novedadSalida,
        string $jornada
    ): bool {
        $fechaActual = Carbon::now()->format('Y-m-d');
        $horaActual = Carbon::now()->format('H:i:s');

        // Validar que estamos en la jornada correcta
        if (!$this->jornadaValidation->validarAsistenciaEnJornada($horaIngreso, $horaActual, $jornada)) {
            throw new \Exception('No se puede actualizar la novedad fuera de la jornada correspondiente.');
        }

        $asistencia = $this->repository->buscarAsistencia($caracterizacionId, $numeroIdentificacion, $horaIngreso);

        if (!$asistencia) {
            throw new \Exception('Asistencia no encontrada.');
        }

        // Validar que la asistencia sea del día actual
        if ($asistencia->created_at->format('Y-m-d') !== $fechaActual) {
            throw new \Exception('Solo se pueden actualizar novedades de asistencias del día actual.');
        }

        $horaSalida = Carbon::now()->format('H:i:s');
        $actualizado = $this->repository->actualizarNovedadSalida($asistencia->id, $novedadSalida, $horaSalida);

        // Disparar evento de salida
        event(new NuevaAsistenciaRegistrada([
            'id' => $asistencia->id,
            'aprendiz' => $asistencia->nombres . ' ' . $asistencia->apellidos,
            'estado' => 'salida',
            'timestamp' => now()->toISOString(),
        ]));

        Log::info('Novedad de salida actualizada', [
            'asistencia_id' => $asistencia->id,
            'novedad' => $novedadSalida,
            'hora_salida' => $horaSalida,
        ]);

        return $actualizado;
    }

    /**
     * Obtiene estadísticas de asistencia
     *
     * @param int $fichaId
     * @param string|null $fechaInicio
     * @param string|null $fechaFin
     * @return array
     */
    public function obtenerEstadisticas(int $fichaId, ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        return $this->repository->obtenerEstadisticas($fichaId, $fechaInicio, $fechaFin);
    }

    /**
     * Verifica si ya existe asistencia del día para un aprendiz
     *
     * @param string $numeroDocumento
     * @param string|null $fecha
     * @return bool
     */
    public function existeAsistenciaDelDia(string $numeroDocumento, ?string $fecha = null): bool
    {
        $fecha = $fecha ?? Carbon::now()->format('Y-m-d');
        return $this->repository->existeAsistenciaDelDia($numeroDocumento, $fecha);
    }
}


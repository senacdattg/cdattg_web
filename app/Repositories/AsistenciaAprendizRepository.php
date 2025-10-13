<?php

namespace App\Repositories;

use App\Models\AsistenciaAprendiz;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AsistenciaAprendizRepository
{
    /**
     * Obtiene asistencias por ficha
     *
     * @param int $fichaId
     * @return Collection
     */
    public function obtenerPorFicha(int $fichaId): Collection
    {
        return AsistenciaAprendiz::whereHas('caracterizacion', function ($query) use ($fichaId) {
            $query->where('ficha_id', $fichaId);
        })
        ->with(['caracterizacion.ficha', 'caracterizacion.jornada'])
        ->orderBy('created_at', 'desc')
        ->get();
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
        return AsistenciaAprendiz::whereHas('caracterizacion', function ($query) use ($fichaId) {
            $query->where('ficha_id', $fichaId);
        })
        ->whereBetween('created_at', [$fechaInicio, $fechaFin])
        ->with(['caracterizacion.ficha', 'caracterizacion.jornada'])
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * Obtiene asistencias por número de documento
     *
     * @param string $numeroDocumento
     * @return Collection
     */
    public function obtenerPorDocumento(string $numeroDocumento): Collection
    {
        return AsistenciaAprendiz::where('numero_identificacion', $numeroDocumento)
            ->with(['caracterizacion.ficha', 'caracterizacion.jornada'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtiene documentos únicos por ficha
     *
     * @param int $fichaId
     * @return Collection
     */
    public function obtenerDocumentosPorFicha(int $fichaId): Collection
    {
        return AsistenciaAprendiz::select('numero_identificacion')
            ->whereHas('caracterizacion', function ($query) use ($fichaId) {
                $query->where('ficha_id', $fichaId);
            })
            ->distinct()
            ->get();
    }

    /**
     * Obtiene asistencias del día por ficha y jornada
     *
     * @param string $ficha
     * @param string $jornada
     * @param string $fecha
     * @return Collection
     */
    public function obtenerPorFichaJornadaYFecha(string $ficha, string $jornada, string $fecha): Collection
    {
        return AsistenciaAprendiz::whereHas('caracterizacion', function ($query) use ($ficha, $jornada) {
            $query->whereHas('ficha', function ($query) use ($ficha) {
                $query->where('ficha', $ficha);
            })->whereHas('jornada', function ($query) use ($jornada) {
                $query->where('jornada', $jornada);
            });
        })
        ->whereDate('created_at', $fecha)
        ->with(['caracterizacion.ficha', 'caracterizacion.jornada'])
        ->get();
    }

    /**
     * Crea una asistencia individual
     *
     * @param array $datos
     * @return AsistenciaAprendiz
     */
    public function crear(array $datos): AsistenciaAprendiz
    {
        // Formatear hora de ingreso si es necesario
        if (isset($datos['hora_ingreso'])) {
            $datos['hora_ingreso'] = Carbon::parse($datos['hora_ingreso'])->format('Y-m-d H:i:s');
        }

        return AsistenciaAprendiz::create($datos);
    }

    /**
     * Crea asistencias en lote
     *
     * @param array $asistencias
     * @param int $caracterizacionId
     * @return int Número de asistencias creadas
     */
    public function crearLote(array $asistencias, int $caracterizacionId): int
    {
        $registros = [];
        
        foreach ($asistencias as $asistencia) {
            $registros[] = [
                'caracterizacion_id' => $caracterizacionId,
                'nombres' => $asistencia['nombres'],
                'apellidos' => $asistencia['apellidos'],
                'numero_identificacion' => $asistencia['numero_identificacion'],
                'hora_ingreso' => Carbon::parse($asistencia['hora_ingreso'])->format('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        AsistenciaAprendiz::insert($registros);
        
        return count($registros);
    }

    /**
     * Actualiza la hora de salida de asistencias por caracterización y fecha
     *
     * @param int $caracterizacionId
     * @param string $fecha
     * @param string $horaSalida
     * @return int Número de registros actualizados
     */
    public function actualizarHoraSalida(int $caracterizacionId, string $fecha, string $horaSalida): int
    {
        return AsistenciaAprendiz::where('caracterizacion_id', $caracterizacionId)
            ->whereDate('created_at', $fecha)
            ->whereNull('hora_salida')
            ->update([
                'hora_salida' => Carbon::parse($horaSalida)->format('Y-m-d H:i:s')
            ]);
    }

    /**
     * Busca una asistencia específica por criterios
     *
     * @param int $caracterizacionId
     * @param string $numeroIdentificacion
     * @param string $horaIngreso
     * @return AsistenciaAprendiz|null
     */
    public function buscarAsistencia(int $caracterizacionId, string $numeroIdentificacion, string $horaIngreso): ?AsistenciaAprendiz
    {
        $horaIngresoFormateada = Carbon::parse($horaIngreso)->format('H:i:s');
        
        return AsistenciaAprendiz::where('caracterizacion_id', $caracterizacionId)
            ->where('numero_identificacion', $numeroIdentificacion)
            ->whereTime('hora_ingreso', $horaIngresoFormateada)
            ->first();
    }

    /**
     * Actualiza una asistencia existente
     *
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar(int $id, array $datos): bool
    {
        return AsistenciaAprendiz::where('id', $id)->update($datos);
    }

    /**
     * Actualiza novedad de entrada
     *
     * @param int $id
     * @param string $novedad
     * @param string|null $horaIngreso
     * @return bool
     */
    public function actualizarNovedadEntrada(int $id, string $novedad, ?string $horaIngreso = null): bool
    {
        $datos = ['novedad_entrada' => $novedad];
        
        if ($horaIngreso) {
            $datos['hora_ingreso'] = Carbon::parse($horaIngreso)->format('Y-m-d H:i:s');
        }

        return $this->actualizar($id, $datos);
    }

    /**
     * Actualiza novedad de salida
     *
     * @param int $id
     * @param string $novedad
     * @param string|null $horaSalida
     * @return bool
     */
    public function actualizarNovedadSalida(int $id, string $novedad, ?string $horaSalida = null): bool
    {
        $datos = ['novedad_salida' => $novedad];
        
        if ($horaSalida) {
            $datos['hora_salida'] = Carbon::parse($horaSalida)->format('Y-m-d H:i:s');
        }

        return $this->actualizar($id, $datos);
    }

    /**
     * Obtiene estadísticas de asistencia por ficha
     *
     * @param int $fichaId
     * @param string|null $fechaInicio
     * @param string|null $fechaFin
     * @return array
     */
    public function obtenerEstadisticas(int $fichaId, ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $query = AsistenciaAprendiz::whereHas('caracterizacion', function ($q) use ($fichaId) {
            $q->where('ficha_id', $fichaId);
        });

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        $asistencias = $query->get();

        return [
            'total_registros' => $asistencias->count(),
            'con_hora_salida' => $asistencias->whereNotNull('hora_salida')->count(),
            'sin_hora_salida' => $asistencias->whereNull('hora_salida')->count(),
            'con_novedad_entrada' => $asistencias->whereNotNull('novedad_entrada')->count(),
            'con_novedad_salida' => $asistencias->whereNotNull('novedad_salida')->count(),
            'aprendices_unicos' => $asistencias->unique('numero_identificacion')->count(),
        ];
    }

    /**
     * Verifica si existe asistencia para el día
     *
     * @param string $numeroDocumento
     * @param string $fecha
     * @return bool
     */
    public function existeAsistenciaDelDia(string $numeroDocumento, string $fecha): bool
    {
        return AsistenciaAprendiz::where('numero_identificacion', $numeroDocumento)
            ->whereDate('created_at', $fecha)
            ->exists();
    }
}


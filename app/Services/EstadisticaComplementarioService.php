<?php

namespace App\Services;

use App\Models\AspiranteComplementario;
use App\Models\ComplementarioOfertado;

class EstadisticaComplementarioService
{
    /**
     * Obtener estadísticas reales de la base de datos
     */
    public function obtenerEstadisticasReales($filtros = [])
    {
        // Total de aspirantes
        $totalAspirantes = AspiranteComplementario::count();

        // Aspirantes aceptados (estado 3 = Aceptado)
        $aspirantesAceptados = AspiranteComplementario::where('estado', 3)->count();

        // Aspirantes pendientes (estado 1 = En proceso, 2 = Documento subido)
        $aspirantesPendientes = AspiranteComplementario::whereIn('estado', [1, 2])->count();

        // Programas activos
        $programasActivos = ComplementarioOfertado::where('estado', 1)->count();

        // Tendencia de inscripciones por mes (últimos 6 meses)
        $tendenciaInscripciones = AspiranteComplementario::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as total
            ')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Distribución por programas
        $distribucionProgramas = AspiranteComplementario::selectRaw('
                complementarios_ofertados.nombre as programa,
                COUNT(*) as total
            ')
            ->join('complementarios_ofertados', 'aspirantes_complementarios.complementario_id', '=', 'complementarios_ofertados.id')
            ->groupBy('complementarios_ofertados.nombre')
            ->orderBy('total', 'desc')
            ->get();

        // Programas con mayor demanda
        $programasDemanda = AspiranteComplementario::selectRaw('
                complementarios_ofertados.nombre as programa,
                COUNT(*) as total_aspirantes,
                SUM(CASE WHEN aspirantes_complementarios.estado = 3 THEN 1 ELSE 0 END) as aceptados,
                SUM(CASE WHEN aspirantes_complementarios.estado IN (1, 2) THEN 1 ELSE 0 END) as pendientes
            ')
            ->join('complementarios_ofertados', 'aspirantes_complementarios.complementario_id', '=', 'complementarios_ofertados.id')
            ->groupBy('complementarios_ofertados.nombre', 'complementarios_ofertados.id')
            ->orderBy('total_aspirantes', 'desc')
            ->limit(10)
            ->get()
            ->map(function($programa) {
                $tasaAceptacion = $programa->total_aspirantes > 0
                    ? round(($programa->aceptados / $programa->total_aspirantes) * 100, 1)
                    : 0;

                return [
                    'programa' => $programa->programa,
                    'total_aspirantes' => $programa->total_aspirantes,
                    'aceptados' => $programa->aceptados,
                    'pendientes' => $programa->pendientes,
                    'tasa_aceptacion' => $tasaAceptacion
                ];
            });

        return [
            'total_aspirantes' => $totalAspirantes,
            'aspirantes_aceptados' => $aspirantesAceptados,
            'aspirantes_pendientes' => $aspirantesPendientes,
            'programas_activos' => $programasActivos,
            'tendencia_inscripciones' => $tendenciaInscripciones,
            'distribucion_programas' => $distribucionProgramas,
            'programas_demanda' => $programasDemanda
        ];
    }

    /**
     * Obtener estadísticas filtradas por criterios específicos
     */
    public function obtenerEstadisticasFiltradas($filtros)
    {
        $query = AspiranteComplementario::with(['persona', 'complementario']);

        // Aplicar filtros de fecha
        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            $query->whereBetween('created_at', [$filtros['fecha_inicio'], $filtros['fecha_fin']]);
        }

        // Aplicar filtros de departamento
        if (isset($filtros['departamento_id'])) {
            $query->whereHas('persona', function($q) use ($filtros) {
                $q->where('departamento_id', $filtros['departamento_id']);
            });
        }

        // Aplicar filtros de municipio
        if (isset($filtros['municipio_id'])) {
            $query->whereHas('persona', function($q) use ($filtros) {
                $q->where('municipio_id', $filtros['municipio_id']);
            });
        }

        // Aplicar filtros de programa
        if (isset($filtros['programa_id'])) {
            $query->where('complementario_id', $filtros['programa_id']);
        }

        return [
            'total_filtrado' => $query->count(),
            'aceptados_filtrado' => (clone $query)->where('estado', 3)->count(),
            'pendientes_filtrado' => (clone $query)->whereIn('estado', [1, 2])->count(),
            'datos' => $query->get()
        ];
    }

    /**
     * Generar reporte de tendencias mensuales
     */
    public function generarReporteTendencias($meses = 12)
    {
        return AspiranteComplementario::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as total_inscripciones,
                SUM(CASE WHEN estado = 3 THEN 1 ELSE 0 END) as aceptados,
                SUM(CASE WHEN estado IN (1, 2) THEN 1 ELSE 0 END) as pendientes
            ')
            ->where('created_at', '>=', now()->subMonths($meses))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * Obtener estadísticas por género
     */
    public function obtenerEstadisticasPorGenero()
    {
        return AspiranteComplementario::selectRaw('
                parametros.name as genero,
                COUNT(*) as total
            ')
            ->join('personas', 'aspirantes_complementarios.persona_id', '=', 'personas.id')
            ->join('parametros', 'personas.genero', '=', 'parametros.id')
            ->groupBy('personas.genero', 'parametros.name')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * Obtener estadísticas por rango de edad
     */
    public function obtenerEstadisticasPorEdad()
    {
        return AspiranteComplementario::selectRaw('
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) < 18 THEN "Menor de 18"
                    WHEN TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) BETWEEN 18 AND 25 THEN "18-25 años"
                    WHEN TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) BETWEEN 26 AND 35 THEN "26-35 años"
                    WHEN TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) BETWEEN 36 AND 45 THEN "36-45 años"
                    WHEN TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) BETWEEN 46 AND 55 THEN "46-55 años"
                    ELSE "Mayor de 55"
                END as rango_edad,
                COUNT(*) as total
            ')
            ->join('personas', 'aspirantes_complementarios.persona_id', '=', 'personas.id')
            ->groupByRaw('CASE
                WHEN TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) < 18 THEN "Menor de 18"
                WHEN TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) BETWEEN 18 AND 25 THEN "18-25 años"
                WHEN TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) BETWEEN 26 AND 35 THEN "26-35 años"
                WHEN TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) BETWEEN 36 AND 45 THEN "36-45 años"
                WHEN TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) BETWEEN 46 AND 55 THEN "46-55 años"
                ELSE "Mayor de 55"
            END')
            ->orderBy('total', 'desc')
            ->get();
    }
}
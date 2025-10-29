<?php

namespace App\Services;

use App\Repositories\AsistenciaAprendizRepository;
use App\Repositories\AprendizRepository;
use App\Repositories\FichaRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteService
{
    protected AsistenciaAprendizRepository $asistenciaRepo;
    protected AprendizRepository $aprendizRepo;
    protected FichaRepository $fichaRepo;

    public function __construct(
        AsistenciaAprendizRepository $asistenciaRepo,
        AprendizRepository $aprendizRepo,
        FichaRepository $fichaRepo
    ) {
        $this->asistenciaRepo = $asistenciaRepo;
        $this->aprendizRepo = $aprendizRepo;
        $this->fichaRepo = $fichaRepo;
    }

    /**
     * Genera reporte de asistencia por ficha y rango de fechas
     *
     * @param int $fichaId
     * @param string $fechaInicio
     * @param string $fechaFin
     * @param string $formato (pdf|excel|array)
     * @return mixed
     */
    public function generarReporteAsistencia(int $fichaId, string $fechaInicio, string $fechaFin, string $formato = 'array')
    {
        try {
            // Obtener datos
            $asistencias = $this->asistenciaRepo->obtenerPorFichaYFechas($fichaId, $fechaInicio, $fechaFin);
            $estadisticas = $this->asistenciaRepo->obtenerEstadisticas($fichaId, $fechaInicio, $fechaFin);
            $ficha = $this->fichaRepo->encontrarConRelaciones($fichaId);

            $datos = [
                'ficha' => [
                    'numero' => $ficha->ficha ?? 'N/A',
                    'programa' => $ficha->programaFormacion->nombre ?? 'N/A',
                    'jornada' => $ficha->jornadaFormacion->jornada ?? 'N/A',
                ],
                'periodo' => [
                    'inicio' => $fechaInicio,
                    'fin' => $fechaFin,
                ],
                'estadisticas' => $estadisticas,
                'asistencias' => $asistencias,
                'resumen_por_aprendiz' => $this->calcularResumenPorAprendiz($asistencias),
            ];

            switch ($formato) {
                case 'excel':
                    return $this->generarExcel($datos);
                case 'pdf':
                    return $this->generarPDF($datos);
                default:
                    return $datos;
            }
        } catch (\Exception $e) {
            Log::error('Error generando reporte de asistencia', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Genera reporte de aprendices por ficha
     *
     * @param int $fichaId
     * @param string $formato
     * @return mixed
     */
    public function generarReporteAprendices(int $fichaId, string $formato = 'array')
    {
        try {
            $aprendices = $this->aprendizRepo->obtenerPorFicha($fichaId);
            $ficha = $this->fichaRepo->encontrarConRelaciones($fichaId);

            $datos = [
                'ficha' => [
                    'numero' => $ficha->ficha ?? 'N/A',
                    'programa' => $ficha->programaFormacion->nombre ?? 'N/A',
                ],
                'total_aprendices' => $aprendices->count(),
                'aprendices_activos' => $aprendices->where('estado', true)->count(),
                'aprendices' => $aprendices->map(function ($aprendiz) {
                    return [
                        'documento' => $aprendiz->persona->numero_documento,
                        'nombre' => $aprendiz->persona->nombre_completo,
                        'email' => $aprendiz->persona->email,
                        'estado' => $aprendiz->estado ? 'Activo' : 'Inactivo',
                    ];
                }),
            ];

            switch ($formato) {
                case 'excel':
                    return $this->generarExcelAprendices($datos);
                case 'pdf':
                    return $this->generarPDFAprendices($datos);
                default:
                    return $datos;
            }
        } catch (\Exception $e) {
            Log::error('Error generando reporte de aprendices', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Genera reporte consolidado de asistencias del mes
     *
     * @param int $mes
     * @param int $anio
     * @return array
     */
    public function generarReporteConsolidadoMes(int $mes, int $anio): array
    {
        try {
            $fechaInicio = Carbon::create($anio, $mes, 1)->startOfMonth()->format('Y-m-d');
            $fechaFin = Carbon::create($anio, $mes, 1)->endOfMonth()->format('Y-m-d');

            $fichas = $this->fichaRepo->obtenerVigentes();
            $reportes = [];

            foreach ($fichas as $ficha) {
                $estadisticas = $this->asistenciaRepo->obtenerEstadisticas($ficha->id, $fechaInicio, $fechaFin);
                
                $reportes[] = [
                    'ficha' => $ficha->ficha,
                    'programa' => $ficha->programaFormacion->nombre ?? 'N/A',
                    'estadisticas' => $estadisticas,
                ];
            }

            return [
                'periodo' => [
                    'mes' => $mes,
                    'anio' => $anio,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                ],
                'total_fichas' => count($reportes),
                'fichas' => $reportes,
            ];
        } catch (\Exception $e) {
            Log::error('Error generando reporte consolidado', [
                'mes' => $mes,
                'anio' => $anio,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Calcula resumen por aprendiz
     *
     * @param Collection $asistencias
     * @return array
     */
    protected function calcularResumenPorAprendiz($asistencias): array
    {
        $resumen = [];

        foreach ($asistencias as $asistencia) {
            $documento = $asistencia->numero_identificacion;
            
            if (!isset($resumen[$documento])) {
                $resumen[$documento] = [
                    'nombres' => $asistencia->nombres,
                    'apellidos' => $asistencia->apellidos,
                    'total_asistencias' => 0,
                    'llegadas_tarde' => 0,
                    'salidas_anticipadas' => 0,
                ];
            }

            $resumen[$documento]['total_asistencias']++;

            if ($asistencia->novedad_entrada === 'Tarde' || $asistencia->novedad_entrada === 'Muy tarde') {
                $resumen[$documento]['llegadas_tarde']++;
            }

            if ($asistencia->novedad_salida === 'Anticipada') {
                $resumen[$documento]['salidas_anticipadas']++;
            }
        }

        return $resumen;
    }

    /**
     * Genera archivo Excel
     *
     * @param array $datos
     * @return string Path del archivo
     */
    protected function generarExcel(array $datos): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'REPORTE DE ASISTENCIAS');
        $sheet->setCellValue('A2', 'Ficha: ' . $datos['ficha']['numero']);
        $sheet->setCellValue('A3', 'Programa: ' . $datos['ficha']['programa']);
        $sheet->setCellValue('A4', 'Periodo: ' . $datos['periodo']['inicio'] . ' - ' . $datos['periodo']['fin']);

        // Datos de asistencias
        $row = 6;
        $sheet->setCellValue('A' . $row, 'Documento');
        $sheet->setCellValue('B' . $row, 'Nombre');
        $sheet->setCellValue('C' . $row, 'Hora Ingreso');
        $sheet->setCellValue('D' . $row, 'Hora Salida');
        $sheet->setCellValue('E' . $row, 'Novedad Entrada');
        $sheet->setCellValue('F' . $row, 'Novedad Salida');

        $row++;
        foreach ($datos['asistencias'] as $asistencia) {
            $sheet->setCellValue('A' . $row, $asistencia->numero_identificacion);
            $sheet->setCellValue('B' . $row, $asistencia->nombres . ' ' . $asistencia->apellidos);
            $sheet->setCellValue('C' . $row, $asistencia->hora_ingreso);
            $sheet->setCellValue('D' . $row, $asistencia->hora_salida);
            $sheet->setCellValue('E' . $row, $asistencia->novedad_entrada);
            $sheet->setCellValue('F' . $row, $asistencia->novedad_salida);
            $row++;
        }

        $filename = 'reportes/asistencias_' . time() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $path = storage_path('app/public/' . $filename);
        $writer->save($path);

        return $filename;
    }

    /**
     * Genera archivo Excel de aprendices
     *
     * @param array $datos
     * @return string
     */
    protected function generarExcelAprendices(array $datos): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LISTADO DE APRENDICES');
        $sheet->setCellValue('A2', 'Ficha: ' . $datos['ficha']['numero']);

        $row = 4;
        $sheet->setCellValue('A' . $row, 'Documento');
        $sheet->setCellValue('B' . $row, 'Nombre');
        $sheet->setCellValue('C' . $row, 'Email');
        $sheet->setCellValue('D' . $row, 'Estado');

        $row++;
        foreach ($datos['aprendices'] as $aprendiz) {
            $sheet->setCellValue('A' . $row, $aprendiz['documento']);
            $sheet->setCellValue('B' . $row, $aprendiz['nombre']);
            $sheet->setCellValue('C' . $row, $aprendiz['email']);
            $sheet->setCellValue('D' . $row, $aprendiz['estado']);
            $row++;
        }

        $filename = 'reportes/aprendices_' . time() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $path = storage_path('app/public/' . $filename);
        $writer->save($path);

        return $filename;
    }

    /**
     * Genera PDF (placeholder)
     *
     * @param array $datos
     * @return string
     */
    protected function generarPDF(array $datos): string
    {
        // Implementar con DomPDF o similar
        throw new \Exception('Generación de PDF no implementada aún');
    }

    /**
     * Genera PDF de aprendices (placeholder)
     *
     * @param array $datos
     * @return string
     */
    protected function generarPDFAprendices(array $datos): string
    {
        // Implementar con DomPDF o similar
        throw new \Exception('Generación de PDF no implementada aún');
    }
}


<?php

namespace App\Http\Controllers;

use App\Services\ReporteService;
use App\Services\EstadisticasService;
use App\Jobs\GenerarReporteAsistenciaJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReporteController extends Controller
{
    protected ReporteService $reporteService;
    protected EstadisticasService $estadisticasService;

    public function __construct(
        ReporteService $reporteService,
        EstadisticasService $estadisticasService
    ) {
        $this->middleware('auth');
        $this->reporteService = $reporteService;
        $this->estadisticasService = $estadisticasService;
    }

    /**
     * Vista principal de reportes
     */
    public function index()
    {
        try {
            $estadisticas = $this->estadisticasService->obtenerDashboardGeneral();

            return view('reportes.index', compact('estadisticas'));
        } catch (\Exception $e) {
            Log::error('Error cargando vista de reportes: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar reportes.');
        }
    }

    /**
     * Genera reporte de asistencia
     */
    public function generarAsistencia(Request $request)
    {
        $request->validate([
            'ficha_id' => 'required|integer|exists:ficha_caracterizacions,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'formato' => 'required|in:excel,pdf,array',
        ]);

        try {
            // Si es un reporte grande, procesar en background
            if ($request->formato !== 'array') {
                GenerarReporteAsistenciaJob::dispatch(
                    $request->ficha_id,
                    $request->fecha_inicio,
                    $request->fecha_fin,
                    $request->formato,
                    auth()->user()
                );

                return response()->json([
                    'success' => true,
                    'message' => 'El reporte se está generando. Recibirás una notificación cuando esté listo.',
                ]);
            }

            // Reporte rápido (array)
            $datos = $this->reporteService->generarReporteAsistencia(
                $request->ficha_id,
                $request->fecha_inicio,
                $request->fecha_fin,
                'array'
            );

            return response()->json([
                'success' => true,
                'data' => $datos,
            ]);
        } catch (\Exception $e) {
            Log::error('Error generando reporte: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al generar el reporte.',
            ], 500);
        }
    }

    /**
     * Genera reporte de aprendices
     */
    public function generarAprendices(Request $request)
    {
        $request->validate([
            'ficha_id' => 'required|integer|exists:ficha_caracterizacions,id',
            'formato' => 'required|in:excel,pdf,array',
        ]);

        try {
            $datos = $this->reporteService->generarReporteAprendices(
                $request->ficha_id,
                $request->formato
            );

            if ($request->formato === 'array') {
                return response()->json([
                    'success' => true,
                    'data' => $datos,
                ]);
            }

            return response()->download(storage_path("app/public/{$datos}"));
        } catch (\Exception $e) {
            Log::error('Error generando reporte de aprendices: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al generar el reporte.',
            ], 500);
        }
    }

    /**
     * Obtiene estadísticas generales
     */
    public function estadisticas()
    {
        try {
            $general = $this->estadisticasService->obtenerDashboardGeneral();
            $tendencias = $this->estadisticasService->obtenerTendenciasMensuales(6);
            $topFichas = $this->estadisticasService->obtenerTopFichasAsistencia(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'general' => $general,
                    'tendencias' => $tendencias,
                    'top_fichas' => $topFichas,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas.',
            ], 500);
        }
    }
}


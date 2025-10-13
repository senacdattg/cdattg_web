<?php

namespace App\Http\Controllers;

use App\Services\EstadisticasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EstadisticasController extends Controller
{
    protected EstadisticasService $estadisticasService;

    public function __construct(EstadisticasService $estadisticasService)
    {
        $this->middleware('auth');
        $this->estadisticasService = $estadisticasService;
    }

    /**
     * Dashboard general
     */
    public function dashboard()
    {
        try {
            $general = $this->estadisticasService->obtenerDashboardGeneral();
            $tendencias = $this->estadisticasService->obtenerTendenciasMensuales(6);
            $topFichas = $this->estadisticasService->obtenerTopFichasAsistencia(10);

            return view('estadisticas.dashboard', compact('general', 'tendencias', 'topFichas'));
        } catch (\Exception $e) {
            Log::error('Error cargando dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar estadísticas.');
        }
    }

    /**
     * API de estadísticas generales
     */
    public function api()
    {
        try {
            $general = $this->estadisticasService->obtenerDashboardGeneral();

            return response()->json([
                'success' => true,
                'data' => $general,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en API estadísticas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
            ], 500);
        }
    }

    /**
     * Estadísticas de asistencia por ficha
     */
    public function asistenciaFicha(Request $request, $fichaId)
    {
        try {
            $estadisticas = $this->estadisticasService->obtenerEstadisticasAsistencia(
                $fichaId,
                $request->input('fecha_inicio'),
                $request->input('fecha_fin')
            );

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de ficha: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
            ], 500);
        }
    }

    /**
     * Tendencias mensuales
     */
    public function tendencias(Request $request)
    {
        try {
            $meses = $request->input('meses', 6);
            $tendencias = $this->estadisticasService->obtenerTendenciasMensuales($meses);

            return response()->json([
                'success' => true,
                'data' => $tendencias,
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo tendencias: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener tendencias',
            ], 500);
        }
    }

    /**
     * Top fichas con mejor asistencia
     */
    public function topFichas(Request $request)
    {
        try {
            $limite = $request->input('limite', 10);
            $topFichas = $this->estadisticasService->obtenerTopFichasAsistencia($limite);

            return response()->json([
                'success' => true,
                'data' => $topFichas,
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo top fichas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener top fichas',
            ], 500);
        }
    }
}


<?php

namespace App\Http\Controllers;

use App\Events\VisitanteActualizado;
use App\Events\EstadisticasVisitantesActualizadas;
use App\Services\EstadisticasService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WebSocketVisitantesController extends Controller
{
    protected EstadisticasService $estadisticasService;

    public function __construct(EstadisticasService $estadisticasService)
    {
        $this->estadisticasService = $estadisticasService;
    }

    /**
     * Obtener estadísticas actuales de visitantes
     */
    public function obtenerEstadisticas(): JsonResponse
    {
        try {
            $estadisticas = $this->estadisticasService->obtenerDashboardGeneral();
            
            return response()->json([
                'success' => true,
                'data' => $estadisticas,
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de visitantes: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
            ], 500);
        }
    }

    /**
     * Registrar entrada de visitante y emitir por WebSocket
     */
    public function registrarEntrada(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'persona_id' => 'required|integer',
                'nombre' => 'required|string',
                'documento' => 'required|string',
                'rol' => 'required|string',
                'ficha' => 'nullable|string',
                'ambiente' => 'nullable|string',
            ]);

            $visitante = [
                'id' => $validated['persona_id'],
                'nombre' => $validated['nombre'],
                'documento' => $validated['documento'],
                'rol' => $validated['rol'],
                'ficha' => $validated['ficha'] ?? null,
                'ambiente' => $validated['ambiente'] ?? null,
                'hora_entrada' => now()->toISOString(),
            ];

            // Emitir evento de entrada por WebSocket
            broadcast(new VisitanteActualizado($visitante, 'entrada'));

            // Actualizar y emitir estadísticas
            $estadisticas = $this->estadisticasService->obtenerDashboardGeneral();
            broadcast(new EstadisticasVisitantesActualizadas($estadisticas));

            return response()->json([
                'success' => true,
                'message' => 'Entrada registrada correctamente',
                'visitante' => $visitante,
            ]);
        } catch (\Exception $e) {
            Log::error('Error registrando entrada de visitante: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar entrada',
            ], 500);
        }
    }

    /**
     * Registrar salida de visitante y emitir por WebSocket
     */
    public function registrarSalida(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'persona_id' => 'required|integer',
                'nombre' => 'required|string',
                'documento' => 'required|string',
                'rol' => 'required|string',
            ]);

            $visitante = [
                'id' => $validated['persona_id'],
                'nombre' => $validated['nombre'],
                'documento' => $validated['documento'],
                'rol' => $validated['rol'],
                'hora_salida' => now()->toISOString(),
            ];

            // Emitir evento de salida por WebSocket
            broadcast(new VisitanteActualizado($visitante, 'salida'));

            // Actualizar y emitir estadísticas
            $estadisticas = $this->estadisticasService->obtenerDashboardGeneral();
            broadcast(new EstadisticasVisitantesActualizadas($estadisticas));

            return response()->json([
                'success' => true,
                'message' => 'Salida registrada correctamente',
                'visitante' => $visitante,
            ]);
        } catch (\Exception $e) {
            Log::error('Error registrando salida de visitante: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar salida',
            ], 500);
        }
    }

    /**
     * Obtener lista de visitantes actuales
     */
    public function obtenerVisitantesActuales(): JsonResponse
    {
        try {
            // Aquí puedes implementar la lógica para obtener visitantes actuales
            // Por ahora devolvemos un array vacío
            $visitantesActuales = [];

            return response()->json([
                'success' => true,
                'data' => $visitantesActuales,
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo visitantes actuales: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener visitantes actuales',
            ], 500);
        }
    }
}

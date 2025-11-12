<?php

namespace App\Http\Controllers;

use App\Services\RegistroPresenciaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class RegistroPresenciaController extends Controller
{
    protected RegistroPresenciaService $registroPresenciaService;

    public function __construct(RegistroPresenciaService $registroPresenciaService)
    {
        $this->registroPresenciaService = $registroPresenciaService;
    }

    /**
     * Registrar entrada de una persona
     */
    public function registrarEntrada(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'persona_id' => 'required|integer|exists:personas,id',
                'ambiente_id' => 'nullable|integer|exists:ambientes,id',
                'ficha_caracterizacion_id' => 'nullable|integer|exists:fichas_caracterizacion,id',
                'observaciones' => 'nullable|string|max:1000',
            ]);

            $registro = $this->registroPresenciaService->registrarEntrada(
                $validated['persona_id'],
                $validated['ambiente_id'] ?? null,
                $validated['ficha_caracterizacion_id'] ?? null,
                $validated['observaciones'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Entrada registrada correctamente',
                'data' => $registro->load(['persona', 'ambiente', 'fichaCaracterizacion']),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error registrando entrada: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Registrar salida de una persona
     */
    public function registrarSalida(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'persona_id' => 'required|integer|exists:personas,id',
                'observaciones' => 'nullable|string|max:1000',
            ]);

            $this->registroPresenciaService->registrarSalida(
                $validated['persona_id'],
                $validated['observaciones'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Salida registrada correctamente',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error registrando salida: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Obtener estadísticas de personas dentro del edificio
     */
    public function estadisticasPersonasDentro(): JsonResponse
    {
        try {
            $estadisticas = $this->registroPresenciaService->obtenerEstadisticasPersonasDentro();

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de personas dentro HOY
     */
    public function estadisticasPersonasDentroHoy(): JsonResponse
    {
        try {
            $estadisticas = $this->registroPresenciaService->obtenerEstadisticasPersonasDentroHoy();

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de hoy: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
            ], 500);
        }
    }

    /**
     * Obtener lista de personas dentro actualmente
     */
    public function personasDentro(Request $request): JsonResponse
    {
        try {
            $tipoPersona = $request->query('tipo_persona'); // Opcional: filtrar por tipo

            $personas = $this->registroPresenciaService->obtenerPersonasDentro($tipoPersona);

            return response()->json([
                'success' => true,
                'data' => $personas,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error obteniendo personas dentro: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener personas dentro',
            ], 500);
        }
    }

    /**
     * Obtener estadísticas por fecha
     */
    public function estadisticasPorFecha(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fecha' => 'required|date',
            ]);

            $estadisticas = $this->registroPresenciaService->obtenerEstadisticasPorFecha($validated['fecha']);

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas por fecha: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
            ], 500);
        }
    }
}


<?php

namespace App\Http\Controllers;

use App\Events\EntradaSalidaRegistrada;
use App\Models\EntradaSalida;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebSocketEntradaSalidaController extends Controller
{
    /**
     * Registrar entrada de una persona con WebSocket
     */
    public function registrarEntrada(Request $request)
    {
        try {
            $request->validate([
                'persona_id' => 'required|exists:personas,id',
                'rol' => 'required|string',
                'sede_id' => 'required|exists:sedes,id',
            ]);

            // Obtener información de la persona
            $persona = Persona::findOrFail($request->persona_id);

            // Preparar datos para el evento (solo lo necesario para la otra app)
            $eventData = [
                'persona_id' => $persona->id,
                'rol' => $request->rol,
                'sede_id' => $request->sede_id,
                'tipo' => 'entrada',
                'timestamp' => now()->toISOString(),
            ];

            // Disparar evento de WebSocket
            event(new EntradaSalidaRegistrada($eventData));

            Log::info('Entrada registrada exitosamente con WebSocket', [
                'persona_id' => $persona->id,
                'tipo' => 'entrada',
                'rol' => $request->rol,
                'sede_id' => $request->sede_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Entrada registrada exitosamente',
                'data' => $eventData
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al registrar entrada con WebSocket', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar entrada: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar salida de una persona con WebSocket
     */
    public function registrarSalida(Request $request)
    {
        try {
            $request->validate([
                'persona_id' => 'required|exists:personas,id',
                'rol' => 'required|string',
                'sede_id' => 'required|exists:sedes,id',
            ]);

            // Obtener información de la persona
            $persona = Persona::findOrFail($request->persona_id);

            // Preparar datos para el evento (solo lo necesario para la otra app)
            $eventData = [
                'persona_id' => $persona->id,
                'rol' => $request->rol,
                'sede_id' => $request->sede_id,
                'tipo' => 'salida',
                'timestamp' => now()->toISOString(),
            ];

            // Disparar evento de WebSocket
            event(new EntradaSalidaRegistrada($eventData));

            Log::info('Salida registrada exitosamente con WebSocket', [
                'persona_id' => $persona->id,
                'tipo' => 'salida',
                'rol' => $request->rol,
                'sede_id' => $request->sede_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Salida registrada exitosamente',
                'data' => $eventData
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al registrar salida con WebSocket', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar salida: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de entradas y salidas (placeholder para compatibilidad)
     */
    public function obtenerEstadisticas()
    {
        try {
            // Este endpoint es solo para compatibilidad, no usa el modelo EntradaSalida
            $estadisticas = [
                'total_entradas_hoy' => 0,
                'total_salidas_hoy' => 0,
                'personas_actualmente_dentro' => 0,
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas de entradas/salidas', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener lista de personas actualmente dentro (placeholder para compatibilidad)
     */
    public function obtenerPersonasDentro()
    {
        try {
            // Este endpoint es solo para compatibilidad, no usa el modelo EntradaSalida
            $personasDentro = [];

            return response()->json([
                'success' => true,
                'data' => $personasDentro
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener personas dentro', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener personas dentro: ' . $e->getMessage()
            ], 500);
        }
    }
}

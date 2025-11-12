<?php

namespace App\Http\Controllers;

use App\Events\EntradaSalidaRegistrada;
use App\Models\EntradaSalida;
use App\Models\Persona;
use App\Models\PersonaIngresoSalida;
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

            // Guardar en base de datos
            $ingresoSalida = PersonaIngresoSalida::create([
                'persona_id' => $persona->id,
                'sede_id' => $request->sede_id,
                'tipo_persona' => $request->rol,
                'fecha_entrada' => now()->toDateString(),
                'hora_entrada' => now()->toTimeString(),
                'timestamp_entrada' => now(),
                'user_create_id' => auth()->id() ?? 1,
            ]);

            // Preparar datos para el evento (solo lo necesario para la otra app)
            $eventData = [
                'persona_id' => $persona->id,
                'numero_documento' => $persona->numero_documento,
                'rol' => $request->rol,
                'sede_id' => $request->sede_id,
                'tipo' => 'entrada',
                'timestamp' => now()->toISOString(),
            ];

            // Disparar evento de WebSocket
            event(new EntradaSalidaRegistrada($eventData));

            Log::info('Entrada registrada exitosamente con WebSocket', [
                'persona_id' => $persona->id,
                'numero_documento' => $persona->numero_documento,
                'tipo' => 'entrada',
                'rol' => $request->rol,
                'sede_id' => $request->sede_id,
                'ingreso_salida_id' => $ingresoSalida->id
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

            // Buscar el registro de entrada sin salida para esta persona
            $ingresoSalida = PersonaIngresoSalida::where('persona_id', $persona->id)
                ->whereNull('timestamp_salida')
                ->latest('timestamp_entrada')
                ->first();

            if ($ingresoSalida) {
                // Actualizar el registro existente con la salida
                $ingresoSalida->update([
                    'fecha_salida' => now()->toDateString(),
                    'hora_salida' => now()->toTimeString(),
                    'timestamp_salida' => now(),
                    'user_edit_id' => auth()->id() ?? 1,
                ]);
            } else {
                // Crear un nuevo registro si no se encontró entrada previa
                $ingresoSalida = PersonaIngresoSalida::create([
                    'persona_id' => $persona->id,
                    'sede_id' => $request->sede_id,
                    'tipo_persona' => $request->rol,
                    'fecha_salida' => now()->toDateString(),
                    'hora_salida' => now()->toTimeString(),
                    'timestamp_salida' => now(),
                    'user_create_id' => auth()->id() ?? 1,
                ]);
            }

            // Preparar datos para el evento (solo lo necesario para la otra app)
            $eventData = [
                'persona_id' => $persona->id,
                'numero_documento' => $persona->numero_documento,
                'rol' => $request->rol,
                'sede_id' => $request->sede_id,
                'tipo' => 'salida',
                'timestamp' => now()->toISOString(),
            ];

            // Disparar evento de WebSocket
            event(new EntradaSalidaRegistrada($eventData));

            Log::info('Salida registrada exitosamente con WebSocket', [
                'persona_id' => $persona->id,
                'numero_documento' => $persona->numero_documento,
                'tipo' => 'salida',
                'rol' => $request->rol,
                'sede_id' => $request->sede_id,
                'ingreso_salida_id' => $ingresoSalida->id
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
     * Obtener estadísticas de entradas y salidas
     */
    public function obtenerEstadisticas()
    {
        try {
            // Obtener estadísticas reales de la base de datos
            $hoy = now()->toDateString();
            
            $total_entradas_hoy = PersonaIngresoSalida::whereDate('fecha_entrada', $hoy)->count();
            $total_salidas_hoy = PersonaIngresoSalida::whereDate('fecha_salida', $hoy)->count();
            $personas_actualmente_dentro = PersonaIngresoSalida::whereNull('timestamp_salida')->count();

            $estadisticas = [
                'total_entradas_hoy' => $total_entradas_hoy,
                'total_salidas_hoy' => $total_salidas_hoy,
                'personas_actualmente_dentro' => $personas_actualmente_dentro,
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
     * Obtener lista de personas actualmente dentro
     */
    public function obtenerPersonasDentro()
    {
        try {
            // Obtener personas que están dentro actualmente (sin salida registrada)
            $personasDentro = PersonaIngresoSalida::with(['persona', 'sede'])
                ->whereNull('timestamp_salida')
                ->get()
                ->map(function ($registro) {
                    return [
                        'persona_id' => $registro->persona_id,
                        'numero_documento' => $registro->persona->numero_documento ?? 'N/A',
                        'nombre_completo' => $registro->persona->nombre_completo ?? 'N/A',
                        'sede' => $registro->sede->sede ?? 'N/A',
                        'tipo_persona' => $registro->tipo_persona,
                        'fecha_entrada' => $registro->fecha_entrada,
                        'hora_entrada' => $registro->hora_entrada,
                        'timestamp_entrada' => $registro->timestamp_entrada,
                    ];
                });

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

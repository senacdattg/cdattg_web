<?php

namespace App\Http\Controllers;

use App\Events\EntradaSalidaRegistrada;
use App\Models\Persona;
use App\Models\PersonaIngresoSalida;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class WebSocketEntradaSalidaController extends Controller
{
    /**
     * Registrar entrada de una persona con WebSocket
     */
    public function registrarEntrada(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'persona_id' => 'required|exists:personas,id',
                'rol' => 'required|string',
                'sede_id' => 'required|exists:sedes,id',
            ]);

            // Obtener información de la persona
            $persona = Persona::findOrFail($validated['persona_id']);

            $timestamp = now();
            $userId = Auth::id() ?? 1;

            // Guardar en base de datos
            $ingresoSalida = DB::transaction(function () use ($persona, $validated, $timestamp, $userId) {
                return PersonaIngresoSalida::create([
                    'persona_id' => $persona->id,
                    'sede_id' => $validated['sede_id'],
                    'tipo_persona' => $validated['rol'],
                    'fecha_entrada' => $timestamp->toDateString(),
                    'hora_entrada' => $timestamp->format('H:i:s'),
                    'timestamp_entrada' => $timestamp,
                    'user_create_id' => $userId,
                ]);
            });

            // Preparar datos para el evento (solo lo necesario para la otra app)
            $eventData = $this->buildEventPayload($ingresoSalida, $persona, 'entrada');

            // Disparar evento de WebSocket
            event(new EntradaSalidaRegistrada($eventData));

            Log::info('Entrada registrada exitosamente con WebSocket', [
                'persona_id' => $persona->id,
                'numero_documento' => $persona->numero_documento,
                'tipo' => 'entrada',
                'rol' => $validated['rol'],
                'sede_id' => $validated['sede_id'],
                'ingreso_salida_id' => $ingresoSalida->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Entrada registrada exitosamente',
                'data' => $eventData
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos para registrar la entrada',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'La persona especificada no existe',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error al registrar entrada con WebSocket', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar entrada',
            ], 500);
        }
    }

    /**
     * Registrar salida de una persona con WebSocket
     */
    public function registrarSalida(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'persona_id' => 'required|exists:personas,id',
                'rol' => 'required|string',
                'sede_id' => 'required|exists:sedes,id',
            ]);

            // Obtener información de la persona
            $persona = Persona::findOrFail($validated['persona_id']);
            $timestamp = now();
            $userId = Auth::id() ?? 1;

            // Buscar el registro de entrada sin salida para esta persona
            $ingresoSalida = DB::transaction(function () use ($persona, $validated, $timestamp, $userId) {
                $registro = PersonaIngresoSalida::where('persona_id', $persona->id)
                    ->whereNull('timestamp_salida')
                    ->latest('timestamp_entrada')
                    ->first();

                if ($registro) {
                    $registro->update([
                        'fecha_salida' => $timestamp->toDateString(),
                        'hora_salida' => $timestamp->format('H:i:s'),
                        'timestamp_salida' => $timestamp,
                        'user_edit_id' => $userId,
                    ]);

                    return $registro;
                }

                return PersonaIngresoSalida::create([
                    'persona_id' => $persona->id,
                    'sede_id' => $validated['sede_id'],
                    'tipo_persona' => $validated['rol'],
                    'fecha_salida' => $timestamp->toDateString(),
                    'hora_salida' => $timestamp->format('H:i:s'),
                    'timestamp_salida' => $timestamp,
                    'user_create_id' => $userId,
                ]);
            });

            // Preparar datos para el evento (solo lo necesario para la otra app)
            $eventData = $this->buildEventPayload($ingresoSalida, $persona, 'salida');

            // Disparar evento de WebSocket
            event(new EntradaSalidaRegistrada($eventData));

            Log::info('Salida registrada exitosamente con WebSocket', [
                'persona_id' => $persona->id,
                'numero_documento' => $persona->numero_documento,
                'tipo' => 'salida',
                'rol' => $validated['rol'],
                'sede_id' => $validated['sede_id'],
                'ingreso_salida_id' => $ingresoSalida->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Salida registrada exitosamente',
                'data' => $eventData
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos para registrar la salida',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'La persona especificada no existe',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error al registrar salida con WebSocket', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar salida',
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de entradas y salidas
     */
    public function obtenerEstadisticas(): JsonResponse
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

        } catch (\Throwable $e) {
            Log::error('Error al obtener estadísticas de entradas/salidas', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
            ], 500);
        }
    }

    /**
     * Obtener lista de personas actualmente dentro
     */
    public function obtenerPersonasDentro(): JsonResponse
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
                        'fecha_entrada' => optional($registro->fecha_entrada)->toDateString(),
                        'hora_entrada' => $registro->hora_entrada,
                        'timestamp_entrada' => optional($registro->timestamp_entrada)->toISOString(),
                    ];
                })
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $personasDentro
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener personas dentro', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener personas dentro',
            ], 500);
        }
    }

    /**
     * Construye la carga útil que será emitida por WebSocket.
     */
    private function buildEventPayload(PersonaIngresoSalida $registro, Persona $persona, string $tipo): array
    {
        $registro->loadMissing('sede');

        $timestamp = $tipo === 'entrada'
            ? $registro->timestamp_entrada
            : $registro->timestamp_salida;

        return [
            'persona_id' => $persona->id,
            'numero_documento' => $persona->numero_documento,
            'nombre_completo' => $persona->nombre_completo,
            'rol' => $registro->tipo_persona,
            'sede_id' => $registro->sede_id,
            'sede_nombre' => optional($registro->sede)->sede,
            'tipo' => $tipo,
            'timestamp' => optional($timestamp)->toISOString(),
        ];
    }
}
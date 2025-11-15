<?php

namespace App\Http\Controllers;

use App\Events\VisitanteActualizado;
use App\Models\PersonaIngresoSalida;
use App\Services\PersonaIngresoSalidaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PersonaIngresoSalidaController extends Controller
{
    private const ERROR_ESTADISTICAS = 'Error al obtener estadísticas';

    protected PersonaIngresoSalidaService $personaIngresoSalidaService;

    public function __construct(PersonaIngresoSalidaService $personaIngresoSalidaService)
    {
        $this->personaIngresoSalidaService = $personaIngresoSalidaService;
    }

    /**
     * Registrar entrada de una persona
     */
    public function registrarEntrada(Request $request): JsonResponse
    {
        try {
            $rules = [
                'persona_id' => 'required|integer|exists:personas,id',
                'sede_id' => 'required|integer|exists:sedes,id',
                'rol_id' => 'nullable|integer|exists:roles,id',
                'observaciones' => 'nullable|string|max:1000',
            ];

            if (Schema::hasColumn('persona_ingreso_salida', 'ambiente_id')) {
                $rules['ambiente_id'] = 'nullable|integer|exists:ambientes,id';
            }

            if (Schema::hasColumn('persona_ingreso_salida', 'ficha_caracterizacion_id')) {
                $rules['ficha_caracterizacion_id'] = 'nullable|integer|exists:fichas_caracterizacion,id';
            }

            $validated = $request->validate($rules);

            /** @var PersonaIngresoSalida $registro */
            $registro = $this->personaIngresoSalidaService->registrarEntrada(
                $validated['persona_id'],
                $validated['sede_id'],
                $validated['rol_id'] ?? null,
                $validated['ambiente_id'] ?? null,
                $validated['ficha_caracterizacion_id'] ?? null,
                $validated['observaciones'] ?? null
            );

            $relaciones = ['persona', 'sede'];
            if (Schema::hasColumn('persona_ingreso_salida', 'ambiente_id')) {
                $relaciones[] = 'ambiente';
            }
            if (Schema::hasColumn('persona_ingreso_salida', 'ficha_caracterizacion_id')) {
                $relaciones[] = 'fichaCaracterizacion';
            }
            $relaciones[] = 'persona.user.roles';

            $registro->loadMissing($relaciones);

            broadcast(new VisitanteActualizado(
                $this->buildVisitantePayload($registro, 'entrada'),
                'entrada'
            ));

            return response()->json([
                'success' => true,
                'message' => 'Entrada registrada correctamente',
                'data' => $registro,
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
                'sede_id' => 'required|integer|exists:sedes,id',
                'observaciones' => 'nullable|string|max:1000',
            ]);

            /** @var PersonaIngresoSalida $registro */
            $registro = $this->personaIngresoSalidaService->registrarSalida(
                $validated['persona_id'],
                $validated['sede_id'],
                $validated['observaciones'] ?? null
            );

            $relaciones = ['persona', 'sede'];
            if (Schema::hasColumn('persona_ingreso_salida', 'ambiente_id')) {
                $relaciones[] = 'ambiente';
            }
            if (Schema::hasColumn('persona_ingreso_salida', 'ficha_caracterizacion_id')) {
                $relaciones[] = 'fichaCaracterizacion';
            }
            $relaciones[] = 'persona.user.roles';

            $registro->loadMissing($relaciones);

            broadcast(new VisitanteActualizado(
                $this->buildVisitantePayload($registro, 'salida'),
                'salida'
            ));

            return response()->json([
                'success' => true,
                'message' => 'Salida registrada correctamente',
                'data' => $registro,
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
    public function estadisticasPersonasDentro(Request $request): JsonResponse
    {
        try {
            $sedeId = $request->query('sede_id'); // Opcional: filtrar por sede
            
            $estadisticas = $this->personaIngresoSalidaService->obtenerEstadisticasPersonasDentro(
                $sedeId ? (int)$sedeId : null
            );

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => self::ERROR_ESTADISTICAS,
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de personas dentro HOY
     */
    public function estadisticasPersonasDentroHoy(Request $request): JsonResponse
    {
        try {
            $sedeId = $request->query('sede_id'); // Opcional: filtrar por sede
            
            $estadisticas = $this->personaIngresoSalidaService->obtenerEstadisticasPersonasDentroHoy(
                $sedeId ? (int)$sedeId : null
            );

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de hoy: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => self::ERROR_ESTADISTICAS,
            ], 500);
        }
    }

    /**
     * Obtener lista de personas dentro actualmente
     */
    public function personasDentro(Request $request): JsonResponse
    {
        try {
            $sedeId = $request->query('sede_id'); // Opcional: filtrar por sede
            $tipoPersona = $request->query('tipo_persona'); // Opcional: filtrar por tipo

            $personas = $this->personaIngresoSalidaService->obtenerPersonasDentro(
                $sedeId ? (int)$sedeId : null,
                $tipoPersona
            );

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
     * Obtener registros del día con nombre, apellidos, sede, entrada y salida.
     */
    public function registrosDiarios(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fecha' => 'nullable|date',
                'sede_id' => 'nullable|integer|exists:sedes,id',
            ]);

            $fecha = $validated['fecha'] ?? today()->toDateString();

            $query = PersonaIngresoSalida::with(['persona', 'sede'])
                ->whereDate('fecha_entrada', $fecha)
                ->orderBy('timestamp_entrada');

            if (!empty($validated['sede_id'])) {
                $query->where('sede_id', $validated['sede_id']);
            }

            $registros = $query->get()->map(function (PersonaIngresoSalida $registro) {
                $persona = $registro->persona;

                return [
                    'persona_id' => $registro->persona_id,
                    'nombre' => $persona ? trim($persona->primer_nombre . ' ' . ($persona->segundo_nombre ?? '')) : null,
                    'apellidos' => $persona ? trim($persona->primer_apellido . ' ' . ($persona->segundo_apellido ?? '')) : null,
                    'sede' => $registro->sede?->sede,
                    'hora_entrada' => optional($registro->timestamp_entrada)->format('H:i:s'),
                    'hora_salida' => optional($registro->timestamp_salida)->format('H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'fecha' => $fecha,
                    'sede_id' => $validated['sede_id'] ?? null,
                    'registros' => $registros,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo registros diarios: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener registros diarios',
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
                'sede_id' => 'nullable|integer|exists:sedes,id',
            ]);

            $estadisticas = $this->personaIngresoSalidaService->obtenerEstadisticasPorFecha(
                $validated['fecha'],
                $validated['sede_id'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas por fecha: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => self::ERROR_ESTADISTICAS,
            ], 500);
        }
    }

    /**
     * Obtener estadísticas por sede
     */
    public function estadisticasPorSede(Request $request, int $sedeId): JsonResponse
    {
        try {
            $estadisticas = $this->personaIngresoSalidaService->obtenerEstadisticasPorSede($sedeId);

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas por sede: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => self::ERROR_ESTADISTICAS,
            ], 500);
        }
    }

    /**
     * @param PersonaIngresoSalida $registro
     * @param string $tipo
     * @return array<string,mixed>
     */
    private function buildVisitantePayload(PersonaIngresoSalida $registro, string $tipo): array
    {
        $persona = $registro->persona;
        $nombre = $persona
            ? trim(sprintf(
                '%s %s %s %s',
                $persona->primer_nombre,
                $persona->segundo_nombre,
                $persona->primer_apellido,
                $persona->segundo_apellido
            ))
            : null;

        $rolNombre = $persona?->user?->roles?->first()?->name;

        return [
            'id' => $persona?->id ?? $registro->persona_id,
            'nombre' => $nombre ?: 'SIN NOMBRE',
            'documento' => $persona->numero_documento ?? null,
            'rol' => $rolNombre ?? 'SIN ROL',
            'sede' => $registro->sede?->sede,
            'ambiente' => null,
            'hora_entrada' => optional($registro->timestamp_entrada)->toIso8601String(),
            'hora_salida' => optional($registro->timestamp_salida)->toIso8601String(),
            'tipo' => $tipo,
        ];
    }
}


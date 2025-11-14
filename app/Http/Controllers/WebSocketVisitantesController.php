<?php

namespace App\Http\Controllers;

use App\Events\VisitanteActualizado;
use App\Events\EstadisticasVisitantesActualizadas;
use App\Models\Persona;
use App\Models\PersonaIngresoSalida;
use App\Services\EstadisticasService;
use App\Services\PersonaIngresoSalidaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class WebSocketVisitantesController extends Controller
{
    protected EstadisticasService $estadisticasService;
    protected PersonaIngresoSalidaService $personaIngresoSalidaService;

    /** @var array<int, string|null> */
    private array $roleNameCache = [];

    /** @var array<string, bool> */
    private array $columnExistsCache = [];

    public function __construct(
        EstadisticasService $estadisticasService,
        PersonaIngresoSalidaService $personaIngresoSalidaService
    ) {
        $this->estadisticasService = $estadisticasService;
        $this->personaIngresoSalidaService = $personaIngresoSalidaService;
    }

    /**
     * Obtener estadísticas actuales de visitantes
     */
    public function obtenerEstadisticas(): JsonResponse
    {
        try {
            $estadisticas = $this->estadisticasService->obtenerDashboardGeneral(false);
            
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
            $rules = [
                'persona_id' => 'required|integer|exists:personas,id',
                'sede_id' => 'required|integer|exists:sedes,id',
                'rol_id' => 'nullable|integer|exists:roles,id',
                'observaciones' => 'nullable|string|max:1000',
            ];

            if ($this->columnaDisponible('ambiente_id')) {
                $rules['ambiente_id'] = 'nullable|integer|exists:ambientes,id';
            }

            if ($this->columnaDisponible('ficha_caracterizacion_id')) {
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

            $registro->load($this->obtenerRelacionesRegistro());

            $visitante = $this->transformarRegistroEnVisitante($registro);

            broadcast(new VisitanteActualizado($visitante, 'entrada'));

            $estadisticas = $this->estadisticasService->refrescarDashboardGeneral();
            broadcast(new EstadisticasVisitantesActualizadas($estadisticas));

            return response()->json([
                'success' => true,
                'message' => 'Entrada registrada correctamente',
                'data' => [
                    'registro' => $registro,
                    'visitante' => $visitante,
                ],
            ], 201);
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

            $registro->load($this->obtenerRelacionesRegistro());

            $visitante = $this->transformarRegistroEnVisitante($registro, true);

            broadcast(new VisitanteActualizado($visitante, 'salida'));

            $estadisticas = $this->estadisticasService->refrescarDashboardGeneral();
            broadcast(new EstadisticasVisitantesActualizadas($estadisticas));

            return response()->json([
                'success' => true,
                'message' => 'Salida registrada correctamente',
                'data' => [
                    'registro' => $registro,
                    'visitante' => $visitante,
                ],
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
    public function obtenerVisitantesActuales(Request $request): JsonResponse
    {
        try {
            $sedeId = $request->query('sede_id');
            $personasDentro = $this->personaIngresoSalidaService->obtenerPersonasDentro(
                $sedeId ? (int) $sedeId : null
            );

            $visitantesActuales = $personasDentro
                ->load($this->obtenerRelacionesRegistro())
                ->map(fn (PersonaIngresoSalida $registro) => $this->transformarRegistroEnVisitante($registro))
                ->values();

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

    /**
     * Entradas por hora (para gráficos).
     */
    public function obtenerEntradasPorHora(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fecha' => 'nullable|date',
                'sede_id' => 'nullable|integer|exists:sedes,id',
            ]);

            $fecha = $validated['fecha'] ?? today()->toDateString();

            $query = PersonaIngresoSalida::whereDate('fecha_entrada', $fecha);

            if (!empty($validated['sede_id'])) {
                $query->where('sede_id', $validated['sede_id']);
            }

            $entradas = $query
                ->select([
                    DB::raw('DATE_FORMAT(timestamp_entrada, "%H:00") as hora'),
                    DB::raw('COUNT(*) as total'),
                ])
                ->groupBy('hora')
                ->orderBy('hora')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'fecha' => $fecha,
                    'sede_id' => $validated['sede_id'] ?? null,
                    'series' => $entradas,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo entradas por hora: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener entradas por hora',
            ], 500);
        }
    }

    /**
     * Relaciones a cargar para un registro de ingreso/salida.
     *
     * @return array<int, string>
     */
    private function obtenerRelacionesRegistro(): array
    {
        $relaciones = [
            'persona.user.roles',
            'sede',
        ];

        if ($this->columnaDisponible('ambiente_id')) {
            $relaciones[] = 'ambiente';
        }

        if ($this->columnaDisponible('ficha_caracterizacion_id')) {
            $relaciones[] = 'fichaCaracterizacion';
        }

        return $relaciones;
    }

    /**
     * Convierte un registro en la estructura esperada para broadcasting/respuestas.
     */
    private function transformarRegistroEnVisitante(PersonaIngresoSalida $registro, bool $esSalida = false): array
    {
        $persona = $registro->persona;
        $rolNombre = $this->resolverNombreRol((int) $registro->rol_id, $persona);
        $ambienteNombre = null;
        $fichaCodigo = null;
        $observaciones = null;

        if ($this->columnaDisponible('ambiente_id') && $registro->relationLoaded('ambiente') && $registro->ambiente) {
            $ambienteNombre = $registro->ambiente->title;
        }

        if (
            $this->columnaDisponible('ficha_caracterizacion_id') &&
            $registro->relationLoaded('fichaCaracterizacion') &&
            $registro->fichaCaracterizacion
        ) {
            $fichaCodigo = $registro->fichaCaracterizacion->ficha;
        }

        if ($this->columnaDisponible('observaciones')) {
            $observaciones = $registro->observaciones;
        }

        return [
            'registro_id' => $registro->id,
            'persona_id' => $registro->persona_id,
            'sede_id' => $registro->sede_id,
            'sede' => $registro->relationLoaded('sede') && $registro->sede ? $registro->sede->nombre : null,
            'nombre' => $persona ? $persona->nombre_completo : null,
            'documento' => $persona ? $persona->numero_documento : null,
            'rol' => $rolNombre,
            'rol_id' => $registro->rol_id,
            'ambiente' => $ambienteNombre,
            'ficha' => $fichaCodigo,
            'hora_entrada' => optional($registro->timestamp_entrada)->toISOString(),
            'hora_salida' => $esSalida ? optional($registro->timestamp_salida)->toISOString() : null,
            'observaciones' => $observaciones,
        ];
    }

    /**
     * Resuelve y cachea el nombre de un rol para minimizar consultas.
     */
    private function resolverNombreRol(int $rolId, ?Persona $persona = null): ?string
    {
        if ($persona && $persona->relationLoaded('user') && $persona->user) {
            $rol = $persona->user->roles->first();

            if ($rol) {
                $nombre = strtoupper($rol->name);
                $this->roleNameCache[$rolId] = $nombre;

                return $nombre;
            }
        }

        if (array_key_exists($rolId, $this->roleNameCache)) {
            return $this->roleNameCache[$rolId];
        }

        $rol = Role::find($rolId);
        $this->roleNameCache[$rolId] = $rol ? strtoupper($rol->name) : null;

        return $this->roleNameCache[$rolId];
    }

    private function columnaDisponible(string $columna): bool
    {
        if (array_key_exists($columna, $this->columnExistsCache)) {
            return $this->columnExistsCache[$columna];
        }

        return $this->columnExistsCache[$columna] = Schema::hasColumn('persona_ingreso_salida', $columna);
    }
}

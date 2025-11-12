<?php

namespace App\Services;

use App\Models\PersonaIngresoSalida;
use App\Models\Persona;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PersonaIngresoSalidaService
{
    /**
     * Determina el tipo de persona basándose en sus relaciones
     */
    public function determinarTipoPersona(int $personaId): string
    {
        $persona = Persona::with(['instructor', 'aprendiz', 'user.roles'])->find($personaId);

        if (!$persona) {
            throw new \Exception("Persona no encontrada con ID: {$personaId}");
        }

        // Verificar si es instructor
        if ($persona->instructor) {
            return 'instructor';
        }

        // Verificar si es aprendiz
        if ($persona->aprendiz) {
            return 'aprendiz';
        }

        // Verificar roles del usuario
        if ($persona->user) {
            $roles = $persona->user->roles->pluck('name')->map(fn($r) => strtoupper($r));

            if ($roles->contains('SUPER ADMINISTRADOR')) {
                return 'super_administrador';
            }

            if ($roles->contains('ADMINISTRADOR')) {
                return 'administrativo';
            }

            if ($roles->contains('VISITANTE')) {
                return 'visitante';
            }

            if ($roles->contains('ASPIRANTE')) {
                return 'aspirante';
            }
        }

        // Por defecto, si no se puede determinar, se considera visitante
        return 'visitante';
    }

    /**
     * Registra la entrada de una persona
     */
    public function registrarEntrada(
        int $personaId,
        int $sedeId,
        ?int $ambienteId = null,
        ?int $fichaCaracterizacionId = null,
        ?string $observaciones = null,
        ?int $userId = null
    ): PersonaIngresoSalida {
        return DB::transaction(function () use ($personaId, $sedeId, $ambienteId, $fichaCaracterizacionId, $observaciones, $userId) {
            // Verificar si ya tiene un registro abierto (entrada sin salida) en esta sede
            $registroAbierto = PersonaIngresoSalida::where('persona_id', $personaId)
                ->where('sede_id', $sedeId)
                ->whereNull('timestamp_salida')
                ->whereDate('fecha_entrada', Carbon::today())
                ->first();

            if ($registroAbierto) {
                throw new \Exception('Ya existe un registro de entrada sin salida para hoy en esta sede.');
            }

            // Determinar tipo de persona
            $tipoPersona = $this->determinarTipoPersona($personaId);

            $now = Carbon::now();

            // Crear registro de entrada
            $registro = PersonaIngresoSalida::create([
                'persona_id' => $personaId,
                'sede_id' => $sedeId,
                'tipo_persona' => $tipoPersona,
                'fecha_entrada' => $now->format('Y-m-d'),
                'hora_entrada' => $now->format('H:i:s'),
                'timestamp_entrada' => $now,
                'ambiente_id' => $ambienteId,
                'ficha_caracterizacion_id' => $fichaCaracterizacionId,
                'observaciones' => $observaciones,
                'user_create_id' => $userId ?? auth()->id(),
            ]);

            Log::info('Entrada registrada', [
                'registro_id' => $registro->id,
                'persona_id' => $personaId,
                'sede_id' => $sedeId,
                'tipo_persona' => $tipoPersona,
                'timestamp' => $now->toISOString(),
            ]);

            return $registro;
        });
    }

    /**
     * Registra la salida de una persona
     */
    public function registrarSalida(
        int $personaId,
        int $sedeId,
        ?string $observaciones = null,
        ?int $userId = null
    ): bool {
        return DB::transaction(function () use ($personaId, $sedeId, $observaciones, $userId) {
            // Buscar registro abierto (entrada sin salida) en esta sede
            $registro = PersonaIngresoSalida::where('persona_id', $personaId)
                ->where('sede_id', $sedeId)
                ->whereNull('timestamp_salida')
                ->whereDate('fecha_entrada', Carbon::today())
                ->latest('timestamp_entrada')
                ->first();

            if (!$registro) {
                throw new \Exception('No se encontró un registro de entrada sin salida para hoy en esta sede.');
            }

            $now = Carbon::now();

            // Actualizar registro con salida
            $registro->update([
                'fecha_salida' => $now->format('Y-m-d'),
                'hora_salida' => $now->format('H:i:s'),
                'timestamp_salida' => $now,
                'observaciones' => $registro->observaciones 
                    ? ($registro->observaciones . "\nSalida: " . ($observaciones ?? ''))
                    : ($observaciones ?? null),
                'user_edit_id' => $userId ?? auth()->id(),
            ]);

            Log::info('Salida registrada', [
                'registro_id' => $registro->id,
                'persona_id' => $personaId,
                'sede_id' => $sedeId,
                'timestamp' => $now->toISOString(),
            ]);

            return true;
        });
    }

    /**
     * Obtiene estadísticas de personas dentro del edificio (todas las sedes)
     */
    public function obtenerEstadisticasPersonasDentro(?int $sedeId = null): array
    {
        $query = PersonaIngresoSalida::dentro();

        if ($sedeId) {
            $query->porSede($sedeId);
        }

        // Contar personas dentro por tipo
        $personasDentro = $query
            ->select('tipo_persona', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo_persona')
            ->pluck('total', 'tipo_persona')
            ->toArray();

        return [
            'instructores' => $personasDentro['instructor'] ?? 0,
            'aprendices' => $personasDentro['aprendiz'] ?? 0,
            'visitantes' => $personasDentro['visitante'] ?? 0,
            'administrativos' => $personasDentro['administrativo'] ?? 0,
            'aspirantes' => $personasDentro['aspirante'] ?? 0,
            'super_administradores' => $personasDentro['super_administrador'] ?? 0,
            'total' => array_sum($personasDentro),
        ];
    }

    /**
     * Obtiene estadísticas de personas dentro del edificio HOY
     */
    public function obtenerEstadisticasPersonasDentroHoy(?int $sedeId = null): array
    {
        $query = PersonaIngresoSalida::dentro()->hoy();

        if ($sedeId) {
            $query->porSede($sedeId);
        }

        $personasDentro = $query
            ->select('tipo_persona', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo_persona')
            ->pluck('total', 'tipo_persona')
            ->toArray();

        return [
            'instructores' => $personasDentro['instructor'] ?? 0,
            'aprendices' => $personasDentro['aprendiz'] ?? 0,
            'visitantes' => $personasDentro['visitante'] ?? 0,
            'administrativos' => $personasDentro['administrativo'] ?? 0,
            'aspirantes' => $personasDentro['aspirante'] ?? 0,
            'super_administradores' => $personasDentro['super_administrador'] ?? 0,
            'total' => array_sum($personasDentro),
        ];
    }

    /**
     * Obtiene lista de personas dentro actualmente
     */
    public function obtenerPersonasDentro(?int $sedeId = null, ?string $tipoPersona = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = PersonaIngresoSalida::dentro()
            ->with(['persona', 'sede', 'ambiente', 'fichaCaracterizacion'])
            ->orderBy('timestamp_entrada', 'desc');

        if ($sedeId) {
            $query->porSede($sedeId);
        }

        if ($tipoPersona) {
            $query->porTipo($tipoPersona);
        }

        return $query->get();
    }

    /**
     * Obtiene estadísticas detalladas por fecha
     */
    public function obtenerEstadisticasPorFecha(string $fecha, ?int $sedeId = null): array
    {
        $queryEntradas = PersonaIngresoSalida::porFecha($fecha);
        $querySalidas = PersonaIngresoSalida::porFecha($fecha)->whereNotNull('timestamp_salida');
        $queryDentro = PersonaIngresoSalida::porFecha($fecha)->dentro();

        if ($sedeId) {
            $queryEntradas->porSede($sedeId);
            $querySalidas->porSede($sedeId);
            $queryDentro->porSede($sedeId);
        }

        $entradas = $queryEntradas
            ->select('tipo_persona', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo_persona')
            ->pluck('total', 'tipo_persona')
            ->toArray();

        $salidas = $querySalidas
            ->select('tipo_persona', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo_persona')
            ->pluck('total', 'tipo_persona')
            ->toArray();

        $dentro = $queryDentro
            ->select('tipo_persona', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo_persona')
            ->pluck('total', 'tipo_persona')
            ->toArray();

        return [
            'fecha' => $fecha,
            'sede_id' => $sedeId,
            'entradas' => [
                'instructores' => $entradas['instructor'] ?? 0,
                'aprendices' => $entradas['aprendiz'] ?? 0,
                'visitantes' => $entradas['visitante'] ?? 0,
                'administrativos' => $entradas['administrativo'] ?? 0,
                'aspirantes' => $entradas['aspirante'] ?? 0,
                'super_administradores' => $entradas['super_administrador'] ?? 0,
                'total' => array_sum($entradas),
            ],
            'salidas' => [
                'instructores' => $salidas['instructor'] ?? 0,
                'aprendices' => $salidas['aprendiz'] ?? 0,
                'visitantes' => $salidas['visitante'] ?? 0,
                'administrativos' => $salidas['administrativo'] ?? 0,
                'aspirantes' => $salidas['aspirante'] ?? 0,
                'super_administradores' => $salidas['super_administrador'] ?? 0,
                'total' => array_sum($salidas),
            ],
            'dentro' => [
                'instructores' => $dentro['instructor'] ?? 0,
                'aprendices' => $dentro['aprendiz'] ?? 0,
                'visitantes' => $dentro['visitante'] ?? 0,
                'administrativos' => $dentro['administrativo'] ?? 0,
                'aspirantes' => $dentro['aspirante'] ?? 0,
                'super_administradores' => $dentro['super_administrador'] ?? 0,
                'total' => array_sum($dentro),
            ],
        ];
    }

    /**
     * Obtiene estadísticas por sede
     */
    public function obtenerEstadisticasPorSede(int $sedeId): array
    {
        $estadisticasHoy = $this->obtenerEstadisticasPersonasDentroHoy($sedeId);
        $estadisticasGenerales = $this->obtenerEstadisticasPersonasDentro($sedeId);

        return [
            'sede_id' => $sedeId,
            'hoy' => $estadisticasHoy,
            'total_dentro' => $estadisticasGenerales,
        ];
    }
}


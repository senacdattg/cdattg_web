<?php

namespace App\Services;

use App\Models\PersonaIngresoSalida;
use App\Models\Persona;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class PersonaIngresoSalidaService
{
    private const ROLE_CATEGORY_MAP = [
        'SUPER ADMINISTRADOR' => 'super_administradores',
        'ADMINISTRADOR' => 'administrativos',
        'INSTRUCTOR' => 'instructores',
        'VISITANTE' => 'visitantes',
        'APRENDIZ' => 'aprendices',
        'ASPIRANTE' => 'aspirantes',
    ];

    /**
     * Cache local para resolver roles por nombre.
     *
     * @var array<string, int|null>
     */
    private array $roleNameCache = [];

    /**
     * Cache local para resolver nombres de rol por id.
     *
     * @var array<int, string|null>
     */
    private array $roleIdNameCache = [];

    /**
     * Determina el tipo de persona basándose en sus relaciones
     */
    public function determinarTipoPersona(int $personaId): int
    {
        $persona = Persona::with(['instructor', 'aprendiz', 'user.roles'])->find($personaId);

        if (!$persona) {
            throw new \Exception("Persona no encontrada con ID: {$personaId}");
        }

        if ($persona->user) {
            $rol = $persona->user->roles->first();
            if ($rol) {
                $this->cacheRoleName((int) $rol->id, $rol->name);
                return (int) $rol->id;
            }
        }

        if ($persona->instructor) {
            $rolId = $this->resolveRoleId('INSTRUCTOR');
            if ($rolId !== null) {
                return $rolId;
            }
        }

        if ($persona->aprendiz) {
            $rolId = $this->resolveRoleId('APRENDIZ');
            if ($rolId !== null) {
                return $rolId;
            }
        }

        $rolId = $this->resolveRoleId('VISITANTE');

        if ($rolId === null) {
            throw new \Exception('No se pudo resolver un rol válido para la persona.');
        }

        return $rolId;
    }

    /**
     * Registra la entrada de una persona
     */
    public function registrarEntrada(
        int $personaId,
        int $sedeId,
        ?int $rolId = null,
        ?int $ambienteId = null,
        ?int $fichaCaracterizacionId = null,
        ?string $observaciones = null,
        ?int $userId = null
    ): PersonaIngresoSalida {
        return DB::transaction(function () use ($personaId, $sedeId, $rolId, $ambienteId, $fichaCaracterizacionId, $observaciones, $userId) {
            // Verificar si ya tiene un registro abierto (entrada sin salida) en esta sede
            $registroAbierto = PersonaIngresoSalida::where('persona_id', $personaId)
                ->where('sede_id', $sedeId)
                ->whereNull('timestamp_salida')
                ->whereDate('fecha_entrada', Carbon::today())
                ->first();

            if ($registroAbierto) {
                throw new \Exception('Ya existe un registro de entrada sin salida para hoy en esta sede.');
            }

            // Determinar rol calculado para referencia
            $rolCalculado = $this->determinarTipoPersona($personaId);
            $rolAsignado = $rolId ?? $rolCalculado;

            $now = Carbon::now();

            // Crear registro de entrada
            $data = [
                'persona_id' => $personaId,
                'sede_id' => $sedeId,
                'rol_id' => $rolAsignado,
                'fecha_entrada' => $now->format('Y-m-d'),
                'hora_entrada' => $now->format('H:i:s'),
                'timestamp_entrada' => $now,
                'user_create_id' => $userId ?? auth()->id(),
            ];

            $registro = PersonaIngresoSalida::create($data);

            $camposActualizados = false;

            if ($ambienteId !== null && Schema::hasColumn($registro->getTable(), 'ambiente_id')) {
                $registro->ambiente_id = $ambienteId;
                $camposActualizados = true;
            }

            if ($fichaCaracterizacionId !== null && Schema::hasColumn($registro->getTable(), 'ficha_caracterizacion_id')) {
                $registro->ficha_caracterizacion_id = $fichaCaracterizacionId;
                $camposActualizados = true;
            }

            if ($observaciones !== null && Schema::hasColumn($registro->getTable(), 'observaciones')) {
                $registro->observaciones = $observaciones;
                $camposActualizados = true;
            }

            if ($camposActualizados) {
                $registro->save();
            }

            Log::info('Entrada registrada', [
                'registro_id' => $registro->id,
                'sede_id' => $sedeId,
                'rol_id' => $rolAsignado,
                'rol_calculado' => $rolCalculado,
                'rol_nombre' => $this->getRoleNameById($rolAsignado),
                'rol_proporcionado' => $rolId,
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
    ): PersonaIngresoSalida {
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

            $registro->refresh();

            Log::info('Salida registrada', [
                'registro_id' => $registro->id,
                'persona_id' => $personaId,
                'sede_id' => $sedeId,
                'rol_id' => $registro->rol_id,
                'rol_nombre' => $this->getRoleNameById((int) $registro->rol_id),
                'timestamp' => $now->toISOString(),
            ]);

            return $registro;
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

        $conteos = $this->compileCountsByRole($query);

        return $this->mapCountsToCategories($conteos);
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

        $conteos = $this->compileCountsByRole($query);

        return $this->mapCountsToCategories($conteos);
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

        return [
            'fecha' => $fecha,
            'sede_id' => $sedeId,
            'entradas' => $this->mapCountsToCategories($this->compileCountsByRole($queryEntradas)),
            'salidas' => $this->mapCountsToCategories($this->compileCountsByRole($querySalidas)),
            'dentro' => $this->mapCountsToCategories($this->compileCountsByRole($queryDentro)),
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

    /**
     * Devuelve la estructura base de categorías.
     */
    private function baseCategories(): array
    {
        return [
            'instructores' => 0,
            'aprendices' => 0,
            'visitantes' => 0,
            'administrativos' => 0,
            'aspirantes' => 0,
            'super_administradores' => 0,
            'total' => 0,
        ];
    }

    /**
     * Convierte los conteos agrupados por rol en categorías de salida.
     *
     * @param array<int, int> $conteos
     */
    private function mapCountsToCategories(array $conteos): array
    {
        $resultado = $this->baseCategories();

        if (empty($conteos)) {
            return $resultado;
        }

        foreach ($conteos as $rolId => $total) {
            $nombreRol = $this->getRoleNameById((int) $rolId);

            if (!$nombreRol) {
                $resultado['visitantes'] += $total;
                $resultado['total'] += $total;
                continue;
            }

            $categoria = self::ROLE_CATEGORY_MAP[$nombreRol] ?? 'visitantes';
            $resultado[$categoria] += $total;
            $resultado['total'] += $total;
        }

        return $resultado;
    }

    /**
     * Obtiene conteos agrupados por rol para la consulta dada.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @return array<int, int>
     */
    private function compileCountsByRole($query): array
    {
        return $query
            ->select('rol_id', DB::raw('COUNT(*) as total'))
            ->groupBy('rol_id')
            ->pluck('total', 'rol_id')
            ->map(fn ($total) => (int) $total)
            ->toArray();
    }

    /**
     * Resuelve el id de un rol a partir de su nombre (mayúsculas).
     */
    private function resolveRoleId(string $roleName): ?int
    {
        $normalized = strtoupper($roleName);

        if (array_key_exists($normalized, $this->roleNameCache)) {
            return $this->roleNameCache[$normalized];
        }

        $role = Role::whereRaw('UPPER(name) = ?', [$normalized])->first(['id', 'name']);

        if (!$role) {
            $this->roleNameCache[$normalized] = null;
            return null;
        }

        $this->cacheRoleName((int) $role->id, $role->name);

        return $this->roleNameCache[$normalized];
    }

    /**
     * Obtiene el nombre del rol (en mayúsculas) por id.
     */
    private function getRoleNameById(int $roleId): ?string
    {
        if (array_key_exists($roleId, $this->roleIdNameCache)) {
            return $this->roleIdNameCache[$roleId];
        }

        $role = Role::find($roleId, ['id', 'name']);

        if (!$role) {
            $this->roleIdNameCache[$roleId] = null;
            return null;
        }

        $this->cacheRoleName((int) $role->id, $role->name);

        return $this->roleIdNameCache[$roleId];
    }

    /**
     * Cachea la relación entre id y nombre de un rol.
     */
    private function cacheRoleName(int $roleId, string $roleName): void
    {
        $normalized = strtoupper($roleName);
        $this->roleIdNameCache[$roleId] = $normalized;
        $this->roleNameCache[$normalized] = $roleId;
    }
}


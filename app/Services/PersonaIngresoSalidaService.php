<?php

namespace App\Services;

use App\Exceptions\PersonaException;
use App\Models\PersonaIngresoSalida;
use App\Models\Persona;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class PersonaIngresoSalidaService
{
    private const COUNT_TOTAL = 'COUNT(*) as total';

    /**
     * Obtiene todos los tipos de persona disponibles dinámicamente desde la base de datos
     */
    public function obtenerTiposPersona(): array
    {
        return PersonaIngresoSalida::obtenerTiposPersonaDisponibles();
    }

    /**
     * Obtiene la configuración de visualización para cada tipo de persona
     * Genera la configuración dinámicamente basándose en los roles del sistema
     */
    public function obtenerConfiguracionTiposPersona(): array
    {
        $tiposPersona = $this->obtenerTiposPersona();
        $configuracion = [];

        // Obtener todos los roles del sistema
        $roles = Role::pluck('name')->mapWithKeys(function ($roleName) {
            return [strtoupper($roleName) => $roleName];
        })->toArray();

        // Mapeo dinámico de tipos de persona a roles del sistema
        // Busca el rol que coincida con el tipo (case-insensitive, sin espacios/guiones)
        $mapeoTipoARol = [];
        foreach ($tiposPersona as $tipo) {
            $tipoNormalizado = str_replace('_', ' ', strtoupper($tipo));
            foreach ($roles as $rolUpper => $rolName) {
                $rolNormalizado = str_replace(' ', '_', strtoupper($rolName));
                if ($tipoNormalizado === $rolNormalizado ||
                    str_contains($rolNormalizado, str_replace(' ', '_', $tipoNormalizado)) ||
                    str_contains($tipoNormalizado, str_replace('_', ' ', $rolNormalizado))) {
                    $mapeoTipoARol[$tipo] = $rolUpper;
                    break;
                }
            }
        }

        // Generar configuración para cada tipo de persona disponible
        foreach ($tiposPersona as $tipo) {
            $nombreRol = $mapeoTipoARol[$tipo] ?? null;
            $rolExiste = $nombreRol && isset($roles[$nombreRol]);

            // Obtener nombre del rol desde la base de datos si existe
            if ($rolExiste) {
                $nombre = $roles[$nombreRol];
                // Convertir a plural y capitalizar
                $nombre = $this->pluralizarYCapitalizar($nombre);
            } else {
                // Fallback: generar nombre desde el tipo
                $nombre = ucfirst(str_replace('_', ' ', $tipo));
                if (!str_ends_with(strtolower($nombre), 's')) {
                    $nombre .= 's';
                }
            }

            // Generar color dinámicamente basado en el tipo
            $color = $this->obtenerColorPorTipo($tipo);
            
            // Generar icono dinámicamente basado en el tipo
            $icono = $this->obtenerIconoPorTipo($tipo);

            $configuracion[$tipo] = [
                'nombre' => $nombre,
                'color' => $color,
                'icono' => $icono,
            ];

            // Estilo personalizado para super_administrador
            if ($tipo === 'super_administrador') {
                $configuracion[$tipo]['estilo_personalizado'] =
                    'background-color: #6f42c1 !important; color: white;';
            }
        }

        return $configuracion;
    }

    /**
     * Pluraliza y capitaliza un nombre de rol
     */
    private function pluralizarYCapitalizar(string $nombre): string
    {
        $nombre = strtolower($nombre);
        
        // Reglas de pluralización básicas
        $plurales = [
            'instructor' => 'Instructores',
            'aprendiz' => 'Aprendices',
            'administrador' => 'Administradores',
            'super administrador' => 'Super Administradores',
            'visitante' => 'Visitantes',
            'aspirante' => 'Aspirantes',
        ];

        if (isset($plurales[$nombre])) {
            return $plurales[$nombre];
        }

        // Fallback: capitalizar y agregar 's' si no termina en 's'
        $capitalizado = ucwords($nombre);
        if (!str_ends_with(strtolower($capitalizado), 's')) {
            $capitalizado .= 's';
        }

        return $capitalizado;
    }

    /**
     * Obtiene el color CSS dinámicamente basado en el tipo de persona
     */
    private function obtenerColorPorTipo(string $tipo): string
    {
        // Colores disponibles en AdminLTE
        $coloresDisponibles = [
            'bg-primary',
            'bg-secondary',
            'bg-success',
            'bg-danger',
            'bg-warning',
            'bg-info',
            'bg-dark',
        ];

        // Generar un índice determinístico basado en el tipo
        $hash = crc32($tipo);
        $indice = abs($hash) % count($coloresDisponibles);

        return $coloresDisponibles[$indice];
    }

    /**
     * Obtiene el icono FontAwesome dinámicamente basado en el tipo de persona
     */
    private function obtenerIconoPorTipo(string $tipo): string
    {
        // Mapeo inteligente basado en palabras clave en el nombre del tipo
        $tipoLower = strtolower($tipo);
        
        // Detectar palabras clave y asignar iconos apropiados
        if (str_contains($tipoLower, 'instructor') || str_contains($tipoLower, 'profesor')) {
            return 'fa-chalkboard-teacher';
        }
        if (str_contains($tipoLower, 'aprendiz') || str_contains($tipoLower, 'estudiante')) {
            return 'fa-user-graduate';
        }
        if (str_contains($tipoLower, 'administrador') || str_contains($tipoLower, 'admin')) {
            if (str_contains($tipoLower, 'super')) {
                return 'fa-user-shield';
            }
            return 'fa-user-tie';
        }
        if (str_contains($tipoLower, 'visitante') || str_contains($tipoLower, 'invitado')) {
            return 'fa-user-friends';
        }
        if (str_contains($tipoLower, 'aspirante') || str_contains($tipoLower, 'candidato')) {
            return 'fa-user-plus';
        }
        if (str_contains($tipoLower, 'vigilante') || str_contains($tipoLower, 'seguridad')) {
            return 'fa-shield-alt';
        }
        if (str_contains($tipoLower, 'coordinador')) {
            return 'fa-user-cog';
        }

        // Fallback: icono genérico
        return 'fa-user';
    }

    /**
     * Determina el tipo de persona basándose en sus relaciones
     */
    public function determinarTipoPersona(int $personaId): string
    {
        $persona = Persona::with(['instructor', 'aprendiz', 'user.roles'])->find($personaId);

        if (!$persona) {
            throw new PersonaException("Persona no encontrada con ID: {$personaId}");
        }

        $tipoPersona = 'visitante'; // Por defecto

        // Verificar si es instructor
        if ($persona->instructor) {
            $tipoPersona = 'instructor';
        } elseif ($persona->aprendiz) {
            // Verificar si es aprendiz
            $tipoPersona = 'aprendiz';
        } elseif ($persona->user) {
            // Verificar roles del usuario
            $roles = $persona->user->roles->pluck('name')->map(fn($r) => strtoupper($r));

            if ($roles->contains('SUPER ADMINISTRADOR')) {
                $tipoPersona = 'super_administrador';
            } elseif ($roles->contains('ADMINISTRADOR')) {
                $tipoPersona = 'administrativo';
            } elseif ($roles->contains('VISITANTE')) {
                $tipoPersona = 'visitante';
            } elseif ($roles->contains('ASPIRANTE')) {
                $tipoPersona = 'aspirante';
            }
        }

        return $tipoPersona;
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
        return DB::transaction(function () use (
            $personaId,
            $sedeId,
            $ambienteId,
            $fichaCaracterizacionId,
            $observaciones,
            $userId
        ) {
            // Verificar si ya tiene un registro abierto (entrada sin salida) en esta sede
            $registroAbierto = PersonaIngresoSalida::where('persona_id', $personaId)
                ->where('sede_id', $sedeId)
                ->whereNull('timestamp_salida')
                ->whereDate('fecha_entrada', Carbon::today())
                ->first();

            if ($registroAbierto) {
                throw new PersonaException('Ya existe un registro de entrada sin salida para hoy en esta sede.');
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
                'user_create_id' => $userId ?? Auth::id(),
            ]);

            Log::info('Entrada registrada', [
                'registro_id' => $registro->id,
                'persona_id' => $personaId,
                'sede_id' => $sedeId,
                'tipo_persona' => $tipoPersona,
                'timestamp' => $now->toDateTimeString(),
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
                throw new PersonaException('No se encontró un registro de entrada sin salida para hoy en esta sede.');
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
                'user_edit_id' => $userId ?? Auth::id(),
            ]);

            Log::info('Salida registrada', [
                'registro_id' => $registro->id,
                'persona_id' => $personaId,
                'sede_id' => $sedeId,
                'timestamp' => $now->toDateTimeString(),
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
            ->select('tipo_persona', DB::raw(self::COUNT_TOTAL))
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
        return $this->obtenerEstadisticasPersonasDentroPorFecha(Carbon::today()->format('Y-m-d'), $sedeId);
    }

    /**
     * Obtiene estadísticas de personas dentro del edificio en una fecha específica
     * Considera personas que entraron en o antes de esa fecha y no salieron o salieron después
     */
    public function obtenerEstadisticasPersonasDentroPorFecha(string $fecha, ?int $sedeId = null): array
    {
        $fechaCarbon = Carbon::parse($fecha)->endOfDay();

        // Personas que entraron en o antes de la fecha y:
        // - No han salido (timestamp_salida IS NULL), o
        // - Salieron después de la fecha seleccionada
        $query = PersonaIngresoSalida::where('timestamp_entrada', '<=', $fechaCarbon)
            ->where(function ($q) use ($fechaCarbon) {
                $q->whereNull('timestamp_salida')
                  ->orWhere('timestamp_salida', '>', $fechaCarbon);
            });

        if ($sedeId) {
            $query->where('sede_id', $sedeId);
        }

        $personasDentro = $query
            ->select('tipo_persona', DB::raw(self::COUNT_TOTAL))
            ->groupBy('tipo_persona')
            ->pluck('total', 'tipo_persona')
            ->toArray();

        // Inicializar todos los tipos con 0
        $estadisticas = [];
        foreach ($this->obtenerTiposPersona() as $tipo) {
            $estadisticas[$tipo] = $personasDentro[$tipo] ?? 0;
        }

        // Mantener compatibilidad con nombres antiguos (para no romper código existente)
        $estadisticas['instructores'] = $estadisticas['instructor'] ?? 0;
        $estadisticas['aprendices'] = $estadisticas['aprendiz'] ?? 0;
        $estadisticas['visitantes'] = $estadisticas['visitante'] ?? 0;
        $estadisticas['administrativos'] = $estadisticas['administrativo'] ?? 0;
        $estadisticas['aspirantes'] = $estadisticas['aspirante'] ?? 0;
        $estadisticas['super_administradores'] = $estadisticas['super_administrador'] ?? 0;
        $estadisticas['total'] = array_sum($personasDentro);

        return $estadisticas;
    }

    /**
     * Obtiene lista de personas dentro actualmente
     */
    public function obtenerPersonasDentro(
        ?int $sedeId = null,
        ?string $tipoPersona = null
    ): \Illuminate\Database\Eloquent\Collection
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
            ->select('tipo_persona', DB::raw(self::COUNT_TOTAL))
            ->groupBy('tipo_persona')
            ->pluck('total', 'tipo_persona')
            ->toArray();

        $salidas = $querySalidas
            ->select('tipo_persona', DB::raw(self::COUNT_TOTAL))
            ->groupBy('tipo_persona')
            ->pluck('total', 'tipo_persona')
            ->toArray();

        $dentro = $queryDentro
            ->select('tipo_persona', DB::raw(self::COUNT_TOTAL))
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

    /**
     * Obtiene lista de fechas que tienen registros (entradas o salidas)
     *
     * @return array Array de fechas en formato Y-m-d
     */
    public function obtenerFechasConRegistros(): array
    {
        return PersonaIngresoSalida::select(DB::raw('DISTINCT DATE(fecha_entrada) as fecha'))
            ->orderBy('fecha', 'desc')
            ->pluck('fecha')
            ->map(function ($fecha) {
                return is_string($fecha) ? $fecha : $fecha->format('Y-m-d');
            })
            ->toArray();
    }

    /**
     * Verifica si una fecha tiene registros
     *
     * @param string $fecha Fecha en formato Y-m-d
     * @return bool
     */
    public function fechaTieneRegistros(string $fecha): bool
    {
        return PersonaIngresoSalida::whereDate('fecha_entrada', $fecha)->exists();
    }

    /**
     * Obtiene la fecha anterior más cercana que tenga registros
     *
     * @param string $fecha Fecha en formato Y-m-d
     * @return string|null Fecha en formato Y-m-d o null si no hay fechas anteriores
     */
    public function obtenerFechaAnteriorConRegistros(string $fecha): ?string
    {
        $fechaCarbon = Carbon::parse($fecha);
        $fechaAnterior = PersonaIngresoSalida::select(DB::raw('DATE(fecha_entrada) as fecha'))
            ->whereDate('fecha_entrada', '<', $fechaCarbon)
            ->orderBy('fecha', 'desc')
            ->first();

        if (!$fechaAnterior) {
            return null;
        }

        $fechaResultado = $fechaAnterior->fecha;
        return is_string($fechaResultado) ? $fechaResultado : $fechaResultado->format('Y-m-d');
    }

    /**
     * Obtiene la fecha siguiente más cercana que tenga registros
     *
     * @param string $fecha Fecha en formato Y-m-d
     * @return string|null Fecha en formato Y-m-d o null si no hay fechas siguientes
     */
    public function obtenerFechaSiguienteConRegistros(string $fecha): ?string
    {
        $fechaCarbon = Carbon::parse($fecha);
        $hoy = Carbon::today();
        
        // No permitir fechas futuras
        if ($fechaCarbon->isSameDay($hoy)) {
            return null;
        }

        $fechaSiguiente = PersonaIngresoSalida::select(DB::raw('DATE(fecha_entrada) as fecha'))
            ->whereDate('fecha_entrada', '>', $fechaCarbon)
            ->whereDate('fecha_entrada', '<=', $hoy)
            ->orderBy('fecha', 'asc')
            ->first();

        if (!$fechaSiguiente) {
            return null;
        }

        $fechaResultado = $fechaSiguiente->fecha;
        return is_string($fechaResultado) ? $fechaResultado : $fechaResultado->format('Y-m-d');
    }

    /**
     * Obtiene eventos recientes de ingreso/salida para una fecha específica
     *
     * @param string $fecha Fecha en formato Y-m-d
     * @param int|null $sedeId ID de la sede, si es null incluye todas
     * @param int $limite Número máximo de eventos a retornar
     * @return array Array con eventos ordenados por fecha/hora más reciente
     */
    public function obtenerEventosRecientes(string $fecha, ?int $sedeId = null, int $limite = 20): array
    {
        $eventos = [];

        // Obtener entradas del día
        $queryEntradas = PersonaIngresoSalida::whereDate('fecha_entrada', $fecha)
            ->with(['persona', 'sede'])
            ->orderBy('timestamp_entrada', 'desc');

        if ($sedeId) {
            $queryEntradas->where('sede_id', $sedeId);
        }

        $entradas = $queryEntradas->get();

        foreach ($entradas as $entrada) {
            $eventos[] = [
                'tipo' => 'entrada',
                'persona_id' => $entrada->persona_id,
                'persona_nombre' => $entrada->persona ? $entrada->persona->nombre_completo : 'N/A',
                'timestamp' => $entrada->timestamp_entrada,
                'hora' => $entrada->hora_entrada,
                'sede_id' => $entrada->sede_id,
                'sede_nombre' => $entrada->sede ? $entrada->sede->sede : 'N/A',
            ];
        }

        // Obtener salidas del día
        $querySalidas = PersonaIngresoSalida::whereDate('fecha_salida', $fecha)
            ->whereNotNull('timestamp_salida')
            ->with(['persona', 'sede'])
            ->orderBy('timestamp_salida', 'desc');

        if ($sedeId) {
            $querySalidas->where('sede_id', $sedeId);
        }

        $salidas = $querySalidas->get();

        foreach ($salidas as $salida) {
            $eventos[] = [
                'tipo' => 'salida',
                'persona_id' => $salida->persona_id,
                'persona_nombre' => $salida->persona ? $salida->persona->nombre_completo : 'N/A',
                'timestamp' => $salida->timestamp_salida,
                'hora' => $salida->hora_salida,
                'sede_id' => $salida->sede_id,
                'sede_nombre' => $salida->sede ? $salida->sede->sede : 'N/A',
            ];
        }

        // Ordenar todos los eventos por timestamp descendente (más recientes primero)
        usort($eventos, function ($a, $b) {
            $timestampA = $a['timestamp'] instanceof Carbon
                ? $a['timestamp']->timestamp
                : Carbon::parse($a['timestamp'])->timestamp;
            $timestampB = $b['timestamp'] instanceof Carbon
                ? $b['timestamp']->timestamp
                : Carbon::parse($b['timestamp'])->timestamp;
            return $timestampB <=> $timestampA;
        });

        // Limitar resultados
        return array_slice($eventos, 0, $limite);
    }

    /**
     * Obtiene estadísticas de entradas y salidas por hora del día
     *
     * @param string|null $fecha Fecha en formato Y-m-d, si es null usa hoy
     * @param int|null $sedeId ID de la sede, si es null incluye todas
     * @return array Array con las horas del día (0-23) y cantidad de entradas/salidas
     */
    public function obtenerEstadisticasPorHora(?string $fecha = null, ?int $sedeId = null): array
    {
        $fecha = $fecha ?? Carbon::today()->format('Y-m-d');

        // Inicializar arrays para todas las horas del día (0-23)
        $entradasPorHora = array_fill(0, 24, 0);
        $salidasPorHora = array_fill(0, 24, 0);

        // Query para entradas
        $queryEntradas = PersonaIngresoSalida::whereDate('fecha_entrada', $fecha);
        if ($sedeId) {
            $queryEntradas->where('sede_id', $sedeId);
        }

        // Obtener entradas agrupadas por hora
        $entradas = $queryEntradas
            ->select(DB::raw('HOUR(timestamp_entrada) as hora'), DB::raw(self::COUNT_TOTAL))
            ->groupBy(DB::raw('HOUR(timestamp_entrada)'))
            ->get();

        foreach ($entradas as $entrada) {
            $hora = (int) $entrada->hora;
            if ($hora >= 0 && $hora <= 23) {
                $entradasPorHora[$hora] = (int) $entrada->total;
            }
        }

        // Query para salidas
        $querySalidas = PersonaIngresoSalida::whereDate('fecha_salida', $fecha)
            ->whereNotNull('timestamp_salida');
        if ($sedeId) {
            $querySalidas->where('sede_id', $sedeId);
        }

        // Obtener salidas agrupadas por hora
        $salidas = $querySalidas
            ->select(DB::raw('HOUR(timestamp_salida) as hora'), DB::raw(self::COUNT_TOTAL))
            ->groupBy(DB::raw('HOUR(timestamp_salida)'))
            ->get();

        foreach ($salidas as $salida) {
            $hora = (int) $salida->hora;
            if ($hora >= 0 && $hora <= 23) {
                $salidasPorHora[$hora] = (int) $salida->total;
            }
        }

        // Formatear etiquetas de horas
        $horasLabels = [];
        for ($i = 0; $i < 24; $i++) {
            $horasLabels[] = sprintf('%02d:00', $i);
        }

        return [
            'fecha' => $fecha,
            'sede_id' => $sedeId,
            'horas' => $horasLabels,
            'entradas' => $entradasPorHora,
            'salidas' => $salidasPorHora,
        ];
    }

    /**
     * Procesa todas las salidas pendientes y genera un reporte
     *
     * @return array Array con información del procesamiento y el reporte generado
     */
    public function procesarSalidasPendientes(): array
    {
        return DB::transaction(function () {
            // Obtener todas las entradas sin salida (pendientes)
            // Solo las del día anterior o anteriores
            $fechaAnterior = Carbon::yesterday();
            $entradasPendientes = PersonaIngresoSalida::whereNull('timestamp_salida')
                ->whereDate('fecha_entrada', '<=', $fechaAnterior->format('Y-m-d'))
                ->with(['persona', 'sede'])
                ->get();

            $salidasProcesadas = [];
            $ahora = Carbon::now();

            foreach ($entradasPendientes as $entrada) {
                // Registrar salida automática
                $entrada->update([
                    'fecha_salida' => $fechaAnterior->format('Y-m-d'),
                    'hora_salida' => '23:59:59',
                    'timestamp_salida' => $fechaAnterior->copy()->setTime(23, 59, 59),
                    'observaciones' => ($entrada->observaciones ?? '') .
                        "\n[SALIDA AUTOMÁTICA] Registrada automáticamente por el sistema a la medianoche.",
                    'user_edit_id' => 1, // Sistema
                ]);

                $salidasProcesadas[] = [
                    'persona_ingreso_salida_id' => $entrada->id,
                    'persona_id' => $entrada->persona_id,
                    'persona_nombre' => $entrada->persona
                        ? trim($entrada->persona->primer_nombre . ' ' .
                            $entrada->persona->segundo_nombre . ' ' .
                            $entrada->persona->primer_apellido . ' ' .
                            $entrada->persona->segundo_apellido)
                        : 'Persona no encontrada',
                    'sede_id' => $entrada->sede_id,
                    'sede_nombre' => $entrada->sede ? $entrada->sede->sede : 'Sede no encontrada',
                    'fecha_entrada' => $entrada->fecha_entrada->format('Y-m-d'),
                    'hora_entrada' => $entrada->hora_entrada,
                    'fecha_salida_automatica' => $fechaAnterior->format('Y-m-d'),
                    'hora_salida_automatica' => '23:59:59',
                    'tipo_persona' => $entrada->tipo_persona,
                ];

                Log::info('Salida automática registrada', [
                    'persona_ingreso_salida_id' => $entrada->id,
                    'persona_id' => $entrada->persona_id,
                    'sede_id' => $entrada->sede_id,
                ]);
            }

            // Crear reporte
            $reporte = \App\Models\ReporteSalidaAutomatica::create([
                'fecha_procesamiento' => $ahora->format('Y-m-d'),
                'hora_procesamiento' => $ahora->format('H:i:s'),
                'total_salidas_procesadas' => count($salidasProcesadas),
                'detalle' => json_encode($salidasProcesadas, JSON_UNESCAPED_UNICODE),
                'user_id' => 1, // Sistema
            ]);

            return [
                'total_procesadas' => count($salidasProcesadas),
                'reporte_id' => $reporte->id,
                'salidas' => $salidasProcesadas,
            ];
        });
    }
}


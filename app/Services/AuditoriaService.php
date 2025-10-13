<?php

namespace App\Services;

use App\Repositories\LoginRepository;
use App\Repositories\AsignacionInstructorLogRepository;
use App\Models\Login;
use Illuminate\Support\Facades\Log;

class AuditoriaService
{
    protected LoginRepository $loginRepo;
    protected AsignacionInstructorLogRepository $asignacionLogRepo;

    public function __construct(
        LoginRepository $loginRepo,
        AsignacionInstructorLogRepository $asignacionLogRepo
    ) {
        $this->loginRepo = $loginRepo;
        $this->asignacionLogRepo = $asignacionLogRepo;
    }

    /**
     * Registra intento de login
     *
     * @param array $datos
     * @return void
     */
    public function registrarLogin(array $datos): void
    {
        try {
            $this->loginRepo->registrar($datos);

            if (!$datos['exitoso']) {
                Log::warning('Intento de login fallido', [
                    'email' => $datos['email'],
                    'ip' => $datos['ip_address'] ?? request()->ip(),
                ]);

                // Verificar intentos fallidos recientes
                $intentosFallidos = $this->loginRepo->contarIntentosFallidosRecientes($datos['email']);
                
                if ($intentosFallidos >= 5) {
                    Log::alert('Múltiples intentos fallidos detectados', [
                        'email' => $datos['email'],
                        'intentos' => $intentosFallidos,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error registrando login', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Registra cambio en asignación de instructor
     *
     * @param int $instructorId
     * @param int $fichaId
     * @param string $accion
     * @param array $detalles
     * @return void
     */
    public function registrarCambioAsignacion(int $instructorId, int $fichaId, string $accion, array $detalles = []): void
    {
        try {
            $this->asignacionLogRepo->registrar([
                'instructor_id' => $instructorId,
                'ficha_caracterizacion_id' => $fichaId,
                'accion' => $accion,
                'detalles' => json_encode($detalles),
                'user_id' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error registrando cambio de asignación', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtiene reporte de auditoría
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @param string $tipo
     * @return array
     */
    public function obtenerReporteAuditoria(string $fechaInicio, string $fechaFin, string $tipo = 'todo'): array
    {
        $resultado = [];

        if ($tipo === 'todo' || $tipo === 'logins') {
            $resultado['logins'] = $this->loginRepo->obtenerEstadisticas($fechaInicio, $fechaFin);
        }

        if ($tipo === 'todo' || $tipo === 'asignaciones') {
            $resultado['asignaciones'] = $this->asignacionLogRepo->obtenerAuditoria($fechaInicio, $fechaFin);
        }

        return $resultado;
    }

    /**
     * Detecta actividades sospechosas
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public function detectarActividadesSospechosas(string $fechaInicio, string $fechaFin): array
    {
        $sospechosas = [];

        // Detectar múltiples intentos fallidos
        $loginsFallidos = Login::where('exitoso', false)
            ->whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->get()
            ->groupBy('email');

        foreach ($loginsFallidos as $email => $intentos) {
            if ($intentos->count() >= 5) {
                $sospechosas[] = [
                    'tipo' => 'intentos_fallidos',
                    'email' => $email,
                    'intentos' => $intentos->count(),
                    'ultimo_intento' => $intentos->first()->fecha_hora,
                ];
            }
        }

        return $sospechosas;
    }
}


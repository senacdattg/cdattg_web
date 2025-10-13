<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\AspiranteComplementario;
use App\Models\Persona;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log;

class ValidarSofiaJob implements ShouldQueue
{
    use Queueable;

    protected $complementarioId;
    protected $userId;
    protected $progressId;

    /**
     * Create a new job instance.
     */
    public function __construct($complementarioId, $userId = null, $progressId = null)
    {
        $this->complementarioId = $complementarioId;
        $this->userId = $userId;
        $this->progressId = $progressId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Iniciando validación SenaSofiaPlus para programa: {$this->complementarioId}");

        // Obtener registro de progreso si existe
        $progress = null;
        if ($this->progressId) {
            $progress = \App\Models\SofiaValidationProgress::find($this->progressId);
            if ($progress) {
                $progress->markAsStarted();
            }
        }

        // Obtener aspirantes que necesitan validación
        $aspirantes = AspiranteComplementario::with('persona')
            ->where('complementario_id', $this->complementarioId)
            ->whereHas('persona', function($query) {
                $query->whereIn('estado_sofia', [0, 2]);
            })
            ->get();

        if ($aspirantes->isEmpty()) {
            Log::info('No hay aspirantes que necesiten validación.');
            if ($progress) {
                $progress->markAsCompleted();
            }
            return;
        }

        Log::info("Validando {$aspirantes->count()} aspirantes...");

        $exitosos = 0;
        $errores = 0;
        $errores_detalle = [];

        foreach ($aspirantes as $aspirante) {
            try {
                $resultado = $this->validarAspirante($aspirante->persona->numero_documento);

                // Actualizar estado basado en resultado
                $nuevoEstado = $this->determinarEstadoSofia($resultado);
                $aspirante->persona->update(['estado_sofia' => $nuevoEstado]);

                Log::info("Cédula {$aspirante->persona->numero_documento}: {$resultado} -> Estado: {$nuevoEstado}");

                if ($nuevoEstado === 1) {
                    $exitosos++;
                }

                // Actualizar progreso
                if ($progress) {
                    $progress->incrementProcessed($nuevoEstado === 1);
                }

            } catch (\Exception $e) {
                $errorMsg = "Error con cédula {$aspirante->persona->numero_documento}: {$e->getMessage()}";
                Log::error($errorMsg);
                $errores++;
                $errores_detalle[] = $errorMsg;

                // Actualizar progreso con error
                if ($progress) {
                    $progress->incrementProcessed(false);
                }
            }

            // Delay para evitar rate limiting
            sleep(2);
        }

        // Marcar como completado
        if ($progress) {
            if ($errores > 0) {
                $progress->markAsFailed($errores_detalle);
            } else {
                $progress->markAsCompleted();
            }
        }

        Log::info("Validación completada - Exitosos: {$exitosos}, Errores: {$errores}");
    }

    private function validarAspirante($cedula)
    {
        // Ejecutar script de Node.js
        $scriptPath = base_path('resources/js/sofia-validator.js');

        $process = new Process(['node', $scriptPath, $cedula]);
        $process->setTimeout(30000); // 30 segundos timeout
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return trim($process->getOutput());
    }

    private function determinarEstadoSofia($resultado)
    {
        $resultadoLower = strtolower($resultado);

        // PRIMERO: Verificar si requiere cambio de documento
        if (str_contains($resultadoLower, 'requiere_cambio') ||
            str_contains($resultadoLower, 'actualizar tu documento') ||
            str_contains($resultadoLower, 'cambiar tu documento')) {
            return 2; // Requiere cambio de cédula
        }

        // SEGUNDO: Verificar si está registrado correctamente
        elseif (str_contains($resultadoLower, 'ya existe') ||
                str_contains($resultadoLower, 'ya cuentas con un registro')) {
            return 1; // Registrado
        }

        // TERCERO: Verificar si NO está registrado (puede registrarse)
        elseif (str_contains($resultadoLower, 'no_registrado') ||
                str_contains($resultadoLower, 'continuar') ||
                str_contains($resultadoLower, 'siguiente') ||
                str_contains($resultadoLower, 'registro exitoso') ||
                str_contains($resultadoLower, 'cuenta creada') ||
                str_contains($resultadoLower, 'bienvenido') ||
                trim($resultado) === '') {
            return 0; // No registrado - puede crear cuenta
        }

        // CUARTO: Error o respuesta desconocida
        else {
            Log::warning("Respuesta no reconocida de SenaSofiaPlus: '{$resultado}'");
            return 0; // Por defecto, asumir no registrado
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error("ValidarSofiaJob falló: {$exception->getMessage()}", [
            'complementario_id' => $this->complementarioId,
            'user_id' => $this->userId,
            'exception' => $exception
        ]);
    }
}

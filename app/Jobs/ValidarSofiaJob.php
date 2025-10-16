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
        Log::info("🚀 Iniciando validación SenaSofiaPlus para programa: {$this->complementarioId}");

        // Obtener registro de progreso si existe
        $progress = null;
        if ($this->progressId) {
            $progress = \App\Models\SofiaValidationProgress::find($this->progressId);
            if ($progress) {
                $progress->markAsStarted();
                Log::info("📊 Progreso inicializado con ID: {$this->progressId}");
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
            Log::info('ℹ️ No hay aspirantes que necesiten validación.');
            if ($progress) {
                $progress->markAsCompleted();
            }
            return;
        }

        $totalAspirantes = $aspirantes->count();
        Log::info("📋 Iniciando validación de {$totalAspirantes} aspirantes...");

        $exitosos = 0;
        $errores = 0;
        $errores_detalle = [];
        $procesados = 0;

        // Procesar en lotes para mejor control de memoria y rate limiting
        $batchSize = 5; // Procesar de 5 en 5 para optimizar
        $batches = $aspirantes->chunk($batchSize);

        foreach ($batches as $batchIndex => $batch) {
            Log::info("🔄 Procesando lote " . ($batchIndex + 1) . "/" . $batches->count() . " ({$batch->count()} aspirantes)");

            foreach ($batch as $aspirante) {
                $procesados++;
                $cedula = $aspirante->persona->numero_documento;

                try {
                    Log::info("🔍 Validando cédula {$cedula} ({$procesados}/{$totalAspirantes})");

                    $startTime = microtime(true);
                    $resultado = $this->validarAspirante($cedula);
                    $endTime = microtime(true);
                    $duration = round($endTime - $startTime, 2);

                    // Actualizar estado basado en resultado
                    $nuevoEstado = $this->determinarEstadoSofia($resultado);
                    $aspirante->persona->update(['estado_sofia' => $nuevoEstado]);

                    $estadoLabel = $this->getEstadoLabel($nuevoEstado);
                    Log::info("✅ Cédula {$cedula}: {$resultado} -> Estado: {$estadoLabel} (Tiempo: {$duration}s)");

                    if ($nuevoEstado === 1) {
                        $exitosos++;
                    }

                    // Actualizar progreso
                    if ($progress) {
                        $progress->incrementProcessed($nuevoEstado === 1);
                    }

                } catch (\Exception $e) {
                    $errorMsg = "❌ Error con cédula {$cedula}: {$e->getMessage()}";
                    Log::error($errorMsg, [
                        'aspirante_id' => $aspirante->id,
                        'persona_id' => $aspirante->persona_id,
                        'complementario_id' => $this->complementarioId,
                        'exception' => $e->getTraceAsString()
                    ]);

                    $errores++;
                    $errores_detalle[] = $errorMsg;

                    // Actualizar progreso con error
                    if ($progress) {
                        $progress->incrementProcessed(false);
                    }
                }

                // Delay optimizado entre validaciones para evitar rate limiting
                $delay = $this->calculateDelay($procesados, $totalAspirantes);
                if ($delay > 0) {
                    Log::debug("⏳ Esperando {$delay}ms antes de siguiente validación...");
                    usleep($delay * 1000); // usleep usa microsegundos
                }
            }

            // Delay adicional entre lotes
            if ($batches->count() > 1 && !$batches->last()->is($batch)) {
                Log::info("🔄 Cambio de lote - Esperando 3 segundos...");
                sleep(3);
            }
        }

        // Marcar como completado
        if ($progress) {
            if ($errores > 0) {
                Log::warning("⚠️ Validación completada con {$errores} errores");
                $progress->markAsFailed($errores_detalle);
            } else {
                Log::info("🎉 Validación completada exitosamente");
                $progress->markAsCompleted();
            }
        }

        Log::info("📊 Resumen final - Total: {$totalAspirantes}, Exitosos: {$exitosos}, Errores: {$errores}, Tasa de éxito: " . round(($exitosos / $totalAspirantes) * 100, 1) . "%");
    }

    /**
     * Calcular delay dinámico basado en el progreso
     */
    private function calculateDelay($procesados, $total)
    {
        $progress = $procesados / $total;

        // Delay inicial alto, luego se reduce
        if ($progress < 0.2) {
            return 3000; // 3 segundos al inicio
        } elseif ($progress < 0.5) {
            return 2000; // 2 segundos en la mitad
        } else {
            return 1000; // 1 segundo al final
        }
    }

    /**
     * Obtener etiqueta legible del estado
     */
    private function getEstadoLabel($estado)
    {
        return match($estado) {
            0 => 'No registrado',
            1 => 'Registrado',
            2 => 'Requiere cambio',
            default => 'Desconocido'
        };
    }

    private function validarAspirante($cedula)
    {
        // Ejecutar script de Node.js con mejor manejo de errores
        $scriptPath = base_path('resources/js/sofia-validator.js');

        Log::debug("Ejecutando script Node.js para cédula: {$cedula}");

        $process = new Process(['node', $scriptPath, $cedula]);
        $process->setTimeout(60000); // Aumentar timeout a 60 segundos
        $process->setWorkingDirectory(base_path());

        try {
            $process->run();

            if (!$process->isSuccessful()) {
                $errorOutput = $process->getErrorOutput();
                $exitCode = $process->getExitCode();

                Log::error("Script Node.js falló para cédula {$cedula}", [
                    'exit_code' => $exitCode,
                    'error_output' => $errorOutput,
                    'command' => $process->getCommandLine()
                ]);

                throw new ProcessFailedException($process);
            }

            $output = trim($process->getOutput());
            Log::debug("Script Node.js completado para cédula {$cedula}: {$output}");

            return $output;

        } catch (ProcessFailedException $e) {
            Log::error("ProcessFailedException para cédula {$cedula}: " . $e->getMessage(), [
                'exit_code' => $e->getExitCode(),
                'error_output' => $e->getErrorOutput(),
                'output' => $e->getOutput()
            ]);
            throw $e;
        }
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

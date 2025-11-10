<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\AspiranteComplementario;
use App\Models\Persona;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ValidarDocumentoJob implements ShouldQueue
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
        Log::info("🚀 Iniciando validación de documentos en Google Drive para programa: {$this->complementarioId}");

        // Obtener registro de progreso si existe
        $progress = null;
        if ($this->progressId) {
            $progress = \App\Models\SofiaValidationProgress::find($this->progressId);
            if ($progress) {
                $progress->markAsStarted();
                Log::info("📊 Progreso inicializado con ID: {$this->progressId}");
            }
        }

        // Obtener todos los aspirantes del programa
        $aspirantes = AspiranteComplementario::with(['persona.tipoDocumento'])
            ->where('complementario_id', $this->complementarioId)
            ->get();

        if ($aspirantes->isEmpty()) {
            Log::info('ℹ️ No hay aspirantes para validar documentos.');
            if ($progress) {
                $progress->markAsCompleted();
            }
            return;
        }

        $totalAspirantes = $aspirantes->count();
        Log::info("📋 Iniciando validación de documentos para {$totalAspirantes} aspirantes...");

        $exitosos = 0;
        $errores = 0;
        $errores_detalle = [];
        $procesados = 0;

        // Procesar en lotes para mejor control de memoria
        $batchSize = 10; // Procesar de 10 en 10
        $batches = $aspirantes->chunk($batchSize);

        foreach ($batches as $batchIndex => $batch) {
            Log::info("🔄 Procesando lote " . ($batchIndex + 1) . "/" . $batches->count() . " ({$batch->count()} aspirantes)");

            foreach ($batch as $aspirante) {
                $procesados++;
                $persona = $aspirante->persona;
                $numeroDocumento = $persona->numero_documento;

                try {
                    Log::info("🔍 Validando documento para cédula {$numeroDocumento} ({$procesados}/{$totalAspirantes})");

                    $startTime = microtime(true);
                    $tieneDocumento = $this->validarDocumentoEnDrive($persona);
                    $endTime = microtime(true);
                    $duration = round($endTime - $startTime, 2);

                    // Actualizar condocumento en la persona
                    $persona->update(['condocumento' => $tieneDocumento ? 1 : 0]);

                    $estadoLabel = $tieneDocumento ? 'Documento encontrado' : 'Documento no encontrado';
                    Log::info("✅ Cédula {$numeroDocumento}: {$estadoLabel} (Tiempo: {$duration}s)");

                    if ($tieneDocumento) {
                        $exitosos++;
                    }

                    // Actualizar progreso
                    if ($progress) {
                        $progress->incrementProcessed($tieneDocumento);
                    }

                } catch (\Exception $e) {
                    $errorMsg = "❌ Error validando documento para cédula {$numeroDocumento}: {$e->getMessage()}";
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

                // Pequeño delay entre validaciones
                if ($procesados < $totalAspirantes) {
                    usleep(100000); // 0.1 segundos
                }
            }

            // Delay adicional entre lotes
            if ($batches->count() > 1 && $batchIndex < $batches->count() - 1) {
                Log::info("🔄 Cambio de lote - Esperando 1 segundo...");
                sleep(1);
            }
        }

        // Marcar como completado
        if ($progress) {
            if ($errores > 0) {
                Log::warning("⚠️ Validación de documentos completada con {$errores} errores");
                $progress->markAsFailed($errores_detalle);
            } else {
                Log::info("🎉 Validación de documentos completada exitosamente");
                $progress->markAsCompleted();
            }
        }

        Log::info("📊 Resumen final - Total: {$totalAspirantes}, Con documento: {$exitosos}, Sin documento: " . ($totalAspirantes - $exitosos - $errores) . ", Errores: {$errores}");
    }

    /**
     * Validar si existe el documento de la persona en Google Drive
     */
    private function validarDocumentoEnDrive($persona): bool
    {
        try {
            // Construir el patrón de búsqueda del archivo en Google Drive
            // Formato: tipo_documento_NumeroDocumento_PrimerNombre_PrimerApellido_*.pdf
            $tipoDocumento = $persona->tipoDocumento ? str_replace(' ', '_', $persona->tipoDocumento->name) : 'DOC';
            $numeroDocumento = $persona->numero_documento;
            $primerNombre = str_replace(' ', '_', $persona->primer_nombre);
            $primerApellido = str_replace(' ', '_', $persona->primer_apellido);
            
            // Listar archivos en Google Drive
            $files = Storage::disk('google')->files('documentos_aspirantes');
            
            // Buscar el archivo que coincida con el patrón
            foreach ($files as $file) {
                $fileName = basename($file);
                // Buscar archivos que empiecen con el patrón esperado
                if (strpos($fileName, "{$tipoDocumento}_{$numeroDocumento}_{$primerNombre}_{$primerApellido}_") === 0) {
                    // Verificar que el archivo existe
                    if (Storage::disk('google')->exists($file)) {
                        Log::debug("✅ Documento encontrado en Google Drive: {$fileName}");
                        return true;
                    }
                }
            }

            Log::debug("❌ Documento no encontrado en Google Drive para: {$tipoDocumento}_{$numeroDocumento}_{$primerNombre}_{$primerApellido}");
            return false;

        } catch (\Exception $e) {
            Log::error("❌ Error al buscar documento en Google Drive", [
                'persona_id' => $persona->id,
                'numero_documento' => $persona->numero_documento,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error("ValidarDocumentoJob falló: {$exception->getMessage()}", [
            'complementario_id' => $this->complementarioId,
            'user_id' => $this->userId,
            'exception' => $exception
        ]);
    }
}


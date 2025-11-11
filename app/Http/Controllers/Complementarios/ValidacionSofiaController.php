<?php

namespace App\Http\Controllers\Complementarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplementarioOfertado;
use App\Models\AspiranteComplementario;
use App\Models\SofiaValidationProgress;
use App\Jobs\ValidarSofiaJob;
use Illuminate\Support\Facades\Log;

class ValidacionSofiaController extends Controller
{
    /**
     * Iniciar validación SOFIA para un programa complementario
     */
    public function validarSofia($complementarioId)
    {
        try {
            Log::info("Iniciando solicitud de validación SenaSofiaPlus", [
                'complementario_id' => $complementarioId,
                'user_id' => auth()->id(),
                'timestamp' => now()
            ]);

            // Verificar que el programa existe
            $programa = ComplementarioOfertado::findOrFail($complementarioId);
            Log::info("Programa encontrado: {$programa->nombre}");

            // Contar aspirantes que necesitan validación
            $aspirantesCount = AspiranteComplementario::with('persona')
                ->where('complementario_id', $complementarioId)
                ->whereHas('persona', function ($query) {
                    $query->whereIn('estado_sofia', [0, 2]);
                })
                ->count();

            Log::info("Aspirantes que necesitan validación: {$aspirantesCount}");

            if ($aspirantesCount === 0) {
                Log::warning("No hay aspirantes que necesiten validación para programa {$complementarioId}");
                return response()->json([
                    'success' => false,
                    'message' => 'No hay aspirantes que necesiten validación en este programa.'
                ]);
            }

            // Verificar si ya hay una validación en progreso para este programa
            $existingProgress = SofiaValidationProgress::where('complementario_id', $complementarioId)
                ->whereIn('status', ['pending', 'processing'])
                ->first();

            if ($existingProgress) {
                Log::warning("Ya existe una validación en progreso para programa {$complementarioId}", [
                    'progress_id' => $existingProgress->id,
                    'status' => $existingProgress->status
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Ya hay una validación en progreso para este programa. Espere a que termine.'
                ]);
            }

            // Crear registro de progreso
            $progress = SofiaValidationProgress::create([
                'complementario_id' => $complementarioId,
                'user_id' => auth()->id(),
                'status' => 'pending',
                'total_aspirantes' => $aspirantesCount,
                'processed_aspirantes' => 0,
                'successful_validations' => 0,
                'failed_validations' => 0,
            ]);

            Log::info("Registro de progreso creado", [
                'progress_id' => $progress->id,
                'total_aspirantes' => $aspirantesCount
            ]);

            // Dispatch el job a la queue con configuración optimizada
            ValidarSofiaJob::dispatch($complementarioId, auth()->id(), $progress->id)
                ->onQueue('sofia-validation') // Usar cola específica para validaciones Sofia
                ->delay(now()->addSeconds(2)); // Pequeño delay para asegurar que el registro esté guardado

            Log::info("Job despachado a la cola", [
                'job_class' => ValidarSofiaJob::class,
                'queue' => 'sofia-validation',
                'delay' => 2
            ]);

            return response()->json([
                'success' => true,
                'message' => "Validación iniciada para {$aspirantesCount} aspirantes. El proceso se ejecutará en segundo plano.",
                'aspirantes_count' => $aspirantesCount,
                'progress_id' => $progress->id
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Programa no encontrado: {$complementarioId}", ['exception' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Programa no encontrado.'
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error iniciando validación SenaSofiaPlus", [
                'complementario_id' => $complementarioId,
                'user_id' => auth()->id(),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el progreso de una validación
     */
    public function getValidationProgress($progressId)
    {
        try {
            $progress = SofiaValidationProgress::with('complementario')->findOrFail($progressId);

            return response()->json([
                'success' => true,
                'progress' => [
                    'id' => $progress->id,
                    'status' => $progress->status,
                    'status_label' => $progress->status_label,
                    'total_aspirantes' => $progress->total_aspirantes,
                    'processed_aspirantes' => $progress->processed_aspirantes,
                    'successful_validations' => $progress->successful_validations,
                    'failed_validations' => $progress->failed_validations,
                    'progress_percentage' => $progress->progress_percentage,
                    'started_at' => $progress->started_at?->format('d/m/Y H:i:s'),
                    'completed_at' => $progress->completed_at?->format('d/m/Y H:i:s'),
                    'errors' => $progress->errors,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el progreso: ' . $e->getMessage()
            ], 500);
        }
    }
}

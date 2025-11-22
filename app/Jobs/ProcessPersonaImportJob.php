<?php

namespace App\Jobs;

use App\Models\PersonaImport;
use App\Services\PersonaImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPersonaImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Identificador de la importación que se está procesando.
     */
    public int $importId;

    /**
     * Cola dedicada para importaciones con configuración independiente.
     */
    private const CONNECTION = 'long-running';
    private const QUEUE = 'long-running';

    public int $tries = 3;
    public int $timeout;

    public function __construct(int $importId)
    {
        $this->importId = $importId;
        $this->onConnection(self::CONNECTION);
        $this->onQueue(self::QUEUE);

        $retryAfter = (int) config('queue.connections.' . self::CONNECTION . '.retry_after', 2400);
        $this->timeout = max(300, $retryAfter - 120);
    }

    public function handle(PersonaImportService $service): void
    {
        $import = PersonaImport::find($this->importId);

        if (!$import) {
            Log::warning('Importación de personas no encontrada', ['import_id' => $this->importId]);
            return;
        }

        try {
            $service->procesar($import);
        } catch (\Throwable $e) {
            $import->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Fallo importando personas', [
                'import_id' => $this->importId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job de importación de personas falló', [
            'import_id' => $this->importId,
            'error' => $exception->getMessage(),
        ]);

        PersonaImport::where('id', $this->importId)
            ->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);
    }
}

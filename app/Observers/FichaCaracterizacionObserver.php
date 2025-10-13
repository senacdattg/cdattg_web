<?php

namespace App\Observers;

use App\Models\FichaCaracterizacion;
use App\Repositories\ConfiguracionRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FichaCaracterizacionObserver
{
    protected ConfiguracionRepository $configuracionRepository;

    public function __construct(ConfiguracionRepository $configuracionRepository)
    {
        $this->configuracionRepository = $configuracionRepository;
    }

    /**
     * Handle the FichaCaracterizacion "created" event.
     */
    public function created(FichaCaracterizacion $ficha): void
    {
        $this->invalidarCache();
        
        Log::info('Ficha de caracterización creada', [
            'ficha_id' => $ficha->id,
            'numero_ficha' => $ficha->ficha,
        ]);
    }

    /**
     * Handle the FichaCaracterizacion "updated" event.
     */
    public function updated(FichaCaracterizacion $ficha): void
    {
        $this->invalidarCache();
        
        if ($ficha->isDirty('status')) {
            Log::info('Estado de ficha cambiado', [
                'ficha_id' => $ficha->id,
                'estado_anterior' => $ficha->getOriginal('status'),
                'estado_nuevo' => $ficha->status,
            ]);
        }
    }

    /**
     * Handle the FichaCaracterizacion "deleted" event.
     */
    public function deleted(FichaCaracterizacion $ficha): void
    {
        $this->invalidarCache();
        
        Log::info('Ficha de caracterización eliminada', [
            'ficha_id' => $ficha->id,
            'numero_ficha' => $ficha->ficha,
        ]);
    }

    /**
     * Invalida caché relacionado con fichas
     */
    protected function invalidarCache(): void
    {
        $this->configuracionRepository->invalidarCacheFichas();
        Cache::tags(['fichas', 'configuracion'])->flush();
    }
}


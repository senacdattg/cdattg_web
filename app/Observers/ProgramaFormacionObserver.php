<?php

namespace App\Observers;

use App\Models\ProgramaFormacion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProgramaFormacionObserver
{
    /**
     * Handle the ProgramaFormacion "created" event.
     */
    public function created(ProgramaFormacion $programa): void
    {
        $this->invalidarCache();
        
        Log::info('Programa de formación creado', [
            'programa_id' => $programa->id,
            'nombre' => $programa->nombre,
        ]);
    }

    /**
     * Handle the ProgramaFormacion "updated" event.
     */
    public function updated(ProgramaFormacion $programa): void
    {
        $this->invalidarCache();
        
        if ($programa->isDirty('status')) {
            Log::info('Estado de programa cambiado', [
                'programa_id' => $programa->id,
                'estado_anterior' => $programa->getOriginal('status'),
                'estado_nuevo' => $programa->status,
            ]);
        }
    }

    /**
     * Handle the ProgramaFormacion "deleted" event.
     */
    public function deleted(ProgramaFormacion $programa): void
    {
        $this->invalidarCache();
        
        Log::info('Programa de formación eliminado', [
            'programa_id' => $programa->id,
            'nombre' => $programa->nombre,
        ]);
    }

    /**
     * Invalida caché relacionado
     */
    protected function invalidarCache(): void
    {
        Cache::tags(['programas', 'configuracion', 'fichas'])->flush();
    }
}


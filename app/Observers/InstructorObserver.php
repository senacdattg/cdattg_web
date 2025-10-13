<?php

namespace App\Observers;

use App\Models\Instructor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class InstructorObserver
{
    /**
     * Handle the Instructor "created" event.
     */
    public function created(Instructor $instructor): void
    {
        $this->invalidarCache();
        
        Log::info('Instructor creado', [
            'instructor_id' => $instructor->id,
            'persona_id' => $instructor->persona_id,
            'regional_id' => $instructor->regional_id,
        ]);
    }

    /**
     * Handle the Instructor "updated" event.
     */
    public function updated(Instructor $instructor): void
    {
        $this->invalidarCache();
        
        $cambios = [];
        if ($instructor->isDirty('status')) {
            $cambios['status'] = [
                'anterior' => $instructor->getOriginal('status'),
                'nuevo' => $instructor->status,
            ];
        }
        
        if ($instructor->isDirty('especialidades')) {
            $cambios['especialidades'] = [
                'anterior' => $instructor->getOriginal('especialidades'),
                'nuevo' => $instructor->especialidades,
            ];
        }

        if (!empty($cambios)) {
            Log::info('Instructor actualizado', [
                'instructor_id' => $instructor->id,
                'cambios' => $cambios,
            ]);
        }
    }

    /**
     * Handle the Instructor "deleted" event.
     */
    public function deleted(Instructor $instructor): void
    {
        $this->invalidarCache();
        
        Log::info('Instructor eliminado', [
            'instructor_id' => $instructor->id,
            'persona_id' => $instructor->persona_id,
        ]);
    }

    /**
     * Invalida caché de instructores
     */
    protected function invalidarCache(): void
    {
        Cache::tags(['instructores', 'personas'])->flush();
    }
}


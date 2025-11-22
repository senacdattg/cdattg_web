<?php

namespace App\Observers;

use App\Models\Aprendiz;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AprendizObserver
{
    /**
     * Handle the Aprendiz "created" event.
     */
    public function created(Aprendiz $aprendiz): void
    {
        $this->assignAprendizRole($aprendiz);
    }

    /**
     * Handle the Aprendiz "updated" event.
     */
    public function updated(Aprendiz $aprendiz): void
    {
        // Si se cambia la ficha, verificar que mantenga el rol
        if ($aprendiz->wasChanged('ficha_caracterizacion_id')) {
            $this->assignAprendizRole($aprendiz);
        }
    }

    /**
     * Handle the Aprendiz "deleted" event.
     */
    public function deleted(Aprendiz $aprendiz): void
    {
        // Evitar warning de parámetro no usado
        unset($aprendiz);
        // Opcional: Remover rol APRENDIZ si se elimina el aprendiz
        // $this->removeAprendizRole($aprendiz);
    }

    /**
     * Handle the Aprendiz "restored" event.
     */
    public function restored(Aprendiz $aprendiz): void
    {
        // Restaurar el rol cuando se restaura el aprendiz
        $this->assignAprendizRole($aprendiz);
    }

    /**
     * Asigna el rol APRENDIZ al usuario asociado a la persona del aprendiz.
     *
     * @param Aprendiz $aprendiz
     * @return void
     */
    private function assignAprendizRole(Aprendiz $aprendiz): void
    {
        try {
            $persona = $aprendiz->persona;
            if (!$persona) {
                Log::warning('Aprendiz sin persona asociada', [
                    'aprendiz_id' => $aprendiz->id,
                    'persona_id' => $aprendiz->persona_id
                ]);
                return;
            }

            // No crear usuario automáticamente: si no existe, registrar y salir
            if (!$persona->user) {
                Log::info('No se creó usuario automáticamente para aprendiz (creación deshabilitada)', [
                    'aprendiz_id' => $aprendiz->id,
                    'persona_id' => $persona->id,
                ]);
                return;
            }
            $user = $persona->user;

            // Verificar que el rol APRENDIZ existe
            Role::firstOrCreate(['name' => 'APRENDIZ']);

            // Sincronizar solo el rol APRENDIZ (evita duplicados)
            $user->syncRoles(['APRENDIZ']);

            Log::info('Rol APRENDIZ sincronizado automáticamente', [
                'aprendiz_id' => $aprendiz->id,
                'user_id' => $user->id,
                'persona_id' => $persona->id,
                'ficha_id' => $aprendiz->ficha_caracterizacion_id
            ]);
        } catch (\Exception $e) {
            Log::error('Error al asignar rol APRENDIZ automáticamente', [
                'aprendiz_id' => $aprendiz->id,
                'persona_id' => $aprendiz->persona_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    // Método removeAprendizRole eliminado por no uso
}

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

            // Crear usuario si no existe
            if (!$persona->user) {
                $user = User::create([
                    'email' => $persona->email ?? "aprendiz_{$aprendiz->id}@sena.edu.co",
                    'password' => Hash::make('123456'), // Password temporal
                    'status' => 1,
                    'persona_id' => $persona->id,
                ]);
                
                Log::info('Usuario creado automáticamente para aprendiz', [
                    'aprendiz_id' => $aprendiz->id,
                    'user_id' => $user->id,
                    'persona_id' => $persona->id,
                    'email' => $user->email
                ]);
            } else {
                $user = $persona->user;
            }

            // Verificar que el rol APRENDIZ existe
            $aprendizRole = Role::firstOrCreate(['name' => 'APRENDIZ']);

            // Asignar rol si no lo tiene
            if (!$user->hasRole('APRENDIZ')) {
                $user->assignRole('APRENDIZ');
                
                Log::info('Rol APRENDIZ asignado automáticamente', [
                    'aprendiz_id' => $aprendiz->id,
                    'user_id' => $user->id,
                    'persona_id' => $persona->id,
                    'ficha_id' => $aprendiz->ficha_caracterizacion_id
                ]);
            } else {
                Log::debug('Usuario ya tiene rol APRENDIZ', [
                    'aprendiz_id' => $aprendiz->id,
                    'user_id' => $user->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error al asignar rol APRENDIZ automáticamente', [
                'aprendiz_id' => $aprendiz->id,
                'persona_id' => $aprendiz->persona_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Remueve el rol APRENDIZ del usuario asociado.
     *
     * @param Aprendiz $aprendiz
     * @return void
     */
    private function removeAprendizRole(Aprendiz $aprendiz): void
    {
        try {
            $persona = $aprendiz->persona;
            if ($persona && $persona->user) {
                $persona->user->removeRole('APRENDIZ');
                
                Log::info('Rol APRENDIZ removido automáticamente', [
                    'aprendiz_id' => $aprendiz->id,
                    'user_id' => $persona->user->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error al remover rol APRENDIZ', [
                'aprendiz_id' => $aprendiz->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

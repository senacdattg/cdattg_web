<?php

namespace App\Observers;

use App\Models\Instructor;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class InstructorObserver
{
    /**
     * Handle the Instructor "created" event.
     */
    public function created(Instructor $instructor): void
    {
        $this->assignInstructorRole($instructor);
    }

    /**
     * Handle the Instructor "updated" event.
     */
    public function updated(Instructor $instructor): void
    {
        // Si se cambia el estado, verificar que mantenga el rol
        if ($instructor->wasChanged('status')) {
            $this->assignInstructorRole($instructor);
        }
    }

    /**
     * Handle the Instructor "deleted" event.
     */
    public function deleted(Instructor $instructor): void
    {
        $this->removeInstructorRole($instructor);
    }

    /**
     * Handle the Instructor "restored" event.
     */
    public function restored(Instructor $instructor): void
    {
        // Restaurar el rol cuando se restaura el instructor
        $this->assignInstructorRole($instructor);
    }

    /**
     * Asigna el rol INSTRUCTOR al usuario asociado a la persona del instructor.
     *
     * @param Instructor $instructor
     * @return void
     */
    private function assignInstructorRole(Instructor $instructor): void
    {
        try {
            $persona = $instructor->persona;
            if (!$persona) {
                Log::warning('Instructor sin persona asociada', [
                    'instructor_id' => $instructor->id,
                    'persona_id' => $instructor->persona_id
                ]);
                return;
            }

            if (!$persona->user) {
                Log::warning('Instructor sin usuario asociado', [
                    'instructor_id' => $instructor->id,
                    'persona_id' => $persona->id
                ]);
                return;
            }

            $user = $persona->user;

            // Verificar que el rol INSTRUCTOR existe
            $instructorRole = Role::firstOrCreate(['name' => 'INSTRUCTOR']);

            // Sincronizar solo el rol INSTRUCTOR (evita duplicados)
            $user->syncRoles(['INSTRUCTOR']);
            
            Log::info('Rol INSTRUCTOR sincronizado automáticamente', [
                'instructor_id' => $instructor->id,
                'user_id' => $user->id,
                'persona_id' => $persona->id,
                'status' => $instructor->status
            ]);

        } catch (\Exception $e) {
            Log::error('Error al asignar rol INSTRUCTOR', [
                'instructor_id' => $instructor->id,
                'persona_id' => $instructor->persona_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Remueve el rol INSTRUCTOR del usuario asociado.
     *
     * @param Instructor $instructor
     * @return void
     */
    private function removeInstructorRole(Instructor $instructor): void
    {
        try {
            $persona = $instructor->persona;
            if ($persona && $persona->user) {
                $user = $persona->user;
                
                // Remover solo el rol INSTRUCTOR, mantener otros roles
                $user->removeRole('INSTRUCTOR');
                
                // Si no tiene otros roles, asignar VISITANTE
                if ($user->roles->isEmpty()) {
                    $visitorRole = Role::firstOrCreate(['name' => 'VISITANTE']);
                    $user->assignRole('VISITANTE');
                }
                
                Log::info('Rol INSTRUCTOR removido automáticamente', [
                    'instructor_id' => $instructor->id,
                    'user_id' => $user->id,
                    'persona_id' => $persona->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error al remover rol INSTRUCTOR', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
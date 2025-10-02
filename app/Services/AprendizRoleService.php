<?php

namespace App\Services;

use App\Models\Aprendiz;
use App\Models\User;
use App\Models\Persona;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AprendizRoleService
{
    /**
     * Asegura que un aprendiz tenga el rol APRENDIZ asignado.
     *
     * @param Aprendiz $aprendiz
     * @return bool
     */
    public function ensureAprendizRole(Aprendiz $aprendiz): bool
    {
        try {
            $persona = $aprendiz->persona;
            if (!$persona) {
                Log::warning('Aprendiz sin persona asociada en AprendizRoleService', [
                    'aprendiz_id' => $aprendiz->id,
                    'persona_id' => $aprendiz->persona_id
                ]);
                return false;
            }

            // Crear usuario si no existe
            if (!$persona->user) {
                $user = $this->createUserForPersona($persona, $aprendiz);
                if (!$user) {
                    return false;
                }
            } else {
                $user = $persona->user;
            }

            // Verificar que el rol APRENDIZ existe
            $aprendizRole = Role::firstOrCreate(['name' => 'APRENDIZ']);

            // Asignar rol si no lo tiene
            if (!$user->hasRole('APRENDIZ')) {
                $user->assignRole('APRENDIZ');
                
                Log::info('Rol APRENDIZ asignado por AprendizRoleService', [
                    'aprendiz_id' => $aprendiz->id,
                    'user_id' => $user->id,
                    'persona_id' => $persona->id,
                    'ficha_id' => $aprendiz->ficha_caracterizacion_id
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error en AprendizRoleService::ensureAprendizRole', [
                'aprendiz_id' => $aprendiz->id,
                'persona_id' => $aprendiz->persona_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Sincroniza los roles del aprendiz con los del usuario asociado.
     *
     * @param Aprendiz $aprendiz
     * @return void
     */
    public function syncRolesWithUser(Aprendiz $aprendiz): void
    {
        try {
            if ($aprendiz->persona && $aprendiz->persona->user) {
                $userRoles = $aprendiz->persona->user->roles->pluck('name')->toArray();
                $aprendiz->syncRoles($userRoles);
                
                Log::info('Roles sincronizados entre Aprendiz y User', [
                    'aprendiz_id' => $aprendiz->id,
                    'user_id' => $aprendiz->persona->user->id,
                    'roles' => $userRoles
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error al sincronizar roles en AprendizRoleService', [
                'aprendiz_id' => $aprendiz->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remueve el rol APRENDIZ del usuario asociado.
     *
     * @param Aprendiz $aprendiz
     * @return bool
     */
    public function removeAprendizRole(Aprendiz $aprendiz): bool
    {
        try {
            $persona = $aprendiz->persona;
            if ($persona && $persona->user) {
                $persona->user->removeRole('APRENDIZ');
                
                Log::info('Rol APRENDIZ removido por AprendizRoleService', [
                    'aprendiz_id' => $aprendiz->id,
                    'user_id' => $persona->user->id
                ]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Error al remover rol APRENDIZ en AprendizRoleService', [
                'aprendiz_id' => $aprendiz->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Valida la consistencia de roles entre Aprendiz y User.
     *
     * @param Aprendiz $aprendiz
     * @return array
     */
    public function validateRoleConsistency(Aprendiz $aprendiz): array
    {
        $issues = [];
        
        try {
            if (!$aprendiz->persona) {
                $issues[] = 'Aprendiz sin persona asociada';
                return $issues;
            }

            if (!$aprendiz->persona->user) {
                $issues[] = 'Persona sin usuario asociado';
                return $issues;
            }

            $user = $aprendiz->persona->user;
            $userRoles = $user->roles->pluck('name')->toArray();
            $aprendizRoles = $aprendiz->roles->pluck('name')->toArray();

            // Verificar que el usuario tenga el rol APRENDIZ
            if (!in_array('APRENDIZ', $userRoles)) {
                $issues[] = 'Usuario no tiene rol APRENDIZ';
            }

            // Verificar que el aprendiz tenga el rol APRENDIZ
            if (!in_array('APRENDIZ', $aprendizRoles)) {
                $issues[] = 'Aprendiz no tiene rol APRENDIZ';
            }

            // Verificar consistencia de roles
            $missingInAprendiz = array_diff($userRoles, $aprendizRoles);
            $missingInUser = array_diff($aprendizRoles, $userRoles);

            if (!empty($missingInAprendiz)) {
                $issues[] = 'Roles faltantes en Aprendiz: ' . implode(', ', $missingInAprendiz);
            }

            if (!empty($missingInUser)) {
                $issues[] = 'Roles faltantes en User: ' . implode(', ', $missingInUser);
            }

        } catch (\Exception $e) {
            $issues[] = 'Error al validar consistencia: ' . $e->getMessage();
        }

        return $issues;
    }

    /**
     * Crea un usuario para una persona.
     *
     * @param Persona $persona
     * @param Aprendiz $aprendiz
     * @return User|null
     */
    private function createUserForPersona(Persona $persona, Aprendiz $aprendiz): ?User
    {
        try {
            $email = $persona->email ?? "aprendiz_{$aprendiz->id}@sena.edu.co";
            
            // Verificar que el email no esté en uso
            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                $email = "aprendiz_{$aprendiz->id}_{$persona->id}@sena.edu.co";
            }

            $user = User::create([
                'email' => $email,
                'password' => Hash::make('123456'), // Password temporal
                'status' => 1,
                'persona_id' => $persona->id,
            ]);
            
            Log::info('Usuario creado por AprendizRoleService', [
                'aprendiz_id' => $aprendiz->id,
                'user_id' => $user->id,
                'persona_id' => $persona->id,
                'email' => $user->email
            ]);

            return $user;
        } catch (\Exception $e) {
            Log::error('Error al crear usuario en AprendizRoleService', [
                'aprendiz_id' => $aprendiz->id,
                'persona_id' => $persona->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Obtiene estadísticas de roles de aprendices.
     *
     * @return array
     */
    public function getRoleStatistics(): array
    {
        try {
            $totalAprendices = Aprendiz::count();
            $aprendicesConUsuario = Aprendiz::whereHas('persona.user')->count();
            $aprendicesConRol = Aprendiz::whereHas('persona.user', function ($query) {
                $query->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', 'APRENDIZ');
                });
            })->count();

            return [
                'total_aprendices' => $totalAprendices,
                'con_usuario' => $aprendicesConUsuario,
                'con_rol_aprendiz' => $aprendicesConRol,
                'sin_usuario' => $totalAprendices - $aprendicesConUsuario,
                'sin_rol_aprendiz' => $aprendicesConUsuario - $aprendicesConRol,
                'porcentaje_con_rol' => $totalAprendices > 0 ? round(($aprendicesConRol / $totalAprendices) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas de roles', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}

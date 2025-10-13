<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class PermisoService
{
    public function asignarPermisos(int $userId, array $permisos): bool
    {
        return DB::transaction(function () use ($userId, $permisos) {
            $user = User::findOrFail($userId);
            
            // Validar si necesita rol de instructor
            if (in_array('TOMAR ASISTENCIA', $permisos)) {
                $roles = $user->roles->pluck('name');
                
                if (!$roles->contains('INSTRUCTOR')) {
                    throw new \Exception('El usuario debe tener el rol de INSTRUCTOR para tomar asistencia.');
                }
            }

            // Sincronizar permisos
            $user->syncPermissions($permisos);

            Log::info('Permisos asignados', [
                'user_id' => $userId,
                'permisos_count' => count($permisos),
            ]);

            return true;
        });
    }

    public function obtenerPermisosPorRol(string $rol): array
    {
        // Retorna permisos específicos según el rol
        $permisosMap = [
            'ADMINISTRADOR' => Permission::all()->pluck('name')->toArray(),
            'INSTRUCTOR' => [
                'VER FICHA',
                'TOMAR ASISTENCIA',
                'VER APRENDIZ',
                'VER ASISTENCIA',
            ],
            'COORDINADOR' => [
                'VER FICHA',
                'CREAR FICHA',
                'EDITAR FICHA',
                'VER INSTRUCTOR',
                'ASIGNAR INSTRUCTOR',
            ],
        ];

        return $permisosMap[$rol] ?? [];
    }
}


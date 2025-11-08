<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $personaPermissions = [
            'CREAR PERSONA',
            'VER PERSONA',
            'EDITAR PERSONA',
            'ELIMINAR PERSONA',
            'CAMBIAR ESTADO PERSONA',
        ];

        $role = Role::where('name', 'SUPER ADMINISTRADOR')->first();

        if ($role) {
            foreach ($personaPermissions as $permissionName) {
                $permission = Permission::firstOrCreate(['name' => $permissionName]);

                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }

        $superAdminUser = User::where('email', 'superAdmin@superAdmin.com')->first();

        if ($superAdminUser && $role && !$superAdminUser->hasRole($role->name)) {
            $superAdminUser->assignRole($role);
        }
    }

    public function down(): void
    {
        // No se revocan permisos para evitar dejar el sistema sin configuraciones básicas.
    }
};

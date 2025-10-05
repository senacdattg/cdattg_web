<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FichaCaracterizacionPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir permisos específicos para FichaCaracterizacion
        $fichaPermissions = [
            'CREAR FICHA CARACTERIZACION',
            'EDITAR FICHA CARACTERIZACION',
            'VER FICHA CARACTERIZACION',
            'ELIMINAR FICHA CARACTERIZACION',
            'GESTIONAR INSTRUCTORES FICHA',
            'GESTIONAR DIAS FICHA',
            'GESTIONAR APRENDICES FICHA',
            'CAMBIAR ESTADO FICHA',
        ];

        // Crear cada permiso si no existe
        foreach ($fichaPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
            $this->command->info("Permiso creado/verificado: {$permissionName}");
        }

        // Asignar permisos a roles específicos
        
        // SUPER ADMINISTRADOR - Todos los permisos
        $superAdminRole = Role::where('name', 'SUPER ADMINISTRADOR')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($fichaPermissions);
            $this->command->info('Permisos asignados a SUPER ADMINISTRADOR');
        }

        // ADMINISTRADOR - Todos los permisos de fichas
        $adminRole = Role::where('name', 'ADMINISTRADOR')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($fichaPermissions);
            $this->command->info('Permisos asignados a ADMINISTRADOR');
        }

        // INSTRUCTOR - Permisos limitados
        $instructorRole = Role::where('name', 'INSTRUCTOR')->first();
        if ($instructorRole) {
            $instructorPermissions = [
                'VER FICHA CARACTERIZACION',
                'EDITAR FICHA CARACTERIZACION',
                'GESTIONAR DIAS FICHA',
                'GESTIONAR APRENDICES FICHA',
                'CAMBIAR ESTADO FICHA',
            ];
            
            $instructorRole->givePermissionTo($instructorPermissions);
            $this->command->info('Permisos limitados asignados a INSTRUCTOR');
        }

        $this->command->info('Permisos de FichaCaracterizacion configurados exitosamente');
    }
}

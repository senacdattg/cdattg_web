<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ResultadosAprendizajePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permisosRAP = [
            // Permisos básicos CRUD
            'VER RESULTADO APRENDIZAJE',
            'CREAR RESULTADO APRENDIZAJE',
            'EDITAR RESULTADO APRENDIZAJE',
            'ELIMINAR RESULTADO APRENDIZAJE',
            
            // Permisos de gestión específicos
            'GESTIONAR COMPETENCIAS RAP',
            'CAMBIAR ESTADO RAP',
            'ASOCIAR GUIA RAP',
            'DESASOCIAR GUIA RAP',
            
            // Permisos adicionales
            'EXPORTAR RAP',
            'IMPORTAR RAP',
            'VER REPORTES RAP',
        ];

        foreach ($permisosRAP as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
            $this->command->info("Permiso creado/verificado: {$permiso}");
        }

        $superAdmin = Role::where('name', 'SUPER ADMINISTRADOR')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permisosRAP);
            $this->command->info('Todos los permisos de RAP asignados a SUPER ADMINISTRADOR');
        }

        $admin = Role::where('name', 'ADMINISTRADOR')->first();
        if ($admin) {
            $admin->givePermissionTo($permisosRAP);
            $this->command->info('Todos los permisos de RAP asignados a ADMINISTRADOR');
        }

        $instructor = Role::where('name', 'INSTRUCTOR')->first();
        if ($instructor) {
            $permisosInstructor = [
                'VER RESULTADO APRENDIZAJE',
                'CREAR RESULTADO APRENDIZAJE',
                'EDITAR RESULTADO APRENDIZAJE',
                'GESTIONAR COMPETENCIAS RAP',
                'CAMBIAR ESTADO RAP',
                'ASOCIAR GUIA RAP',
                'DESASOCIAR GUIA RAP',
                'EXPORTAR RAP',
                'VER REPORTES RAP',
            ];
            
            $instructor->givePermissionTo($permisosInstructor);
            $this->command->info('Permisos limitados de RAP asignados a INSTRUCTOR');
        }

        $this->command->info('Permisos de Resultados de Aprendizaje configurados exitosamente');
    }
}


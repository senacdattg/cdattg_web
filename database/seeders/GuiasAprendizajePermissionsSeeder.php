<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class GuiasAprendizajePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir permisos específicos para Guías de Aprendizaje
        $permisosGuiasAprendizaje = [
            // Permisos básicos CRUD
            'VER GUIA APRENDIZAJE',
            'CREAR GUIA APRENDIZAJE',
            'EDITAR GUIA APRENDIZAJE',
            'ELIMINAR GUIA APRENDIZAJE',
            
            // Permisos de gestión específicos
            'GESTIONAR RESULTADOS GUIA',
            'GESTIONAR EVIDENCIAS GUIA',
            'CAMBIAR ESTADO GUIA',
            
            // Permisos adicionales para funcionalidades avanzadas
            'EXPORTAR GUIA PDF',
            'EXPORTAR GUIA EXCEL',
            'DUPLICAR GUIA',
            'CREAR GUIA DESDE PLANTILLA',
            'GESTIONAR PLANTILLAS GUIA',
            'VER REPORTES GUIA',
            'GESTIONAR COMENTARIOS GUIA',
            'GESTIONAR VERSIONES GUIA',
        ];

        // Crear cada permiso si no existe
        foreach ($permisosGuiasAprendizaje as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Obtener roles existentes
        $superAdmin = Role::where('name', 'SUPER ADMINISTRADOR')->first();
        $admin = Role::where('name', 'ADMINISTRADOR')->first();
        $instructor = Role::where('name', 'INSTRUCTOR')->first();

        // Asignar TODOS los permisos de Guías de Aprendizaje al SUPER ADMINISTRADOR
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permisosGuiasAprendizaje);
        }

        // Asignar permisos completos al ADMINISTRADOR (excepto gestión de plantillas)
        if ($admin) {
            $permisosAdmin = [
                'VER GUIA APRENDIZAJE',
                'CREAR GUIA APRENDIZAJE',
                'EDITAR GUIA APRENDIZAJE',
                'ELIMINAR GUIA APRENDIZAJE',
                'GESTIONAR RESULTADOS GUIA',
                'GESTIONAR EVIDENCIAS GUIA',
                'CAMBIAR ESTADO GUIA',
                'EXPORTAR GUIA PDF',
                'EXPORTAR GUIA EXCEL',
                'DUPLICAR GUIA',
                'CREAR GUIA DESDE PLANTILLA',
                'VER REPORTES GUIA',
                'GESTIONAR COMENTARIOS GUIA',
                'GESTIONAR VERSIONES GUIA',
            ];
            $admin->givePermissionTo($permisosAdmin);
        }

        // Asignar permisos limitados al INSTRUCTOR
        if ($instructor) {
            $permisosInstructor = [
                'VER GUIA APRENDIZAJE',
                'CREAR GUIA APRENDIZAJE',
                'EDITAR GUIA APRENDIZAJE', // Solo sus propias guías
                'GESTIONAR RESULTADOS GUIA', // Solo sus propias guías
                'GESTIONAR EVIDENCIAS GUIA', // Solo sus propias guías
                'CAMBIAR ESTADO GUIA', // Solo sus propias guías
                'EXPORTAR GUIA PDF', // Solo sus propias guías
                'DUPLICAR GUIA',
                'CREAR GUIA DESDE PLANTILLA',
                'VER REPORTES GUIA', // Solo sus propias guías
                'GESTIONAR COMENTARIOS GUIA', // Solo sus propias guías
            ];
            $instructor->givePermissionTo($permisosInstructor);
        }

        $this->command->info('Permisos de Guías de Aprendizaje creados y asignados exitosamente.');
    }
}

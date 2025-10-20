<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o obtener el rol SUPER ADMINISTRADOR
        $superAdminRole = Role::firstOrCreate(['name' => 'SUPER ADMINISTRADOR']);

        // Definir todos los permisos del sistema
        $allPermissions = [
            // Permisos para parámetros
            'CREAR PARAMETRO',
            'EDITAR PARAMETRO',
            'VER PARAMETRO',
            'ELIMINAR PARAMETRO',
            
            // Permisos para temas
            'CREAR TEMA',
            'EDITAR TEMA',
            'VER TEMA',
            'ELIMINAR TEMA',
            'ELIMINAR PARAMETRO DE TEMA',
            
            // Permisos para regionales
            'CREAR REGIONAL',
            'EDITAR REGIONAL',
            'VER REGIONAL',
            'ELIMINAR REGIONAL',
            
            // Permisos para municipios
            'CREAR MUNICIPIO',
            'EDITAR MUNICIPIO',
            'VER MUNICIPIO',
            'ELIMINAR MUNICIPIO',
            
            // Permisos para centros de formación
            'CREAR CENTRO DE FORMACION',
            'EDITAR CENTRO DE FORMACION',
            'VER CENTROS DE FORMACION',
            'ELIMINAR CENTRO DE FORMACION',
            
            // Permisos para sedes
            'CREAR SEDE',
            'VER SEDE',
            'EDITAR SEDE',
            'ELIMINAR SEDE',
            
            // Permisos para bloques
            'CREAR BLOQUE',
            'VER BLOQUE',
            'EDITAR BLOQUE',
            'ELIMINAR BLOQUE',
            
            // Permisos para pisos
            'CREAR PISO',
            'VER PISO',
            'EDITAR PISO',
            'ELIMINAR PISO',
            
            // Permisos para ambientes
            'CREAR AMBIENTE',
            'VER AMBIENTE',
            'EDITAR AMBIENTE',
            'ELIMINAR AMBIENTE',
            
            // Permisos para instructores
            'CREAR INSTRUCTOR',
            'VER INSTRUCTOR',
            'EDITAR INSTRUCTOR',
            'ELIMINAR INSTRUCTOR',
            'GESTIONAR ESPECIALIDADES INSTRUCTOR',
            'VER FICHAS ASIGNADAS',
            'CAMBIAR ESTADO INSTRUCTOR',
            
            // Permisos para fichas de caracterización
            'CREAR FICHA CARACTERIZACION',
            'EDITAR FICHA CARACTERIZACION',
            'VER FICHA CARACTERIZACION',
            'ELIMINAR FICHA CARACTERIZACION',
            'GESTIONAR INSTRUCTORES FICHA',
            'GESTIONAR APRENDICES FICHA',
            'GESTIONAR DIAS FICHA',
            'CAMBIAR ESTADO FICHA',
            
            // Permisos para roles y permisos
            'ASIGNAR PERMISOS',
            
            // Permisos para asistencia
            'TOMAR ASISTENCIA',
            
            // Permisos para caracterización de programas
            'VER PROGRAMA DE CARACTERIZACION',
            'CREAR PROGRAMA DE CARACTERIZACION',
            'EDITAR PROGRAMA DE CARACTERIZACION',
            'ELIMINAR PROGRAMA DE CARACTERIZACION',
            
            // Permisos para personas
            'CREAR PERSONA',
            'VER PERSONA',
            'EDITAR PERSONA',
            'ELIMINAR PERSONA',
            'CAMBIAR ESTADO PERSONA',
            
            // Permisos para redes de conocimiento
            'CREAR RED CONOCIMIENTO',
            'EDITAR RED CONOCIMIENTO',
            'VER RED CONOCIMIENTO',
            'ELIMINAR RED CONOCIMIENTO',
            
            // Permisos para aprendices
            'VER APRENDIZ',
            'CREAR APRENDIZ',
            'EDITAR APRENDIZ',
            'ELIMINAR APRENDIZ',
            
            // Permisos para programas de formación
            'programa.index',
            'programa.show',
            'programa.create',
            'programa.edit',
            'programa.delete',
            'programa.search',
        ];

        // Crear cada permiso si no existe
        foreach ($allPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Asignar TODOS los permisos al rol SUPER ADMINISTRADOR
        $superAdminRole->syncPermissions($allPermissions);

        // Crear o actualizar el usuario Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'email' => 'admin@admin.com',
                'password' => Hash::make('123456'),
                'status' => 1,
                'persona_id' => 1, // Usar persona_id = 1 (debe existir por PersonaSeeder)
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Asignar el rol SUPER ADMINISTRADOR al usuario
        if (!$superAdmin->hasRole('SUPER ADMINISTRADOR')) {
            $superAdmin->assignRole('SUPER ADMINISTRADOR');
        }

        $this->command->info('Super Admin creado exitosamente:');
        $this->command->info('Email: admin@admin.com');
        $this->command->info('Password: 123456');
        $this->command->info('Rol: SUPER ADMINISTRADOR');
        $this->command->info('Permisos asignados: ' . count($allPermissions) . ' permisos');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Crear roles usando firstOrCreate para evitar duplicados
        $superAdministrador = Role::firstOrCreate(['name' => 'SUPER ADMINISTRADOR']);
        $administrador      = Role::firstOrCreate(['name' => 'ADMINISTRADOR']);
        $instructor         = Role::firstOrCreate(['name' => 'INSTRUCTOR']);
        $visitante          = Role::firstOrCreate(['name' => 'VISITANTE']);
        $aprendiz           = Role::firstOrCreate(['name' => 'APRENDIZ']);

        // Definir un arreglo de permisos para cada grupo
        $permisos = [   
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
            'GESTIONAR DIAS FICHA',
            'GESTIONAR APRENDICES FICHA',
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
            //Permisos para crear personas
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
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Asignar permisos a SUPER ADMINISTRADOR (todos los permisos que quieras para este rol)
        $superAdministrador->givePermissionTo([
            'CREAR PARAMETRO',
            'EDITAR PARAMETRO',
            'VER PARAMETRO',
            'ELIMINAR PARAMETRO',
            'CREAR TEMA',
            'EDITAR TEMA',
            'VER TEMA',
            'ELIMINAR TEMA',
            'ELIMINAR PARAMETRO DE TEMA',
            'CREAR REGIONAL',
            'EDITAR REGIONAL',
            'VER REGIONAL',
            'ELIMINAR REGIONAL',
            'CREAR MUNICIPIO',
            'EDITAR MUNICIPIO',
            'VER MUNICIPIO',
            'ELIMINAR MUNICIPIO',
            'CREAR CENTRO DE FORMACION',
            'EDITAR CENTRO DE FORMACION',
            'VER CENTROS DE FORMACION',
            'ELIMINAR CENTRO DE FORMACION',
            'CREAR SEDE',
            'VER SEDE',
            'EDITAR SEDE',
            'ELIMINAR SEDE',
            'CREAR BLOQUE',
            'VER BLOQUE',
            'EDITAR BLOQUE',
            'ELIMINAR BLOQUE',
            'CREAR PISO',
            'VER PISO',
            'EDITAR PISO',
            'ELIMINAR PISO',
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
            'ASIGNAR PERMISOS',
            'VER PROGRAMA DE CARACTERIZACION',
            'CREAR PROGRAMA DE CARACTERIZACION',
            'EDITAR PROGRAMA DE CARACTERIZACION',
            'ELIMINAR PROGRAMA DE CARACTERIZACION',
            // Permisos para fichas de caracterización
            'CREAR FICHA CARACTERIZACION',
            'EDITAR FICHA CARACTERIZACION',
            'VER FICHA CARACTERIZACION',
            'ELIMINAR FICHA CARACTERIZACION',
            'GESTIONAR INSTRUCTORES FICHA',
            'GESTIONAR DIAS FICHA',
            'GESTIONAR APRENDICES FICHA',
            'CAMBIAR ESTADO FICHA',
            'CREAR PERSONA',
            'VER PERSONA',
            'EDITAR PERSONA',
            'ELIMINAR PERSONA',
            'CAMBIAR ESTADO PERSONA',
            'CREAR RED CONOCIMIENTO',
            'EDITAR RED CONOCIMIENTO',
            'VER RED CONOCIMIENTO',
            'ELIMINAR RED CONOCIMIENTO',
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
        ]);

        // Asignar permisos al rol ADMINISTRADOR
        $administrador->givePermissionTo([
            // Permisos para fichas de caracterización
            'CREAR FICHA CARACTERIZACION',
            'EDITAR FICHA CARACTERIZACION',
            'VER FICHA CARACTERIZACION',
            'ELIMINAR FICHA CARACTERIZACION',
            'GESTIONAR INSTRUCTORES FICHA',
            'GESTIONAR DIAS FICHA',
            'GESTIONAR APRENDICES FICHA',
            'CAMBIAR ESTADO FICHA',
            // Permisos para instructores
            'CREAR INSTRUCTOR',
            'VER INSTRUCTOR',
            'EDITAR INSTRUCTOR',
            'ELIMINAR INSTRUCTOR',
            'GESTIONAR ESPECIALIDADES INSTRUCTOR',
            'VER FICHAS ASIGNADAS',
            'CAMBIAR ESTADO INSTRUCTOR',
            // Permisos para personas
            'CREAR PERSONA',
            'VER PERSONA',
            'EDITAR PERSONA',
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
            // Permisos para programas de formación (solo lectura y búsqueda para administradores)
            'programa.index',
            'programa.show',
            'programa.search',
        ]);

        // Asignar permisos al rol INSTRUCTOR
        $instructor->givePermissionTo([
            'TOMAR ASISTENCIA',
            'VER APRENDIZ',
            // Permisos para instructores (solo los que le corresponden)
            'VER INSTRUCTOR',
            'VER FICHAS ASIGNADAS',
            // Permisos para fichas de caracterización (limitados a fichas asignadas)
            'VER FICHA CARACTERIZACION',
            'EDITAR FICHA CARACTERIZACION',
            'GESTIONAR DIAS FICHA',
            'GESTIONAR APRENDICES FICHA',
            'CAMBIAR ESTADO FICHA',
            // Permisos para programas de formación (solo consulta para instructores)
            'programa.index',
            'programa.show',
            'programa.search',
        ]);

        // Asignar permisos al rol APRENDIZ
        // Los aprendices tienen acceso limitado solo a consultar su propia información
        $aprendiz->givePermissionTo([
            // Por ahora, el rol aprendiz no tiene permisos específicos
            // Se controla el acceso mediante políticas y middleware
        ]);
    }
}

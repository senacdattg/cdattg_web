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
        $bot = Role::firstOrCreate(['name' => 'BOT']);
        $superAdmin = Role::firstOrCreate(['name' => 'SUPER ADMINISTRADOR']);
        $admin      = Role::firstOrCreate(['name' => 'ADMINISTRADOR']);
        $instructor         = Role::firstOrCreate(['name' => 'INSTRUCTOR']);
        $visitante          = Role::firstOrCreate(['name' => 'VISITANTE']);
        $aprendiz           = Role::firstOrCreate(['name' => 'APRENDIZ']);
        $aspirante          = Role::firstOrCreate(['name' => 'ASPIRANTE']);

        // Definir  un arreglo de permisos para cada grupo
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
            'VER CENTRO DE FORMACION',
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
            'CAMBIAR ESTADO INSTRUCTOR',
            // Permisos para fichas de caracterización
            'CREAR FICHA DE CARACTERIZACION',
            'EDITAR FICHA DE CARACTERIZACION',
            'VER FICHA DE CARACTERIZACION',
            'ELIMINAR FICHA DE CARACTERIZACION',
            'CREAR FICHA CARACTERIZACION',
            'EDITAR FICHA CARACTERIZACION',
            'VER FICHA CARACTERIZACION',
            'ELIMINAR FICHA CARACTERIZACION',
            'GESTIONAR INSTRUCTORES FICHA',
            'GESTIONAR INSTRUCTORES',
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
            'VER PERFIL',
            'EDITAR PERSONA',
            'ELIMINAR PERSONA',
            'CAMBIAR ESTADO PERSONA',
            'RESTABLECER PASSWORD',
            // Permisos para inventario
            // Productos
            'CREAR PRODUCTO',
            'VER PRODUCTO',
            'EDITAR PRODUCTO',
            'ELIMINAR PRODUCTO',
            'VER CATALOGO PRODUCTO',
            'BUSCAR PRODUCTO',
            // Carrito
            'VER CARRITO',
            'AGREGAR CARRITO',
            'ACTUALIZAR CARRITO',
            'ELIMINAR CARRITO',
            'VACIAR CARRITO',
            // Categorías
            'CREAR CATEGORIA',
            'VER CATEGORIA',
            'EDITAR CATEGORIA',
            'ELIMINAR CATEGORIA',
            // Marcas
            'CREAR MARCA',
            'VER MARCA',
            'EDITAR MARCA',
            'ELIMINAR MARCA',
            // Contratos y Convenios
            'CREAR CONTRATO',
            'VER CONTRATO',
            'EDITAR CONTRATO',
            'ELIMINAR CONTRATO',
            // Proveedores
            'CREAR PROVEEDOR',
            'VER PROVEEDOR',
            'EDITAR PROVEEDOR',
            'ELIMINAR PROVEEDOR',
            // Ordenes
            'VER ORDEN',
            'CREAR ORDEN',
            'EDITAR ORDEN',
            'ELIMINAR ORDEN',
            'APROBAR ORDEN',
            'COMPLETAR ORDEN',
            // Préstamos
            'VER PRESTAMO',
            'CREAR PRESTAMO',
            'EDITAR PRESTAMO',
            'DEVOLVER PRESTAMO',
            'APROBAR PRESTAMO',
            // Entradas
            'VER ENTRADA',
            'CREAR ENTRADA',
            // Salidas
            'VER SALIDA',
            'CREAR SALIDA',
            // Devoluciones
            'VER DEVOLUCION',
            'CREAR DEVOLUCION',
            'PROCESAR DEVOLUCION',
            // Notificaciones
            'VER NOTIFICACION',
            // Dashboard
            'VER DASHBOARD INVENTARIO',
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

            // Permisos para resultados de aprendizaje
            'VER RESULTADO APRENDIZAJE',
            'CREAR RESULTADO APRENDIZAJE',
            'EDITAR RESULTADO APRENDIZAJE',
            'ELIMINAR RESULTADO APRENDIZAJE',
            'GESTIONAR COMPETENCIAS RAP',
            'CAMBIAR ESTADO RAP',
            // Permisos para competencias
            'VER COMPETENCIA',
            'CREAR COMPETENCIA',
            'EDITAR COMPETENCIA',
            'ELIMINAR COMPETENCIA',
            'GESTIONAR RESULTADOS COMPETENCIA',
            'CAMBIAR ESTADO COMPETENCIA',
            'ASOCIAR GUIA RAP',
            'DESASOCIAR GUIA RAP',
            'EXPORTAR RAP',
            'IMPORTAR RAP',
            'VER REPORTES RAP',
            // Permisos para aspirantes complementarios
            'VER ESTADISTICAS',
            // Permisos para programas complementarios
            'VER PROGRAMA COMPLEMENTARIO',
            'CREAR PROGRAMA COMPLEMENTARIO',
            'ELIMINAR ASPIRANTE COMPLEMENTARIO',
            // Permisos para talento humano
            'VER TALENTO HUMANO',

            'VER FICHAS ASIGNADAS',
            'GESTIONAR DIAS FICHA',
            'GESTIONAR APRENDICES FICHA',
            'GESTIONAR APRENDICES',
            'CAMBIAR ESTADO FICHA',
            'ASIGNACION DE INSTRUCTORES',
        ];

        // Crear cada permiso si no existe
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Asignar todos los permisos existentes al SUPER ADMINISTRADOR
        $superAdmin->syncPermissions(Permission::all());

        // Asignar permisos al rol ADMINISTRADOR
        $admin->givePermissionTo([
            'CREAR FICHA DE CARACTERIZACION',
            'EDITAR FICHA DE CARACTERIZACION',
            'VER FICHA DE CARACTERIZACION',
            'ELIMINAR FICHA DE CARACTERIZACION',
            'CREAR FICHA CARACTERIZACION',
            'EDITAR FICHA CARACTERIZACION',
            'VER FICHA CARACTERIZACION',
            'ELIMINAR FICHA CARACTERIZACION',
            'GESTIONAR INSTRUCTORES FICHA',
            'GESTIONAR INSTRUCTORES',
            'CREAR INSTRUCTOR',
            'VER INSTRUCTOR',
            'EDITAR INSTRUCTOR',
            'ELIMINAR INSTRUCTOR',
            'GESTIONAR ESPECIALIDADES INSTRUCTOR',
            'CAMBIAR ESTADO INSTRUCTOR',
            'VER CENTRO DE FORMACION',
            'CREAR PERSONA',
            'VER PERSONA',
            'EDITAR PERSONA',
            'CAMBIAR ESTADO PERSONA',
            'RESTABLECER PASSWORD',

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
            'VER NOTIFICACION',
            'VER ESTADISTICAS',
            'VER PROGRAMA COMPLEMENTARIO',
            'CREAR PROGRAMA COMPLEMENTARIO',
            'ELIMINAR ASPIRANTE COMPLEMENTARIO',
            'VER TALENTO HUMANO',
            'ASIGNACION DE INSTRUCTORES',
        ]);

        // Asignar permisos al rol INSTRUCTOR
        $instructor->givePermissionTo([
            'TOMAR ASISTENCIA',

            'VER APRENDIZ',
            // Permisos para instructores (solo los que le corresponden)
            'VER INSTRUCTOR',
            'VER FICHAS ASIGNADAS',
            // Permisos para fichas de caracterización (limitados a fichas asignadas)
            'VER FICHA DE CARACTERIZACION',
            'EDITAR FICHA DE CARACTERIZACION',
            'GESTIONAR DIAS FICHA',
            'GESTIONAR APRENDICES FICHA',
            'CAMBIAR ESTADO FICHA',
            // Permisos para programas de formación (solo consulta para instructores)
            'programa.index',
            'programa.show',
            'programa.search',
            // Permisos para resultados de aprendizaje
            'VER RESULTADO APRENDIZAJE',
            'CREAR RESULTADO APRENDIZAJE',
            'EDITAR RESULTADO APRENDIZAJE',
            'GESTIONAR COMPETENCIAS RAP',
            'CAMBIAR ESTADO RAP',
            // Permisos para competencias
            'VER COMPETENCIA',
            // Permisos de inventario para INSTRUCTOR (solo catálogo y carrito, NO lista de productos ni configuraciones)
            'VER CATALOGO PRODUCTO',
            'BUSCAR PRODUCTO',
            'VER CARRITO',
            'AGREGAR CARRITO',
            'ACTUALIZAR CARRITO',
            'ELIMINAR CARRITO',
            'VACIAR CARRITO',
            'CREAR ORDEN',
            'DEVOLVER PRESTAMO',
            'VER NOTIFICACION',
        ]);

        // Asignar permisos al rol ASPIRANTE
        // Los aspirantes NO deben tener acceso al módulo de personas
        // Solo pueden ver su propio perfil a través de /mi-perfil
        $aspirante->syncPermissions([
            'VER PROGRAMA COMPLEMENTARIO',
            'VER PERFIL', // Solo pueden ver su propio perfil
            'EDITAR PERSONA',
        ]);

        // Asignar permisos al rol VISITANTE
        $visitante->givePermissionTo([
            'VER PERFIL',
            'EDITAR PERSONA',
        ]);
        // Asignar permisos al rol APRENDIZ
        // Los aprendices tienen acceso limitado solo a consultar su propia información
        $aprendiz->syncPermissions([
            // Permisos de inventario para APRENDIZ (solo catálogo y carrito, NO lista de productos ni configuraciones)
            // 'VER CATALOGO PRODUCTO',
            // 'BUSCAR PRODUCTO',
            // 'VER CARRITO',
            // 'AGREGAR CARRITO',
            // 'ACTUALIZAR CARRITO',
            // 'ELIMINAR CARRITO',
            // 'VACIAR CARRITO',
            // 'CREAR ORDEN',
            // 'DEVOLVER PRESTAMO',
            // 'VER NOTIFICACION',
            'VER TALENTO HUMANO',
            'VER PERFIL',
            'EDITAR PERSONA',
        ]);
    }
}

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
            // Permisos para fichas de caracterización
            'CREAR FICHA DE CARACTERIZACION',
            'EDITAR FICHA DE CARACTERIZACION',
            'VER FICHA DE CARACTERIZACION',
            'ELIMINAR FICHA DE CARACTERIZACION',
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
            // Permisos para aspirantes complementarios
            'VER ESTADISTICAS',
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
            'ASIGNAR PERMISOS',
            'VER PROGRAMA DE CARACTERIZACION',
            'CREAR PROGRAMA DE CARACTERIZACION',
            'EDITAR PROGRAMA DE CARACTERIZACION',
            'ELIMINAR PROGRAMA DE CARACTERIZACION',
            'CREAR PERSONA',
            'VER PERSONA',
            'EDITAR PERSONA',
            'ELIMINAR PERSONA',
            'CAMBIAR ESTADO PERSONA',

            // Permisos de inventario para SUPER ADMINISTRADOR
            'CREAR PRODUCTO',
            'VER PRODUCTO',
            'EDITAR PRODUCTO',
            'ELIMINAR PRODUCTO',
            'VER CATALOGO PRODUCTO',
            'BUSCAR PRODUCTO',
            'VER CARRITO',
            'AGREGAR CARRITO',
            'ACTUALIZAR CARRITO',
            'ELIMINAR CARRITO',
            'VACIAR CARRITO',
            'CREAR CATEGORIA',
            'VER CATEGORIA',
            'EDITAR CATEGORIA',
            'ELIMINAR CATEGORIA',
            'CREAR MARCA',
            'VER MARCA',
            'EDITAR MARCA',
            'ELIMINAR MARCA',
            'CREAR CONTRATO',
            'VER CONTRATO',
            'EDITAR CONTRATO',
            'ELIMINAR CONTRATO',
            'CREAR PROVEEDOR',
            'VER PROVEEDOR',
            'EDITAR PROVEEDOR',
            'ELIMINAR PROVEEDOR',
            'VER ORDEN',
            'CREAR ORDEN',
            'EDITAR ORDEN',
            'ELIMINAR ORDEN',
            'APROBAR ORDEN',
            'COMPLETAR ORDEN',
            'VER PRESTAMO',
            'CREAR PRESTAMO',
            'EDITAR PRESTAMO',
            'DEVOLVER PRESTAMO',
            'APROBAR PRESTAMO',
            'VER ENTRADA',
            'CREAR ENTRADA',
            'VER SALIDA',
            'CREAR SALIDA',
            'VER DEVOLUCION',
            'CREAR DEVOLUCION',
            'PROCESAR DEVOLUCION',
            'VER NOTIFICACION',
            'VER DASHBOARD INVENTARIO',
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
            'VER ESTADISTICAS',
        ]);

        // Asignar permisos al rol ADMINISTRADOR
        $administrador->givePermissionTo([
            'CREAR FICHA DE CARACTERIZACION',
            'EDITAR FICHA DE CARACTERIZACION',
            'VER FICHA DE CARACTERIZACION',
            'ELIMINAR FICHA DE CARACTERIZACION',
            'CREAR INSTRUCTOR',
            'VER INSTRUCTOR',
            'EDITAR INSTRUCTOR',
            'ELIMINAR INSTRUCTOR',
            'CREAR PERSONA',
            'VER PERSONA',
            'EDITAR PERSONA',
            'CAMBIAR ESTADO PERSONA',
            'VER ESTADISTICAS',
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
            // Permisos para resultados de aprendizaje
            'VER RESULTADO APRENDIZAJE',
            'CREAR RESULTADO APRENDIZAJE',
            'EDITAR RESULTADO APRENDIZAJE',
            'GESTIONAR COMPETENCIAS RAP',
            'CAMBIAR ESTADO RAP',
            // Permisos para competencias
            'VER COMPETENCIA',
            // Permisos de inventario para INSTRUCTOR (limitados)
            'VER PRODUCTO',
            'VER CATALOGO PRODUCTO',
            'BUSCAR PRODUCTO',
            'VER CARRITO',
            'AGREGAR CARRITO',
            'ACTUALIZAR CARRITO',
            'ELIMINAR CARRITO',
            'VACIAR CARRITO',
            'VER CATEGORIA',
            'VER MARCA',
            'VER ORDEN',
            'CREAR ORDEN',
            'VER PRESTAMO',
            'CREAR PRESTAMO',
            'DEVOLVER PRESTAMO',
            'VER SALIDA',
            'CREAR SALIDA',
            'VER DEVOLUCION',
            'CREAR DEVOLUCION',
            'VER NOTIFICACION',
        ]);

        // Asignar permisos al rol ASPIRANTE
        $aspirante->givePermissionTo([
            'VER PERSONA',
        ]);

        // Asignar permisos al rol VISITANTE
        $visitante->givePermissionTo([
            'VER PERSONA',
        ]);
    }
}

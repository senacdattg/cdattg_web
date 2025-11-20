<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    // Constantes para permisos comunes
    private const PERMISO_VER_PERFIL = 'VER PERFIL';
    private const PERMISO_EDITAR_PERSONA = 'EDITAR PERSONA';
    private const PERMISO_VER_NOTIFICACION = 'VER NOTIFICACION';
    private const PERMISO_VER_PROGRAMA_COMPLEMENTARIO = 'VER PROGRAMA COMPLEMENTARIO';

    /**
     * Ejecuta el seeder
     */
    public function run(): void
    {
        $roles = $this->crearRoles();
        $this->crearPermisos();
        $this->asignarPermisosARoles($roles);
    }

    /**
     * Crea todos los roles del sistema
     */
    private function crearRoles(): array
    {
        return [
            'bot' => Role::firstOrCreate(['name' => 'BOT']),
            'super_admin' => Role::firstOrCreate(['name' => 'SUPER ADMINISTRADOR']),
            'admin' => Role::firstOrCreate(['name' => 'ADMINISTRADOR']),
            'vigilante' => Role::firstOrCreate(['name' => 'VIGILANTE']),
            'coordinador' => Role::firstOrCreate(['name' => 'COORDINADOR']),
            'instructor' => Role::firstOrCreate(['name' => 'INSTRUCTOR']),
            'visitante' => Role::firstOrCreate(['name' => 'VISITANTE']),
            'aprendiz' => Role::firstOrCreate(['name' => 'APRENDIZ']),
            'aspirante' => Role::firstOrCreate(['name' => 'ASPIRANTE']),
        ];
    }

    /**
     * Crea todos los permisos del sistema
     */
    private function crearPermisos(): void
    {
        $permisos = array_merge(
            $this->getPermisosParametros(),
            $this->getPermisosInfraestructura(),
            $this->getPermisosInstructores(),
            $this->getPermisosFichas(),
            $this->getPermisosPersonas(),
            $this->getPermisosInventario(),
            $this->getPermisosRedesConocimiento(),
            $this->getPermisosAprendices(),
            $this->getPermisosProgramas(),
            $this->getPermisosResultadosAprendizaje(),
            $this->getPermisosCompetencias(),
            $this->getPermisosComplementarios(),
            $this->getPermisosControlSeguimiento(),
            $this->getPermisosGenerales()
        );

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }
    }

    /**
     * Asigna permisos a cada rol según sus responsabilidades
     */
    private function asignarPermisosARoles(array $roles): void
    {
        // SUPER ADMINISTRADOR tiene todos los permisos
        $roles['super_admin']->syncPermissions(Permission::all());

        // ADMINISTRADOR
        $roles['admin']->syncPermissions($this->getPermisosAdministrador());

        // INSTRUCTOR
        $roles['instructor']->syncPermissions($this->getPermisosInstructor());

        // ASPIRANTE
        $roles['aspirante']->syncPermissions($this->getPermisosAspirante());

        // VISITANTE
        $roles['visitante']->syncPermissions($this->getPermisosVisitante());

        // APRENDIZ
        $roles['aprendiz']->syncPermissions($this->getPermisosAprendiz());
    }

    // ==========================================
    // DEFINICIÓN DE PERMISOS POR MÓDULO
    // ==========================================

    /**
     * Permisos del módulo de Parámetros y Temas
     */
    private function getPermisosParametros(): array
    {
        return [
            'CREAR PARAMETRO',
            'EDITAR PARAMETRO',
            'VER PARAMETRO',
            'ELIMINAR PARAMETRO',
            'CREAR TEMA',
            'EDITAR TEMA',
            'VER TEMA',
            'ELIMINAR TEMA',
            'ELIMINAR PARAMETRO DE TEMA',
        ];
    }

    /**
     * Permisos del módulo de Infraestructura
     */
    private function getPermisosInfraestructura(): array
    {
        return [
            'CREAR REGIONAL',
            'EDITAR REGIONAL',
            'VER REGIONAL',
            'VER REGIONALES',
            'ELIMINAR REGIONAL',
            'CAMBIAR ESTADO REGIONAL',

            'CREAR MUNICIPIO',
            'EDITAR MUNICIPIO',
            'VER MUNICIPIO',
            'VER MUNICIPIOS',
            'ELIMINAR MUNICIPIO',
            'CAMBIAR ESTADO MUNICIPIO',

            'CREAR CENTRO DE FORMACION',
            'EDITAR CENTRO DE FORMACION',
            'VER CENTRO DE FORMACION',
            'VER CENTROS DE FORMACION',
            'ELIMINAR CENTRO DE FORMACION',
            'CAMBIAR ESTADO CENTRO DE FORMACION',

            'CREAR SEDE',
            'VER SEDE',
            'VER SEDES',
            'EDITAR SEDE',
            'ELIMINAR SEDE',
            'CAMBIAR ESTADO SEDE',

            'CREAR BLOQUE',
            'VER BLOQUE',
            'VER BLOQUES',
            'EDITAR BLOQUE',
            'ELIMINAR BLOQUE',
            'CAMBIAR ESTADO BLOQUE',

            'CREAR PISO',
            'VER PISO',
            'VER PISOS',
            'EDITAR PISO',
            'ELIMINAR PISO',
            'CAMBIAR ESTADO PISO',

            'CREAR AMBIENTE',
            'VER AMBIENTE',
            'VER AMBIENTES',
            'EDITAR AMBIENTE',
            'ELIMINAR AMBIENTE',
            'CAMBIAR ESTADO AMBIENTE',
        ];
    }

    /**
     * Permisos del módulo de Instructores
     */
    private function getPermisosInstructores(): array
    {
        return [
            'CREAR INSTRUCTOR',
            'VER INSTRUCTOR',
            'VER INSTRUCTORES',
            'EDITAR INSTRUCTOR',
            'ELIMINAR INSTRUCTOR',
            'CAMBIAR ESTADO INSTRUCTOR',

            'GESTIONAR ESPECIALIDADES INSTRUCTOR',
            'ASIGNACION DE INSTRUCTORES',
        ];
    }

    /**
     * Permisos del módulo de Fichas de Caracterización
     */
    private function getPermisosFichas(): array
    {
        return [
            'CREAR FICHA DE CARACTERIZACION',
            'EDITAR FICHA DE CARACTERIZACION',
            'VER FICHA DE CARACTERIZACION',
            'ELIMINAR FICHA DE CARACTERIZACION',
            'CAMBIAR ESTADO FICHA DE CARACTERIZACION',

            'CREAR FICHA CARACTERIZACION',
            'EDITAR FICHA CARACTERIZACION',
            'VER FICHA CARACTERIZACION',
            'ELIMINAR FICHA CARACTERIZACION',

            'GESTIONAR INSTRUCTORES FICHA',
            'GESTIONAR INSTRUCTORES',

            'VER FICHAS ASIGNADAS',
            'GESTIONAR DIAS FICHA',
            'GESTIONAR APRENDICES FICHA',
            'GESTIONAR APRENDICES',
            'CAMBIAR ESTADO FICHA'
        ];
    }

    /**
     * Permisos del módulo de Personas
     */
    private function getPermisosPersonas(): array
    {
        return [
            'CREAR PERSONA',
            'VER PERSONA',
            'VER PERSONAS',
            self::PERMISO_VER_PERFIL,
            self::PERMISO_EDITAR_PERSONA,
            'ELIMINAR PERSONA',

            'CAMBIAR ESTADO PERSONA',
            'RESTABLECER PASSWORD',
        ];
    }

    /**
     * Permisos del módulo de Inventario
     */
    private function getPermisosInventario(): array
    {
        return [
            // Productos
            'CREAR PRODUCTO',
            'VER PRODUCTO',
            'VER PRODUCTOS',
            'EDITAR PRODUCTO',
            'ELIMINAR PRODUCTO',
            'CAMBIAR ESTADO PRODUCTO',
            'BUSCAR PRODUCTO',
            'VER CATALOGO PRODUCTO',

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
            self::PERMISO_VER_NOTIFICACION,
            // Dashboard
            'VER DASHBOARD INVENTARIO',
        ];
    }

    /**
     * Permisos del módulo de Redes de Conocimiento
     */
    private function getPermisosRedesConocimiento(): array
    {
        return [
            'CREAR RED CONOCIMIENTO',
            'EDITAR RED CONOCIMIENTO',
            'VER RED CONOCIMIENTO',
            'VER REDES CONOCIMIENTO',
            'ELIMINAR RED CONOCIMIENTO',
            'CAMBIAR ESTADO RED CONOCIMIENTO',
        ];
    }

    /**
     * Permisos del módulo de Aprendices
     */
    private function getPermisosAprendices(): array
    {
        return [
            'VER APRENDIZ',
            'VER APRENDICES',
            'CREAR APRENDIZ',
            'EDITAR APRENDIZ',
            'ELIMINAR APRENDIZ',
            'CAMBIAR ESTADO APRENDIZ',

        ];
    }

    /**
     * Permisos del módulo de Programas de Formación
     */
    private function getPermisosProgramas(): array
    {
        return [
            'VER PROGRAMA DE FORMACION',
            'VER PROGRAMAS DE FORMACION',
            'CREAR PROGRAMA DE FORMACION',
            'EDITAR PROGRAMA DE FORMACION',
            'ELIMINAR PROGRAMA DE FORMACION',
            'CAMBIAR ESTADO PROGRAMA DE FORMACION',
        ];
    }

    /**
     * Permisos del módulo de Resultados de Aprendizaje
     */
    private function getPermisosResultadosAprendizaje(): array
    {
        return [
            'VER RESULTADO APRENDIZAJE',
            'VER RESULTADOS APRENDIZAJE',
            'CREAR RESULTADO APRENDIZAJE',
            'EDITAR RESULTADO APRENDIZAJE',
            'ELIMINAR RESULTADO APRENDIZAJE',
            'GESTIONAR COMPETENCIAS RESULTADO APRENDIZAJE',
            'CAMBIAR ESTADO RESULTADO APRENDIZAJE',
        ];
    }

    /**
     * Permisos del módulo de Competencias
     */
    private function getPermisosCompetencias(): array
    {
        return [
            'VER COMPETENCIA',
            'VER COMPETENCIAS',
            'CREAR COMPETENCIA',
            'EDITAR COMPETENCIA',
            'ELIMINAR COMPETENCIA',
            'CAMBIAR ESTADO COMPETENCIA',
            'GESTIONAR RESULTADOS COMPETENCIA',
            'ASOCIAR GUIA RAP',
            'DESASOCIAR GUIA RAP',
            'EXPORTAR RAP',
            'IMPORTAR RAP',
            'VER REPORTES RAP',
        ];
    }

    /**
     * Permisos del módulo de Programas Complementarios
     */
    private function getPermisosComplementarios(): array
    {
        return [
            'VER ESTADISTICAS',
            self::PERMISO_VER_PROGRAMA_COMPLEMENTARIO,
            'CREAR PROGRAMA COMPLEMENTARIO',
            'ELIMINAR ASPIRANTE COMPLEMENTARIO',
        ];
    }

    /**
     * Permisos del módulo de Control y Seguimiento
     */
    private function getPermisosControlSeguimiento(): array
    {
        return [
            'VER INGRESO SALIDA',
            'TOMAR ASISTENCIA',
        ];
    }

    /**
     * Permisos generales del sistema
     */
    private function getPermisosGenerales(): array
    {
        return [
            'ASIGNAR PERMISOS',
        ];
    }

    // ==========================================
    // ASIGNACIÓN DE PERMISOS POR ROL
    // ==========================================

    /**
     * Permisos para el rol ADMINISTRADOR
     */
    private function getPermisosAdministrador(): array
    {
        return array_merge(
            $this->getPermisosFichas(),
            $this->getPermisosInstructores(),
            [
                'VER CENTRO DE FORMACION',
                'CREAR PERSONA',
                'VER PERSONA',
                'EDITAR PERSONA',
                'CAMBIAR ESTADO PERSONA',
                'RESTABLECER PASSWORD',
            ],
            $this->getPermisosRedesConocimiento(),
            $this->getPermisosAprendices(),
            [
                'VER PROGRAMAS DE FORMACION',
                'VER PROGRAMA DE FORMACION',
                self::PERMISO_VER_NOTIFICACION,
                'VER ESTADISTICAS',
                self::PERMISO_VER_PROGRAMA_COMPLEMENTARIO,
                'CREAR PROGRAMA COMPLEMENTARIO',
                'ELIMINAR ASPIRANTE COMPLEMENTARIO',
                'VER INGRESO SALIDA',
                'ASIGNACION DE INSTRUCTORES',
            ]
        );
    }

    /**
     * Permisos para el rol INSTRUCTOR
     */
    private function getPermisosInstructor(): array
    {
        return array_merge(
            [
                'TOMAR ASISTENCIA',
                'VER APRENDIZ',
                'VER INSTRUCTOR',
                'VER FICHAS ASIGNADAS',
                'VER FICHA DE CARACTERIZACION',
                'EDITAR FICHA DE CARACTERIZACION',
                'GESTIONAR DIAS FICHA',
                'GESTIONAR APRENDICES FICHA',
                'CAMBIAR ESTADO FICHA',
                'VER PROGRAMAS DE FORMACION',
                'VER PROGRAMA DE FORMACION',
            ],
            $this->getPermisosResultadosAprendizaje(),
            [
                'VER COMPETENCIA',
            ],
            [
                'VER CATALOGO PRODUCTO',
                'BUSCAR PRODUCTO',
                'VER CARRITO',
                'AGREGAR CARRITO',
                'ACTUALIZAR CARRITO',
                'ELIMINAR CARRITO',
                'VACIAR CARRITO',
                'CREAR ORDEN',
                'DEVOLVER PRESTAMO',
                self::PERMISO_VER_NOTIFICACION,
            ]
        );
    }

    /**
     * Permisos para el rol ASPIRANTE
     */
    private function getPermisosAspirante(): array
    {
        return [
            self::PERMISO_VER_PROGRAMA_COMPLEMENTARIO,
            self::PERMISO_VER_PERFIL,
            self::PERMISO_EDITAR_PERSONA,
        ];
    }

    /**
     * Permisos para el rol VISITANTE
     */
    private function getPermisosVisitante(): array
    {
        return [
            self::PERMISO_VER_PERFIL,
            self::PERMISO_EDITAR_PERSONA,
        ];
    }

    /**
     * Permisos para el rol APRENDIZ
     */
    private function getPermisosAprendiz(): array
    {
        return $this->getPermisosVisitante();
    }
}

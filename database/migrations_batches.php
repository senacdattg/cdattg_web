<?php

/**
 * Índice de Batches de Migraciones por Módulos
 * 
 * Este archivo define la organización de las migraciones del proyecto
 * agrupadas por módulos funcionales con sus dependencias.
 * 
 * Uso:
 *   - Migrar un módulo específico: php artisan migrate:module batch_01_sistema_base
 *   - Migrar todos los módulos: php artisan migrate:module --all
 *   - Listar módulos: php artisan migrate:module --list
 */

return [
    'batch_01_sistema_base' => [
        'descripcion' => 'Sistema Base - Usuarios, autenticación y sistema de colas',
        'dependencias' => [],
        'tablas' => [
            'users',
            'password_reset_tokens',
            'personal_access_tokens',
            'failed_jobs',
            'jobs',
            'logins',
        ],
        'orden' => 1,
    ],

    'batch_02_permisos' => [
        'descripcion' => 'Sistema de Permisos y Roles (Spatie Laravel Permission)',
        'dependencias' => ['batch_01_sistema_base'],
        'tablas' => [
            'permissions',
            'roles',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
        ],
        'orden' => 2,
    ],

    'batch_03_parametros' => [
        'descripcion' => 'Parámetros y Configuración del Sistema',
        'dependencias' => ['batch_01_sistema_base'],
        'tablas' => [
            'parametros',
            'temas'
        ],
        'orden' => 3,
    ],

    'batch_04_ubicaciones' => [
        'descripcion' => 'Ubicaciones Geográficas - Países, departamentos, municipios, centros y sedes',
        'dependencias' => ['batch_01_sistema_base'],
        'tablas' => [
            'pais',
            'departamentos',
            'municipios',
            'regionals',
            'centro_formacions',
            'sedes',
        ],
        'orden' => 4,
    ],

    'batch_05_personas' => [
        'descripcion' => 'Personas - Información personal y relación con usuarios',
        'dependencias' => ['batch_01_sistema_base', 'batch_03_ubicaciones'],
        'tablas' => [
            'personas',
        ],
        'orden' => 5,
    ],

    'batch_06_infraestructura' => [
        'descripcion' => 'Infraestructura Física - Bloques, pisos y ambientes',
        'dependencias' => ['batch_03_ubicaciones'],
        'tablas' => [
            'bloques',
            'pisos',
            'ambientes',
        ],
        'orden' => 6,
    ],

    'batch_07_programas' => [
        'descripcion' => 'Programas de Formación y Redes de Conocimiento',
        'dependencias' => ['batch_01_sistema_base'],
        'tablas' => [
            'red_conocimientos',
            'programas_formacion',
        ],
        'orden' => 7,
    ],

    'batch_08_fichas' => [
        'descripcion' => 'Fichas de Caracterización de Programas',
        'dependencias' => ['batch_06_programas', 'batch_07_instructores_aprendices', 'batch_03_ubicaciones'],
        'tablas' => [
            'fichas_caracterizacion',
        ],
        'orden' => 9,
    ],

    'batch_09_instructores_aprendices' => [
        'descripcion' => 'Instructores, Aprendices y Vigilantes',
        'dependencias' => ['batch_04_personas', 'batch_03_ubicaciones'],
        'tablas' => [
            'instructors',
            'aprendices',
            'vigilantes',
        ],
        'orden' => 8,
    ],

    'batch_10_relaciones' => [
        'descripcion' => 'Tablas Pivot - Relaciones entre entidades',
        'dependencias' => ['batch_07_instructores_aprendices', 'batch_08_fichas', 'batch_05_infraestructura'],
        'tablas' => [
            'aprendiz_ficha_caracterizacion',
            'instructor_ficha_caracterizacion',
            'ambiente_ficha',
            'ambiente_instructor_ficha',
        ],
        'orden' => 10,
    ],

    'batch_11_jornadas_horarios' => [
        'descripcion' => 'Jornadas, Horarios y Días de Formación',
        'dependencias' => ['batch_08_fichas', 'batch_09_relaciones'],
        'tablas' => [
            'jornadas_formacion',
            'ficha_caracterizacion_dias_formacion',
            'instructor_ficha_dias',
        ],
        'orden' => 11,
    ],

    'batch_12_asistencias' => [
        'descripcion' => 'Registro de Asistencias y Control de Entrada/Salida',
        'dependencias' => ['batch_09_relaciones', 'batch_10_jornadas_horarios'],
        'tablas' => [
            'asistencia_aprendices',
            'entrada_salidas',
        ],
        'orden' => 12,
    ],

    'batch_13_competencias' => [
        'descripcion' => 'Competencias, Resultados de Aprendizaje y Guías',
        'dependencias' => ['batch_06_programas'],
        'tablas' => [
            'competencias',
            'resultados_aprendizajes',
            'guia_aprendizajes',
            'guia_aprendizaje_rap',
        ],
        'orden' => 13,
    ],

    'batch_14_evidencias' => [
        'descripcion' => 'Evidencias de Aprendizaje',
        'dependencias' => ['batch_12_competencias', 'batch_07_instructores_aprendices'],
        'tablas' => [
            'evidencias',
        ],
        'orden' => 14,
    ],

    'batch_15_logs_auditoria' => [
        'descripcion' => 'Logs y Auditoría del Sistema',
        'dependencias' => ['batch_07_instructores_aprendices', 'batch_08_fichas'],
        'tablas' => [
            'asignacion_instructor_logs',
        ],
        'orden' => 15,
    ],

    'batch_16_inventario' => [
        'descripcion' => 'Módulo de inventario',
        'dependencias' => [
            'batch_01_sistema_base',
            'batch_02_permisos',
            'batch_03_parametros',
            'batch_05_personas',
            'batch_06_infraestructura',
        ],
        'tablas' => [
            'productos',
            'ordenes',
            'proveedores',
            'contratos_convenios',
            'detalle_ordenes',
            'devoluciones',
        ],
        'orden' => 16,
    ],

    'batch_17_complementarios' => [
        'descripcion' => 'Módulo de Complementarios - Cursos complementarios, aspirantes y caracterización',
        'dependencias' => [
            'batch_05_personas',
            'batch_03_parametros',
            'batch_11_jornadas_horarios',
            'batch_06_infraestructura',
        ],
        'tablas' => [
            'complementarios_ofertados',
            'complementarios_ofertados_dias_formacion',
            'aspirantes_complementarios',
            'categorias_caracterizacion_complementarios',
            'persona_caracterizacion',
        ],
        'orden' => 17,
    ],
];

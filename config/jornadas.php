<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración de Jornadas de Formación
    |--------------------------------------------------------------------------
    |
    | Define los horarios de las diferentes jornadas de formación del SENA.
    | Estos horarios son utilizados para validar asistencias y novedades.
    |
    */

    'horarios' => [
        'Mañana' => [
            'inicio' => '06:00:00',
            'fin' => '13:10:00',
            'tolerancia_entrada' => 15, // minutos
            'tolerancia_salida' => 10, // minutos
        ],
        'Tarde' => [
            'inicio' => '13:00:00',
            'fin' => '18:10:00',
            'tolerancia_entrada' => 15,
            'tolerancia_salida' => 10,
        ],
        'Noche' => [
            'inicio' => '17:50:00',
            'fin' => '23:10:00',
            'tolerancia_entrada' => 15,
            'tolerancia_salida' => 10,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de Novedad
    |--------------------------------------------------------------------------
    |
    | Define los tipos de novedades que pueden registrarse en las asistencias.
    |
    */

    'tipos_novedad' => [
        'entrada' => [
            'Puntual',
            'Tarde',
            'Muy tarde',
            'Permiso',
            'Excusa médica',
        ],
        'salida' => [
            'Normal',
            'Anticipada',
            'Permiso',
            'Emergencia',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reglas de Validación
    |--------------------------------------------------------------------------
    |
    | Define las reglas de validación para las asistencias.
    |
    */

    'validacion' => [
        'tiempo_minimo_clase' => 45, // minutos
        'porcentaje_asistencia_minimo' => 80, // porcentaje
        'horas_maximas_instructor_semana' => 48,
    ],

];


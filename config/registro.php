<?php

return [
    'fallback_documentos' => [
        (object) ['id' => 3, 'name' => 'CEDULA DE CIUDADANIA'],
        (object) ['id' => 4, 'name' => 'CEDULA DE EXTRANJERIA'],
        (object) ['id' => 5, 'name' => 'PASAPORTE'],
        (object) ['id' => 6, 'name' => 'TARJETA DE IDENTIDAD'],
        (object) ['id' => 7, 'name' => 'REGISTRO CIVIL'],
        (object) ['id' => 8, 'name' => 'SIN IDENTIFICACION'],
    ],

    'fallback_generos' => [
        (object) ['id' => 9, 'name' => 'MASCULINO'],
        (object) ['id' => 10, 'name' => 'FEMENINO'],
        (object) ['id' => 11, 'name' => 'NO DEFINE'],
    ],

    'audit_default_user_id' => env('AUDIT_DEFAULT_USER_ID'),
];

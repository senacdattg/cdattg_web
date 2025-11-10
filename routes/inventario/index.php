<?php

// Archivo central de rutas para el módulo de inventario.
foreach ([
    'dashboard',
    'notificaciones',
    'carrito',
    'categoria',
    'contratoConvenio',
    'devolucion',
    'marca',
    'ordenes',
    'productos',
    'proveedor',
] as $routeFile) {
    require_once __DIR__ . "/{$routeFile}.php"; // NOSONAR - Standard Laravel practice for route inclusion --- IGNORE ---
}

<?php

if (!function_exists('routes_path')) {
    function routes_path($path = '') {
        return base_path('routes' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
    }
}

if (!function_exists('obtener_numero_evidencia')) {
    function obtener_numero_evidencia($cadenaEvidencia){
        if (preg_match('/^([a-zA-Z]+)-(\d+)$/', $cadenaEvidencia, $matches)){
            return $matches[2];
        } // Retorna null si no coincide el patrón
    }
}

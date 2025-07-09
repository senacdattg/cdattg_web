<?php

// Script de prueba para verificar los endpoints de la API

echo "=== PRUEBA DE ENDPOINTS API ===\n\n";

// URL base
$baseUrl = 'http://localhost:8000/api';

// 1. Probar endpoint de prueba sin autenticación
echo "1. Probando endpoint de prueba sin autenticación...\n";
$testUrl = $baseUrl . '/test';
$response = file_get_contents($testUrl);
echo "Respuesta: " . $response . "\n\n";

// 2. Probar endpoint de fichas sin autenticación
echo "2. Probando endpoint de fichas sin autenticación...\n";
$fichasUrl = $baseUrl . '/fichas-caracterizacion/test';
$response = file_get_contents($fichasUrl);
echo "Respuesta: " . $response . "\n\n";

// 3. Probar endpoint con autenticación (debería fallar sin token)
echo "3. Probando endpoint con autenticación (sin token)...\n";
$fichasAuthUrl = $baseUrl . '/fichas-caracterizacion/all';

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'Accept: application/json',
            'Content-Type: application/json'
        ]
    ]
]);

$response = file_get_contents($fichasAuthUrl, false, $context);
echo "Respuesta: " . $response . "\n\n";

echo "=== FIN DE PRUEBAS ===\n";
?>

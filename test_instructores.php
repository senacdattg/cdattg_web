<?php

require_once 'vendor/autoload.php';

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use App\Services\AsignacionInstructorService;
use App\Services\InstructorBusinessRulesService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DIAGNÓSTICO DE INSTRUCTORES ===\n";

// Obtener primera ficha
$ficha = FichaCaracterizacion::with(['sede.regional', 'programaFormacion.redConocimiento'])->first();

if (!$ficha) {
    echo "No hay fichas de caracterización\n";
    exit;
}

echo "Ficha ID: " . $ficha->id . "\n";
echo "Ficha número: " . $ficha->ficha . "\n";
echo "Sede ID: " . $ficha->sede_id . "\n";

if ($ficha->sede) {
    echo "Sede nombre: " . $ficha->sede->sede . "\n";
    echo "Regional ID: " . ($ficha->sede->regional_id ?? 'NULL') . "\n";
} else {
    echo "Sede no encontrada\n";
}

// Obtener instructores directamente
$instructores = Instructor::with(['persona', 'regional'])
    ->where('status', true)
    ->get();

echo "\n=== INSTRUCTORES EN BD ===\n";
echo "Total instructores activos: " . $instructores->count() . "\n";

foreach ($instructores as $instructor) {
    echo "ID: " . $instructor->id . 
         ", Nombre: " . ($instructor->persona->primer_nombre ?? 'Sin nombre') . 
         ", Regional: " . ($instructor->regional_id ?? 'NULL') . 
         ", Status: " . ($instructor->status ? 'Activo' : 'Inactivo') . "\n";
}

// Probar el servicio
echo "\n=== PROBANDO SERVICIO ===\n";
try {
    $service = new AsignacionInstructorService(new InstructorBusinessRulesService());
    $disponibles = $service->obtenerInstructoresDisponibles($ficha->id);
    
    echo "Instructores disponibles retornados: " . count($disponibles) . "\n";
    
    foreach ($disponibles as $index => $data) {
        $instructor = $data['instructor'];
        echo "  " . ($index + 1) . ". ID: " . $instructor->id . 
             ", Nombre: " . ($instructor->persona->primer_nombre ?? 'Sin nombre') . 
             ", Disponible: " . ($data['disponible'] ? 'SÍ' : 'NO') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error en servicio: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DIAGNÓSTICO ===\n";

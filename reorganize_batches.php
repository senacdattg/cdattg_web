<?php

/**
 * Script para reorganizar los batches con el orden correcto de dependencias
 * Uso: php reorganize_batches.php
 */

$migrationsPath = __DIR__ . '/database/migrations';

// Orden correcto basado en dependencias
$correctOrder = [
    'batch_01_sistema_base' => 'batch_01_sistema_base',
    'batch_02_permisos' => 'batch_02_permisos',
    'batch_15_parametros' => 'batch_03_parametros',           // ERA 15 → AHORA 03
    'batch_03_ubicaciones' => 'batch_04_ubicaciones',         // ERA 03 → AHORA 04
    'batch_04_personas' => 'batch_05_personas',               // ERA 04 → AHORA 05
    'batch_05_infraestructura' => 'batch_06_infraestructura', // ERA 05 → AHORA 06
    'batch_06_programas' => 'batch_07_programas',             // ERA 06 → AHORA 07
    'batch_08_fichas' => 'batch_08_fichas',                   // QUEDA IGUAL (ERA 09 ORIGINALMENTE)
    'batch_07_instructores_aprendices' => 'batch_09_instructores_aprendices', // ERA 07 → AHORA 09
    'batch_09_relaciones' => 'batch_10_relaciones',           // ERA 09 → AHORA 10
    'batch_10_jornadas_horarios' => 'batch_11_jornadas_horarios', // ERA 10 → AHORA 11
    'batch_11_asistencias' => 'batch_12_asistencias',         // ERA 11 → AHORA 12
    'batch_12_competencias' => 'batch_13_competencias',       // ERA 12 → AHORA 13
    'batch_13_evidencias' => 'batch_14_evidencias',           // ERA 13 → AHORA 14
    'batch_14_logs_auditoria' => 'batch_15_logs_auditoria',   // ERA 14 → AHORA 15
];

echo "=== Reorganizando Batches con Orden Correcto ===\n\n";
echo "🔍 Análisis de dependencias:\n";
echo "   • parametros (15) → 03 (personas lo necesita)\n";
echo "   • fichas (09) → 08 (instructores_aprendices lo necesita)\n";
echo "   • instructores_aprendices (07) → 09 (depende de fichas)\n\n";

// Paso 1: Renombrar a temporales
echo "📦 Paso 1: Renombrando a nombres temporales...\n";
foreach ($correctOrder as $oldName => $newName) {
    if ($oldName !== $newName) {
        $oldPath = $migrationsPath . '/' . $oldName;
        $tempPath = $migrationsPath . '/temp_' . $oldName;
        
        if (is_dir($oldPath)) {
            rename($oldPath, $tempPath);
            echo "  ✓ {$oldName} → temp_{$oldName}\n";
        } else {
            echo "  ⚠ No encontrado: {$oldName}\n";
        }
    }
}

echo "\n📦 Paso 2: Renombrando a nombres finales...\n";
// Paso 2: Renombrar temporales a finales
foreach ($correctOrder as $oldName => $newName) {
    if ($oldName !== $newName) {
        $tempPath = $migrationsPath . '/temp_' . $oldName;
        $finalPath = $migrationsPath . '/' . $newName;
        
        if (is_dir($tempPath)) {
            rename($tempPath, $finalPath);
            echo "  ✓ temp_{$oldName} → {$newName}\n";
        }
    } else {
        echo "  - {$oldName} (sin cambios)\n";
    }
}

echo "\n=== ✅ Reorganización Completada ===\n\n";

echo "📋 Nuevo orden:\n";
$i = 1;
foreach ($correctOrder as $oldName => $newName) {
    $arrow = ($oldName !== $newName) ? " (cambió de {$oldName})" : "";
    echo "  {$i}. {$newName}{$arrow}\n";
    $i++;
}

echo "\n💡 Ahora ejecuta:\n";
echo "   php artisan migrate:module --all --fresh\n\n";


<?php

/**
 * Script para reorganizar migraciones de Laravel en batches por módulos
 * Uso: php reorganize_migrations.php
 */

$migrationsPath = __DIR__ . '/database/migrations';
$timestamp = '2025_10_27_';

// Definir la estructura de módulos con sus migraciones
$batches = [
    'batch_01_sistema_base' => [
        ['file' => '2014_10_12_000000_create_users_table.php', 'new_timestamp' => '000001'],
        ['file' => '2014_10_12_100000_create_password_reset_tokens_table.php', 'new_timestamp' => '000002'],
        ['file' => '2019_12_14_000001_create_personal_access_tokens_table.php', 'new_timestamp' => '000003'],
        ['file' => '2019_08_19_000000_create_failed_jobs_table.php', 'new_timestamp' => '000004'],
        ['file' => '2025_09_29_001548_create_jobs_table.php', 'new_timestamp' => '000005'],
        ['file' => '2024_02_21_224928_create_logins_table.php', 'new_timestamp' => '000006'],
    ],
    
    'batch_02_permisos' => [
        ['file' => '2024_03_13_213211_create_permission_tables.php', 'new_timestamp' => '000010'],
    ],
    
    'batch_03_ubicaciones' => [
        ['file' => '2024_03_14_201218_create_pais_table.php', 'new_timestamp' => '000020'],
        ['file' => '2024_03_14_202152_create_departamentos_table.php', 'new_timestamp' => '000021'],
        ['file' => '2024_03_14_203305_create_municipios_table.php', 'new_timestamp' => '000022'],
        ['file' => '2024_05_23_165051_create_regionals_table.php', 'new_timestamp' => '000023'],
        ['file' => '2025_06_15_065507_add_column_departamento_to_regionals_table.php', 'new_timestamp' => '000024'],
        ['file' => '2025_03_19_114325_create_centro_formacions_table.php', 'new_timestamp' => '000025'],
        ['file' => '2024_02_29_145150_create_sedes_table.php', 'new_timestamp' => '000026'],
        ['file' => '2024_03_18_151803_add_municipioid_to_sedes_table.php', 'new_timestamp' => '000027'],
        ['file' => '2024_05_23_170002_add_regional_to_sedes.php', 'new_timestamp' => '000028'],
    ],
    
    'batch_04_personas' => [
        ['file' => '2024_02_29_142754_create_personas_table.php', 'new_timestamp' => '000030'],
        ['file' => '2024_03_19_221526_add_column_tipodocumento_genero_to_personas.php', 'new_timestamp' => '000031'],
        ['file' => '2024_03_20_000000_add_location_fields_to_personas_table.php', 'new_timestamp' => '000032'],
        ['file' => '2024_02_29_143847_add_persona_id_to_users_table.php', 'new_timestamp' => '000033'],
    ],
    
    'batch_05_infraestructura' => [
        ['file' => '2024_03_01_161746_create_bloques_table.php', 'new_timestamp' => '000040'],
        ['file' => '2024_03_01_164313_create_pisos_table.php', 'new_timestamp' => '000041'],
        ['file' => '2024_03_01_165021_create_ambientes_table.php', 'new_timestamp' => '000042'],
        ['file' => '2024_03_04_152231_add_columns_to_ambientes_table.php', 'new_timestamp' => '000043'],
    ],
    
    'batch_06_programas' => [
        ['file' => '2025_06_12_185118_create_red_conocimientos_table.php', 'new_timestamp' => '000050'],
        ['file' => '2025_09_30_165657_add_audit_fields_to_red_conocimientos_table.php', 'new_timestamp' => '000051'],
        ['file' => '2024_02_29_144616_create_tipos_programas_table.php', 'new_timestamp' => '000052'],
        ['file' => '2024_02_29_145153_create_programas_formacion_table.php', 'new_timestamp' => '000053'],
        ['file' => '2025_06_12_192227_update_column_tipo_programa_of_table_programas_formacion.php', 'new_timestamp' => '000054'],
        ['file' => '2025_06_12_192843_drop_tipos_programas_table.php', 'new_timestamp' => '000055'],
        ['file' => '2025_06_12_194935_add_column_nivel_to_programa_formacion.php', 'new_timestamp' => '000056'],
        ['file' => '2025_10_02_174116_add_audit_fields_to_programas_formacion_table.php', 'new_timestamp' => '000057'],
    ],
    
    'batch_07_instructores_aprendices' => [
        ['file' => '2024_03_19_151920_create_instructors_table.php', 'new_timestamp' => '000060'],
        ['file' => '2024_05_23_170617_add_regional_to_instructors.php', 'new_timestamp' => '000061'],
        ['file' => '2025_10_05_111230_add_missing_fields_to_instructors_table.php', 'new_timestamp' => '000062'],
        ['file' => '2025_06_14_164133_create_aprendices_table.php', 'new_timestamp' => '000063'],
        ['file' => '2025_09_30_182445_add_ficha_and_estado_to_aprendices_table.php', 'new_timestamp' => '000064'],
        ['file' => '2025_06_14_173037_create_vigilantes_table.php', 'new_timestamp' => '000065'],
    ],
    
    'batch_08_fichas' => [
        ['file' => '2024_03_20_091224_create_fichas_caracterizacion_table.php', 'new_timestamp' => '000070'],
        ['file' => '2024_05_28_150148_añadir_user_create_edit_a_ficha_caracterizacions.php', 'new_timestamp' => '000071'],
        ['file' => '2024_05_28_152652_añadir_regional_ficha_caracterizacions.php', 'new_timestamp' => '000072'],
        ['file' => '2025_06_14_105356_add_column_fecha_inicio_fin_to_ficha_caracterizacion.php', 'new_timestamp' => '000073'],
        ['file' => '2025_10_03_085539_add_missing_fields_to_fichas_caracterizacion_table.php', 'new_timestamp' => '000074'],
        ['file' => '2024_09_12_091351_create_caracterizacion_programas_table.php', 'new_timestamp' => '000075'],
        ['file' => '2025_06_15_000043_drop_caracterizacion_programas_table.php', 'new_timestamp' => '000076'],
    ],
    
    'batch_09_relaciones' => [
        ['file' => '2025_06_14_164738_create_aprendiz_ficha_caracterizacion_table.php', 'new_timestamp' => '000080'],
        ['file' => '2025_06_14_165134_create_instructor_ficha_caracterizacion_table.php', 'new_timestamp' => '000081'],
        ['file' => '2025_06_14_180217_create_ambiente_ficha_table.php', 'new_timestamp' => '000082'],
        ['file' => '2025_06_14_175227_create_ambiente_instructor_ficha_table.php', 'new_timestamp' => '000083'],
    ],
    
    'batch_10_jornadas_horarios' => [
        ['file' => '2024_09_11_092711_create_jornadas_formacion_table.php', 'new_timestamp' => '000090'],
        ['file' => '2025_06_14_231756_remove_columns_from_jornadas_formacion_table.php', 'new_timestamp' => '000091'],
        ['file' => '2025_06_14_105009_create_ficha_caracterizacion_dias_formacion_table.php', 'new_timestamp' => '000092'],
        ['file' => '2025_06_14_132002_remove_dias_formacion_from_fichas_caracterizacion.php', 'new_timestamp' => '000093'],
        ['file' => '2025_06_14_170542_create_instructor_ficha_dias_table.php', 'new_timestamp' => '000094'],
    ],
    
    'batch_11_asistencias' => [
        ['file' => '2024_09_18_154335_create_asistencia_aprendices_table.php', 'new_timestamp' => '000100'],
        ['file' => '2025_06_14_172054_remove_caracterizacion_from_asistencia_aprendices_table.php', 'new_timestamp' => '000101'],
        ['file' => '2025_06_14_172251_add_instructor_ficha_to_asistencia_aprendices_table.php', 'new_timestamp' => '000102'],
        ['file' => '2024_02_29_210346_create_entrada_salidas_table.php', 'new_timestamp' => '000103'],
        ['file' => '2024_03_21_154757_add_column_to_entrada_salidas.php', 'new_timestamp' => '000104'],
        ['file' => '2024_04_03_162543_add_column_to_table_entrada_salidas.php', 'new_timestamp' => '000105'],
        ['file' => '2024_07_03_172007_add_column_to_entrada_salidas.php', 'new_timestamp' => '000106'],
    ],
    
    'batch_12_competencias' => [
        ['file' => '2025_06_17_233018_create_competencias_table.php', 'new_timestamp' => '000110'],
        ['file' => '2025_10_11_000000_add_missing_fields_to_competencias_table.php', 'new_timestamp' => '000111'],
        ['file' => '2025_06_17_234950_create_resultados_aprendizajes_table.php', 'new_timestamp' => '000112'],
        ['file' => '2025_10_07_140000_add_status_and_indexes_to_resultados_aprendizajes_table.php', 'new_timestamp' => '000113'],
        ['file' => '2025_06_17_235644_create_guia_aprendizajes_table.php', 'new_timestamp' => '000114'],
        ['file' => '2025_06_17_235645_add_missing_fields_to_guia_aprendizajes_table.php', 'new_timestamp' => '000115'],
        ['file' => '2025_06_17_235646_add_es_obligatorio_to_guia_aprendizaje_rap_table.php', 'new_timestamp' => '000116'],
    ],
    
    'batch_13_evidencias' => [
        ['file' => '2025_06_18_000009_create_evidencias_table.php', 'new_timestamp' => '000120'],
        ['file' => '2025_07_17_175044_add_column_id_evidencia.php', 'new_timestamp' => '000121'],
        ['file' => '2025_07_25_091949_add_column_estado_fecha_evidencia.php', 'new_timestamp' => '000122'],
    ],
    
    'batch_14_logs_auditoria' => [
        ['file' => '2025_10_05_211515_create_asignacion_instructor_logs_table.php', 'new_timestamp' => '000130'],
        ['file' => '2025_10_12_120000_modify_instructor_id_nullable_in_asignacion_instructor_logs_table.php', 'new_timestamp' => '000131'],
    ],
    
    'batch_15_parametros' => [
        ['file' => '2024_02_23_145952_create_parametros_table.php', 'new_timestamp' => '000140'],
        ['file' => '2024_03_19_195110_create_temas_table.php', 'new_timestamp' => '000141'],
    ],
];

echo "=== Reorganizando migraciones por módulos ===\n\n";

$stats = [
    'moved' => 0,
    'skipped' => 0,
    'errors' => 0,
];

foreach ($batches as $batchName => $migrations) {
    $batchPath = $migrationsPath . '/' . $batchName;
    
    // Crear directorio del batch si no existe
    if (!is_dir($batchPath)) {
        mkdir($batchPath, 0755, true);
        echo "✓ Creado directorio: $batchName\n";
    }
    
    foreach ($migrations as $migration) {
        $oldFile = $migrationsPath . '/' . $migration['file'];
        $newFileName = $timestamp . $migration['new_timestamp'] . '_' . 
                      substr($migration['file'], strpos($migration['file'], '_', 14) + 1);
        $newFile = $batchPath . '/' . $newFileName;
        
        if (file_exists($oldFile)) {
            if (copy($oldFile, $newFile)) {
                echo "  → {$migration['file']} → {$batchName}/{$newFileName}\n";
                $stats['moved']++;
            } else {
                echo "  ✗ Error copiando: {$migration['file']}\n";
                $stats['errors']++;
            }
        } else {
            echo "  ! No encontrado: {$migration['file']}\n";
            $stats['skipped']++;
        }
    }
    
    echo "\n";
}

echo "\n=== Resumen ===\n";
echo "Migraciones movidas: {$stats['moved']}\n";
echo "No encontradas: {$stats['skipped']}\n";
echo "Errores: {$stats['errors']}\n";
echo "\n";

if ($stats['moved'] > 0) {
    echo "✓ Reorganización completada exitosamente.\n";
    echo "\nPara ejecutar las migraciones por módulo:\n";
    echo "  php artisan migrate --path=database/migrations/batch_01_sistema_base\n";
    echo "  php artisan migrate --path=database/migrations/batch_02_permisos\n";
    echo "  ... etc\n\n";
    echo "Para ejecutar todas en orden:\n";
    echo "  php artisan migrate:fresh\n\n";
}


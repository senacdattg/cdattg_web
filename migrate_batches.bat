@echo off
echo ==================================================
echo Ejecutando Migraciones Organizadas por Batches
echo ==================================================

echo.
echo [BATCH 1] Sistema Base (Laravel Core)
php artisan migrate --path=database/migrations/2014_10_12_000000_create_users_table.php
php artisan migrate --path=database/migrations/2014_10_12_100000_create_password_reset_tokens_table.php
php artisan migrate --path=database/migrations/2019_08_19_000000_create_failed_jobs_table.php
php artisan migrate --path=database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php
php artisan migrate --path=database/migrations/2025_09_29_001548_create_jobs_table.php

echo.
echo [BATCH 2] Configuracion y Parametros
php artisan migrate --path=database/migrations/2024_02_21_224928_create_logins_table.php
php artisan migrate --path=database/migrations/2024_02_23_145952_create_parametros_table.php
php artisan migrate --path=database/migrations/2024_03_13_213211_create_permission_tables.php

echo.
echo [BATCH 3] Ubicaciones Geograficas
php artisan migrate --path=database/migrations/2024_03_14_201218_create_pais_table.php
php artisan migrate --path=database/migrations/2024_03_14_202152_create_departamentos_table.php
php artisan migrate --path=database/migrations/2024_03_14_203305_create_municipios_table.php
php artisan migrate --path=database/migrations/2024_05_23_165051_create_regionals_table.php
php artisan migrate --path=database/migrations/2025_06_15_065507_add_column_departamento_to_regionals_table.php

echo.
echo [BATCH 4] Personas y Usuarios
php artisan migrate --path=database/migrations/2024_02_29_142754_create_personas_table.php
php artisan migrate --path=database/migrations/2024_02_29_143847_add_persona_id_to_users_table.php
php artisan migrate --path=database/migrations/2024_03_19_221526_add_column_tipodocumento_genero_to_personas.php
php artisan migrate --path=database/migrations/2024_03_20_000000_add_location_fields_to_personas_table.php

echo.
echo [BATCH 5] Infraestructura Fisica
php artisan migrate --path=database/migrations/2024_02_29_145150_create_sedes_table.php
php artisan migrate --path=database/migrations/2024_03_18_151803_add_municipioid_to_sedes_table.php
php artisan migrate --path=database/migrations/2024_05_23_170002_add_regional_to_sedes.php
php artisan migrate --path=database/migrations/2024_03_01_161746_create_bloques_table.php
php artisan migrate --path=database/migrations/2024_03_01_164313_create_pisos_table.php
php artisan migrate --path=database/migrations/2024_03_01_165021_create_ambientes_table.php
php artisan migrate --path=database/migrations/2024_03_04_152231_add_columns_to_ambientes_table.php
php artisan migrate --path=database/migrations/2025_03_19_114325_create_centro_formacions_table.php

echo.
echo [BATCH 6] Programas y Red de Conocimiento
php artisan migrate --path=database/migrations/2025_06_12_185118_create_red_conocimientos_table.php
php artisan migrate --path=database/migrations/2025_09_30_165657_add_audit_fields_to_red_conocimientos_table.php
php artisan migrate --path=database/migrations/2024_02_29_144616_create_tipos_programas_table.php
php artisan migrate --path=database/migrations/2024_02_29_145153_create_programas_formacion_table.php
php artisan migrate --path=database/migrations/2025_06_12_192227_update_column_tipo_programa_of_table_programas_formacion.php
php artisan migrate --path=database/migrations/2025_06_12_192843_drop_tipos_programas_table.php
php artisan migrate --path=database/migrations/2025_06_12_194935_add_column_nivel_to_programa_formacion.php
php artisan migrate --path=database/migrations/2025_10_02_174116_add_audit_fields_to_programas_formacion_table.php

echo.
echo [BATCH 7] Instructores, Aprendices y Vigilantes
php artisan migrate --path=database/migrations/2024_03_19_151920_create_instructors_table.php
php artisan migrate --path=database/migrations/2024_05_23_170617_add_regional_to_instructors.php
php artisan migrate --path=database/migrations/2025_10_05_111230_add_missing_fields_to_instructors_table.php
php artisan migrate --path=database/migrations/2025_06_14_164133_create_aprendices_table.php
php artisan migrate --path=database/migrations/2025_09_30_182445_add_ficha_and_estado_to_aprendices_table.php
php artisan migrate --path=database/migrations/2025_06_14_173037_create_vigilantes_table.php
php artisan migrate --path=database/migrations/2025_10_05_211515_create_asignacion_instructor_logs_table.php
php artisan migrate --path=database/migrations/2025_10_12_120000_modify_instructor_id_nullable_in_asignacion_instructor_logs_table.php

echo.
echo [BATCH 8] Fichas de Caracterizacion
php artisan migrate --path=database/migrations/2024_03_20_091224_create_fichas_caracterizacion_table.php
php artisan migrate --path=database/migrations/2024_05_28_150148_añadir_user_create_edit_a_ficha_caracterizacions.php
php artisan migrate --path=database/migrations/2024_05_28_152652_añadir_regional_ficha_caracterizacions.php
php artisan migrate --path=database/migrations/2025_06_14_105009_create_ficha_caracterizacion_dias_formacion_table.php
php artisan migrate --path=database/migrations/2025_06_14_105356_add_column_fecha_inicio_fin_to_ficha_caracterizacion.php
php artisan migrate --path=database/migrations/2025_06_14_132002_remove_dias_formacion_from_fichas_caracterizacion.php
php artisan migrate --path=database/migrations/2025_10_03_085539_add_missing_fields_to_fichas_caracterizacion_table.php
php artisan migrate --path=database/migrations/2025_06_14_164738_create_aprendiz_ficha_caracterizacion_table.php
php artisan migrate --path=database/migrations/2025_06_14_165134_create_instructor_ficha_caracterizacion_table.php
php artisan migrate --path=database/migrations/2025_06_14_170542_create_instructor_ficha_dias_table.php

echo.
echo [BATCH 9] Jornadas y Ambientes
php artisan migrate --path=database/migrations/2024_09_11_092711_create_jornadas_formacion_table.php
php artisan migrate --path=database/migrations/2025_06_14_231756_remove_columns_from_jornadas_formacion_table.php
php artisan migrate --path=database/migrations/2024_09_12_091351_create_caracterizacion_programas_table.php
php artisan migrate --path=database/migrations/2025_06_15_000043_drop_caracterizacion_programas_table.php
php artisan migrate --path=database/migrations/2025_06_14_175227_create_ambiente_instructor_ficha_table.php
php artisan migrate --path=database/migrations/2025_06_14_180217_create_ambiente_ficha_table.php

echo.
echo [BATCH 10] Competencias y Resultados de Aprendizaje
php artisan migrate --path=database/migrations/2025_06_17_233018_create_competencias_table.php
php artisan migrate --path=database/migrations/2025_10_11_000000_add_missing_fields_to_competencias_table.php
php artisan migrate --path=database/migrations/2025_06_17_234950_create_resultados_aprendizajes_table.php
php artisan migrate --path=database/migrations/2025_10_07_140000_add_status_and_indexes_to_resultados_aprendizajes_table.php
php artisan migrate --path=database/migrations/2024_03_19_195110_create_temas_table.php

echo.
echo [BATCH 11] Guias y Evidencias
php artisan migrate --path=database/migrations/2025_06_17_235644_create_guia_aprendizajes_table.php
php artisan migrate --path=database/migrations/2025_06_17_235645_add_missing_fields_to_guia_aprendizajes_table.php
php artisan migrate --path=database/migrations/2025_06_17_235646_add_es_obligatorio_to_guia_aprendizaje_rap_table.php
php artisan migrate --path=database/migrations/2025_06_18_000009_create_evidencias_table.php
php artisan migrate --path=database/migrations/2025_07_17_175044_add_column_id_evidencia.php
php artisan migrate --path=database/migrations/2025_07_25_091949_add_column_estado_fecha_evidencia.php

echo.
echo [BATCH 12] Asistencias y Entrada/Salida
php artisan migrate --path=database/migrations/2024_02_29_210346_create_entrada_salidas_table.php
php artisan migrate --path=database/migrations/2024_03_21_154757_add_column_to_entrada_salidas.php
php artisan migrate --path=database/migrations/2024_04_03_162543_add_column_to_table_entrada_salidas.php
php artisan migrate --path=database/migrations/2024_07_03_172007_add_column_to_entrada_salidas.php
php artisan migrate --path=database/migrations/2024_09_18_154335_create_asistencia_aprendices_table.php
php artisan migrate --path=database/migrations/2025_06_14_172054_remove_caracterizacion_from_asistencia_aprendices_table.php
php artisan migrate --path=database/migrations/2025_06_14_172251_add_instructor_ficha_to_asistencia_aprendices_table.php

echo.
echo ==================================================
echo Todas las migraciones completadas!
echo ==================================================

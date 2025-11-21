<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            // 2. Información laboral como instructor
            $table->string('tipo_vinculacion', 50)->nullable()->after('regional_id')->comment('Tipo de vinculación: planta, contratista, apoyo a la formación');
            $table->foreignId('centro_formacion_id')->nullable()->after('tipo_vinculacion')->constrained('centro_formacions')->onDelete('set null')->comment('Centro de formación asignado');
            $table->foreignId('jornada_trabajo_id')->nullable()->after('centro_formacion_id')->constrained('jornadas_formacion')->onDelete('set null')->comment('Jornada de trabajo');
            $table->integer('experiencia_instructor_meses')->nullable()->after('anos_experiencia')->comment('Experiencia como instructor en meses');
            $table->date('fecha_ingreso_sena')->nullable()->after('experiencia_instructor_meses')->comment('Fecha de ingreso al SENA');

            // 3. Formación académica
            $table->foreignId('nivel_academico_id')->nullable()->after('fecha_ingreso_sena')->constrained('parametros_temas')->onDelete('set null')->comment('Nivel académico más alto alcanzado (parametro_tema)');
            $table->json('titulos_obtenidos')->nullable()->after('nivel_academico_id')->comment('Títulos obtenidos');
            $table->json('instituciones_educativas')->nullable()->after('titulos_obtenidos')->comment('Instituciones educativas');
            $table->json('certificaciones_tecnicas')->nullable()->after('instituciones_educativas')->comment('Certificaciones técnicas o tecnológicas');
            $table->json('cursos_complementarios')->nullable()->after('certificaciones_tecnicas')->comment('Cursos complementarios relevantes');
            $table->text('formacion_pedagogia')->nullable()->after('cursos_complementarios')->comment('Formación en pedagogía (Diplomado en pedagogía SENA u otros equivalentes)');

            // 4. Competencias y habilidades
            $table->json('areas_experticia')->nullable()->after('formacion_pedagogia')->comment('Áreas de experticia');
            $table->json('competencias_tic')->nullable()->after('areas_experticia')->comment('Competencias TIC');
            $table->json('idiomas')->nullable()->after('competencias_tic')->comment('Idiomas y nivel');
            $table->json('habilidades_pedagogicas')->nullable()->after('idiomas')->comment('Habilidades pedagógicas (virtual, presencial, dual)');

            // 5. Documentos adjuntos
            $table->json('documentos_adjuntos')->nullable()->after('habilidades_pedagogicas')->comment('Documentos adjuntos (rutas de archivos)');

            // 6. Información administrativa (opcional)
            $table->string('numero_contrato', 100)->nullable()->after('documentos_adjuntos')->comment('Número de contrato');
            $table->date('fecha_inicio_contrato')->nullable()->after('numero_contrato')->comment('Fecha de inicio de contrato');
            $table->date('fecha_fin_contrato')->nullable()->after('fecha_inicio_contrato')->comment('Fecha de fin de contrato');
            $table->string('supervisor_contrato', 255)->nullable()->after('fecha_fin_contrato')->comment('Supervisor de contrato');
            $table->string('eps', 255)->nullable()->after('supervisor_contrato')->comment('EPS');
            $table->string('arl', 255)->nullable()->after('eps')->comment('ARL');

            // Índices adicionales
            $table->index('tipo_vinculacion', 'idx_instructors_tipo_vinculacion');
            $table->index('centro_formacion_id', 'idx_instructors_centro_formacion');
            $table->index('fecha_ingreso_sena', 'idx_instructors_fecha_ingreso');
            $table->index('nivel_academico_id', 'idx_instructors_nivel_academico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            // Eliminar índices primero
            $table->dropIndex('idx_instructors_tipo_vinculacion');
            $table->dropIndex('idx_instructors_centro_formacion');
            $table->dropIndex('idx_instructors_fecha_ingreso');
            $table->dropIndex('idx_instructors_nivel_academico');

            // Eliminar campos de información administrativa
            $table->dropColumn([
                'arl',
                'eps',
                'supervisor_contrato',
                'fecha_fin_contrato',
                'fecha_inicio_contrato',
                'numero_contrato'
            ]);

            // Eliminar campos de documentos adjuntos
            $table->dropColumn('documentos_adjuntos');

            // Eliminar campos de competencias y habilidades
            $table->dropColumn([
                'habilidades_pedagogicas',
                'idiomas',
                'competencias_tic',
                'areas_experticia'
            ]);

            // Eliminar campos de formación académica
            $table->dropColumn([
                'formacion_pedagogia',
                'cursos_complementarios',
                'certificaciones_tecnicas',
                'instituciones_educativas',
                'titulos_obtenidos',
                'nivel_academico_id'
            ]);

            // Eliminar campos de información laboral
            $table->dropForeign(['centro_formacion_id']);
            $table->dropForeign(['jornada_trabajo_id']);
            $table->dropForeign(['nivel_academico_id']);
            $table->dropColumn([
                'fecha_ingreso_sena',
                'experiencia_instructor_meses',
                'jornada_trabajo_id',
                'centro_formacion_id',
                'tipo_vinculacion'
            ]);
        });
    }
};

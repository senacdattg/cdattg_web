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
        Schema::table('guia_aprendizajes', function (Blueprint $table) {
            // Agregar columnas de auditoría si no existen
            if (!Schema::hasColumn('guia_aprendizajes', 'user_create_id')) {
                $table->foreignId('user_create_id')->nullable()->constrained('users')->after('nombre');
            }
            if (!Schema::hasColumn('guia_aprendizajes', 'user_edit_id')) {
                $table->foreignId('user_edit_id')->nullable()->constrained('users')->after('user_create_id');
            }
            
            // Agregar columnas de contenido
            $table->text('descripcion')->nullable()->after('user_edit_id');
            $table->integer('duracion_horas')->nullable()->after('descripcion');
            $table->enum('nivel_dificultad', ['BASICO', 'INTERMEDIO', 'AVANZADO'])->default('BASICO')->after('duracion_horas');
            $table->boolean('status')->default(true)->after('nivel_dificultad');
            
            // Agregar soft deletes
            $table->softDeletes()->after('updated_at');
            
            // Agregar índices para optimización
            $table->index(['codigo'], 'idx_guia_aprendizajes_codigo');
            $table->index(['nombre'], 'idx_guia_aprendizajes_nombre');
            $table->index(['status'], 'idx_guia_aprendizajes_status');
            $table->index(['nivel_dificultad'], 'idx_guia_aprendizajes_nivel_dificultad');
            $table->index(['user_create_id'], 'idx_guia_aprendizajes_user_create');
            $table->index(['created_at'], 'idx_guia_aprendizajes_created_at');
            
            // Índice compuesto para búsquedas frecuentes
            $table->index(['status', 'nivel_dificultad'], 'idx_guia_aprendizajes_status_nivel');
            $table->index(['user_create_id', 'status'], 'idx_guia_aprendizajes_user_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guia_aprendizajes', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex('idx_guia_aprendizajes_codigo');
            $table->dropIndex('idx_guia_aprendizajes_nombre');
            $table->dropIndex('idx_guia_aprendizajes_status');
            $table->dropIndex('idx_guia_aprendizajes_nivel_dificultad');
            $table->dropIndex('idx_guia_aprendizajes_user_create');
            $table->dropIndex('idx_guia_aprendizajes_created_at');
            $table->dropIndex('idx_guia_aprendizajes_status_nivel');
            $table->dropIndex('idx_guia_aprendizajes_user_status');
            
            // Eliminar columnas agregadas
            $table->dropColumn([
                'descripcion',
                'duracion_horas',
                'nivel_dificultad',
                'status',
                'deleted_at'
            ]);
            
            // Eliminar foreign keys si fueron agregadas
            if (Schema::hasColumn('guia_aprendizajes', 'user_create_id')) {
                $table->dropForeign(['user_create_id']);
                $table->dropColumn('user_create_id');
            }
            if (Schema::hasColumn('guia_aprendizajes', 'user_edit_id')) {
                $table->dropForeign(['user_edit_id']);
                $table->dropColumn('user_edit_id');
            }
        });
    }
};

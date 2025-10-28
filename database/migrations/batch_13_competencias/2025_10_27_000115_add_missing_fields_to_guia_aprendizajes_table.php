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
            // Agregar columnas de contenido (user_create_id y user_edit_id ya existen)
            if (!Schema::hasColumn('guia_aprendizajes', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('user_edit_id');
            }
            if (!Schema::hasColumn('guia_aprendizajes', 'duracion_horas')) {
                $table->integer('duracion_horas')->nullable()->after('descripcion');
            }
            if (!Schema::hasColumn('guia_aprendizajes', 'nivel_dificultad')) {
                $table->enum('nivel_dificultad', ['BASICO', 'INTERMEDIO', 'AVANZADO'])->default('BASICO')->after('duracion_horas');
            }
            if (!Schema::hasColumn('guia_aprendizajes', 'status')) {
                $table->boolean('status')->default(true)->after('nivel_dificultad');
            }
            
            // Agregar soft deletes si no existe
            if (!Schema::hasColumn('guia_aprendizajes', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
        
        // Agregar índices para optimización
        Schema::table('guia_aprendizajes', function (Blueprint $table) {
            try {
                $table->index(['codigo'], 'idx_guia_aprendizajes_codigo');
            } catch (\Exception $e) {
                // Índice ya existe
            }
            
            try {
                $table->index(['nombre'], 'idx_guia_aprendizajes_nombre');
            } catch (\Exception $e) {
                // Índice ya existe
            }
            
            try {
                $table->index(['status'], 'idx_guia_aprendizajes_status');
            } catch (\Exception $e) {
                // Índice ya existe
            }
            
            try {
                $table->index(['nivel_dificultad'], 'idx_guia_aprendizajes_nivel_dificultad');
            } catch (\Exception $e) {
                // Índice ya existe
            }
            
            try {
                $table->index(['user_create_id'], 'idx_guia_aprendizajes_user_create');
            } catch (\Exception $e) {
                // Índice ya existe
            }
            
            try {
                $table->index(['created_at'], 'idx_guia_aprendizajes_created_at');
            } catch (\Exception $e) {
                // Índice ya existe
            }
            
            try {
                $table->index(['status', 'nivel_dificultad'], 'idx_guia_aprendizajes_status_nivel');
            } catch (\Exception $e) {
                // Índice ya existe
            }
            
            try {
                $table->index(['user_create_id', 'status'], 'idx_guia_aprendizajes_user_status');
            } catch (\Exception $e) {
                // Índice ya existe
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guia_aprendizajes', function (Blueprint $table) {
            // Eliminar índices
            try { $table->dropIndex('idx_guia_aprendizajes_codigo'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_guia_aprendizajes_nombre'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_guia_aprendizajes_status'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_guia_aprendizajes_nivel_dificultad'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_guia_aprendizajes_user_create'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_guia_aprendizajes_created_at'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_guia_aprendizajes_status_nivel'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_guia_aprendizajes_user_status'); } catch (\Exception $e) {}
            
            // Eliminar columnas agregadas
            if (Schema::hasColumn('guia_aprendizajes', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
            if (Schema::hasColumn('guia_aprendizajes', 'duracion_horas')) {
                $table->dropColumn('duracion_horas');
            }
            if (Schema::hasColumn('guia_aprendizajes', 'nivel_dificultad')) {
                $table->dropColumn('nivel_dificultad');
            }
            if (Schema::hasColumn('guia_aprendizajes', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('guia_aprendizajes', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
};

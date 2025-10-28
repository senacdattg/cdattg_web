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
        Schema::table('programas_formacion', function (Blueprint $table) {
            // Agregar campos de auditoría
            $table->foreignId('user_create_id')->nullable()->after('nivel_formacion_id')->constrained('users')->onDelete('set null');
            $table->foreignId('user_edit_id')->nullable()->after('user_create_id')->constrained('users')->onDelete('set null');
            $table->boolean('status')->default(true)->after('user_edit_id');
            
            // Agregar columna deleted_at para soft deletes
            $table->softDeletes()->after('updated_at');
            
            // Verificar si las columnas timestamps existen, si no las agregamos
            if (!Schema::hasColumn('programas_formacion', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('programas_formacion', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
            
            // Agregar índices para optimizar búsquedas
            $table->index(['codigo'], 'idx_programas_formacion_codigo');
            $table->index(['nombre'], 'idx_programas_formacion_nombre');
            $table->index(['status'], 'idx_programas_formacion_status');
            $table->index(['red_conocimiento_id'], 'idx_programas_formacion_red_conocimiento');
            $table->index(['nivel_formacion_id'], 'idx_programas_formacion_nivel_formacion');
            $table->index(['user_create_id'], 'idx_programas_formacion_user_create');
            $table->index(['user_edit_id'], 'idx_programas_formacion_user_edit');
            
            // Índice compuesto para búsquedas frecuentes
            $table->index(['status', 'red_conocimiento_id'], 'idx_programas_formacion_status_red');
            $table->index(['status', 'nivel_formacion_id'], 'idx_programas_formacion_status_nivel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programas_formacion', function (Blueprint $table) {
            // Eliminar índices compuestos
            $table->dropIndex('idx_programas_formacion_status_red');
            $table->dropIndex('idx_programas_formacion_status_nivel');
            
            // Eliminar índices simples
            $table->dropIndex('idx_programas_formacion_codigo');
            $table->dropIndex('idx_programas_formacion_nombre');
            $table->dropIndex('idx_programas_formacion_status');
            $table->dropIndex('idx_programas_formacion_red_conocimiento');
            $table->dropIndex('idx_programas_formacion_nivel_formacion');
            $table->dropIndex('idx_programas_formacion_user_create');
            $table->dropIndex('idx_programas_formacion_user_edit');
            
            // Eliminar soft deletes
            $table->dropSoftDeletes();
            
            // Eliminar campos de auditoría
            $table->dropForeign(['user_create_id']);
            $table->dropColumn('user_create_id');
            
            $table->dropForeign(['user_edit_id']);
            $table->dropColumn('user_edit_id');
            
            $table->dropColumn('status');
        });
    }
};

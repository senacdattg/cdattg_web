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
        Schema::table('resultados_aprendizajes', function (Blueprint $table) {
            // Agregar campo status (1 = activo, 0 = inactivo)
            $table->boolean('status')->default(1)->after('user_edit_id');
            
            // Agregar índices para optimizar búsquedas
            $table->index('codigo', 'idx_rap_codigo');
            $table->index('status', 'idx_rap_status');
            $table->index(['codigo', 'status'], 'idx_rap_codigo_status');
            $table->index('created_at', 'idx_rap_created_at');
        });

        // Agregar índice en la tabla pivot para optimizar búsquedas por competencia
        Schema::table('resultados_aprendizaje_competencia', function (Blueprint $table) {
            $table->index('competencia_id', 'idx_rap_comp_competencia');
            $table->index('rap_id', 'idx_rap_comp_rap');
            $table->index(['competencia_id', 'rap_id'], 'idx_rap_comp_both');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resultados_aprendizajes', function (Blueprint $table) {
            // Eliminar índices primero
            $table->dropIndex('idx_rap_codigo');
            $table->dropIndex('idx_rap_status');
            $table->dropIndex('idx_rap_codigo_status');
            $table->dropIndex('idx_rap_created_at');
            
            // Eliminar columna status
            $table->dropColumn('status');
        });

        Schema::table('resultados_aprendizaje_competencia', function (Blueprint $table) {
            $table->dropIndex('idx_rap_comp_competencia');
            $table->dropIndex('idx_rap_comp_rap');
            $table->dropIndex('idx_rap_comp_both');
        });
    }
};


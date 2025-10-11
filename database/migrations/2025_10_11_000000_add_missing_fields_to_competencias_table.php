<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competencias', function (Blueprint $table) {
            // Verificar y agregar campo status si no existe
            if (!Schema::hasColumn('competencias', 'status')) {
                $table->boolean('status')->default(true)->after('fecha_fin');
            }

            // Agregar índices para optimizar búsquedas
            if (!Schema::hasIndex('competencias', ['codigo'])) {
                $table->index('codigo', 'idx_competencias_codigo');
            }
        });

        // Agregar índice en la tabla pivot competencia_programa
        Schema::table('competencia_programa', function (Blueprint $table) {
            if (!Schema::hasIndex('competencia_programa', ['programa_id'])) {
                $table->index('programa_id', 'idx_competencia_programa_programa_id');
            }
            
            if (!Schema::hasIndex('competencia_programa', ['competencia_id'])) {
                $table->index('competencia_id', 'idx_competencia_programa_competencia_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('competencias', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex('idx_competencias_codigo');
            
            // Eliminar campo status si existe
            if (Schema::hasColumn('competencias', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('competencia_programa', function (Blueprint $table) {
            $table->dropIndex('idx_competencia_programa_programa_id');
            $table->dropIndex('idx_competencia_programa_competencia_id');
        });
    }
};


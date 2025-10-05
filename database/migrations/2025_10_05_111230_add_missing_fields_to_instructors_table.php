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
            // Campos de auditoría
            $table->foreignId('user_create_id')->nullable()->constrained('users')->comment('Usuario que creó el registro');
            $table->foreignId('user_edit_id')->nullable()->constrained('users')->comment('Usuario que editó el registro');
            
            // Campo de estado
            $table->boolean('status')->default(true)->comment('Estado del instructor (activo/inactivo)');
            
            // Campos de especialidades y competencias
            $table->json('especialidades')->nullable()->comment('Especialidades del instructor');
            $table->json('competencias')->nullable()->comment('Competencias del instructor');
            
            // Campo de experiencia
            $table->integer('anos_experiencia')->nullable()->comment('Años de experiencia del instructor');
            $table->text('experiencia_laboral')->nullable()->comment('Experiencia laboral detallada');
            
            // Campos adicionales para optimización
            $table->string('numero_documento_cache')->nullable()->comment('Caché del número de documento para búsquedas rápidas');
            $table->string('nombre_completo_cache')->nullable()->comment('Caché del nombre completo para búsquedas rápidas');
            
            // Índices para optimizar consultas frecuentes
            $table->index('status', 'idx_instructors_status');
            $table->index('numero_documento_cache', 'idx_instructors_documento');
            $table->index('nombre_completo_cache', 'idx_instructors_nombre');
            $table->index(['regional_id', 'status'], 'idx_instructors_regional_status');
            $table->index('anos_experiencia', 'idx_instructors_experiencia');
            
            // Índice compuesto para búsquedas por especialidad
            $table->index(['status', 'anos_experiencia'], 'idx_instructors_status_experiencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            // Eliminar índices primero
            $table->dropIndex('idx_instructors_status');
            $table->dropIndex('idx_instructors_documento');
            $table->dropIndex('idx_instructors_nombre');
            $table->dropIndex('idx_instructors_regional_status');
            $table->dropIndex('idx_instructors_experiencia');
            $table->dropIndex('idx_instructors_status_experiencia');
            
            // Eliminar campos de caché
            $table->dropColumn('numero_documento_cache');
            $table->dropColumn('nombre_completo_cache');
            
            // Eliminar campos de experiencia
            $table->dropColumn('anos_experiencia');
            $table->dropColumn('experiencia_laboral');
            
            // Eliminar campos de especialidades y competencias
            $table->dropColumn('especialidades');
            $table->dropColumn('competencias');
            
            // Eliminar campo de estado
            $table->dropColumn('status');
            
            // Eliminar campos de auditoría
            $table->dropForeign(['user_create_id']);
            $table->dropColumn('user_create_id');
            $table->dropForeign(['user_edit_id']);
            $table->dropColumn('user_edit_id');
        });
    }
};

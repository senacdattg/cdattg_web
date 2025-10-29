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
        Schema::table('aprendices', function (Blueprint $table) {
            // Agregar ficha de caracterización principal
            $table->foreignId('ficha_caracterizacion_id')
                ->nullable()
                ->after('persona_id')
                ->constrained('fichas_caracterizacion')
                ->onDelete('set null');
            
            // Agregar estado (activo/inactivo)
            $table->boolean('estado')->default(1)->after('ficha_caracterizacion_id');
            
            // Agregar campos de auditoría
            $table->foreignId('user_create_id')
                ->nullable()
                ->after('estado')
                ->constrained('users')
                ->onDelete('set null');
            
            $table->foreignId('user_edit_id')
                ->nullable()
                ->after('user_create_id')
                ->constrained('users')
                ->onDelete('set null');
            
            // Agregar soft deletes
            $table->softDeletes();
            
            // Agregar índices para optimizar consultas
            $table->index('persona_id', 'aprendices_persona_id_index');
            $table->index('ficha_caracterizacion_id', 'aprendices_ficha_index');
            $table->index('estado', 'aprendices_estado_index');
            $table->index(['persona_id', 'estado'], 'aprendices_persona_estado_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aprendices', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex('aprendices_persona_id_index');
            $table->dropIndex('aprendices_ficha_index');
            $table->dropIndex('aprendices_estado_index');
            $table->dropIndex('aprendices_persona_estado_index');
            
            // Eliminar foreign keys
            $table->dropForeign(['ficha_caracterizacion_id']);
            $table->dropForeign(['user_create_id']);
            $table->dropForeign(['user_edit_id']);
            
            // Eliminar columnas
            $table->dropColumn([
                'ficha_caracterizacion_id',
                'estado',
                'user_create_id',
                'user_edit_id',
                'deleted_at'
            ]);
        });
    }
};
